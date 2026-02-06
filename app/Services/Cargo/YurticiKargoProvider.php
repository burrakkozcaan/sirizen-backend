<?php

declare(strict_types=1);

namespace App\Services\Cargo;

use App\Contracts\CargoProviderInterface;
use App\Models\CargoIntegration;
use App\Models\Shipment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YurticiKargoProvider implements CargoProviderInterface
{
    protected string $baseUrl;

    protected string $username;

    protected string $password;

    protected string $userLanguage;

    protected bool $testMode;

    protected int $timeout;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(array $config = [])
    {
        $config = array_merge(config('cargo.providers.yurtici', []), $config);

        $this->baseUrl = $config['base_url'] ?? 'https://ws.yurticikargo.com';
        $this->username = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->userLanguage = $config['user_language'] ?? 'TR';
        $this->testMode = $config['test_mode'] ?? true;
        $this->timeout = $config['timeout'] ?? 30;
    }

    /**
     * Kargo gönderisi oluştur
     *
     * @return array{success: bool, tracking_number?: string, cargo_reference_id?: string, label_url?: string, barcode_url?: string, error?: string}
     */
    public function createShipment(Shipment $shipment, CargoIntegration $integration): array
    {
        try {
            $orderItem = $shipment->orderItem;
            $order = $orderItem?->order;
            $address = $order?->shippingAddress ?? $order?->user?->addresses()->where('is_default', true)->first();

            if (! $address) {
                return ['success' => false, 'error' => 'Teslimat adresi bulunamadı'];
            }

            $credentials = $this->getCredentials($integration);

            $xml = $this->buildCreateShipmentXml([
                'invoice_key' => $order?->order_number ?? $shipment->id,
                'receiver_name' => $address->full_name ?? $order?->user?->name ?? 'Alıcı',
                'receiver_phone' => $address->phone ?? $order?->user?->phone ?? '',
                'receiver_address' => $address->full_address ?? $address->address_line,
                'receiver_city_code' => $address->city?->plate_code ?? '',
                'receiver_city' => $address->city?->name ?? $address->city_name ?? '',
                'receiver_district' => $address->district?->name ?? $address->district_name ?? '',
                'weight' => $shipment->weight ?? 1,
                'desi' => $shipment->desi ?? 1,
                'piece_count' => $shipment->piece_count ?? 1,
                'product_name' => $orderItem?->product_name ?? 'Ürün',
            ], $credentials);

            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'text/xml; charset=utf-8'])
                ->send('POST', $credentials['base_url'] . '/KOPSWebServices/ShippingOrderDispatcherServices', [
                    'body' => $xml,
                ]);

            $result = $this->parseCreateResponse($response->body());

            return $result;
        } catch (\Throwable $e) {
            Log::error('Yurtici createShipment failed', [
                'shipment_id' => $shipment->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Kargo takip sorgula
     *
     * @return array{success: bool, status?: string, events?: array<int, array{date: string, status: string, location?: string, description?: string}>, error?: string}
     */
    public function trackShipment(string $trackingNumber, CargoIntegration $integration): array
    {
        try {
            $credentials = $this->getCredentials($integration);

            $xml = $this->buildQueryXml($trackingNumber, $credentials);

            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'text/xml; charset=utf-8'])
                ->send('POST', $credentials['base_url'] . '/KOPSWebServices/ShippingOrderDispatcherServices', [
                    'body' => $xml,
                ]);

            return $this->parseTrackingResponse($response->body());
        } catch (\Throwable $e) {
            Log::error('Yurtici trackShipment failed', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Kargo iptal
     *
     * @return array{success: bool, error?: string}
     */
    public function cancelShipment(string $trackingNumber, CargoIntegration $integration): array
    {
        try {
            $credentials = $this->getCredentials($integration);

            $xml = $this->buildCancelXml($trackingNumber, $credentials);

            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'text/xml; charset=utf-8'])
                ->send('POST', $credentials['base_url'] . '/KOPSWebServices/ShippingOrderDispatcherServices', [
                    'body' => $xml,
                ]);

            $result = $this->parseResponse($response->body());

            return [
                'success' => $result['success'],
                'error' => $result['error'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('Yurtici cancelShipment failed', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Barkod/etiket al
     *
     * @return array{success: bool, label_url?: string, label_content?: string, barcode_url?: string, error?: string}
     */
    public function getLabel(string $trackingNumber, CargoIntegration $integration): array
    {
        try {
            $credentials = $this->getCredentials($integration);

            $xml = $this->buildLabelXml($trackingNumber, $credentials);

            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'text/xml; charset=utf-8'])
                ->send('POST', $credentials['base_url'] . '/KOPSWebServices/ShippingOrderDispatcherServices', [
                    'body' => $xml,
                ]);

            return $this->parseLabelResponse($response->body());
        } catch (\Throwable $e) {
            Log::error('Yurtici getLabel failed', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Kargo ücreti hesapla
     *
     * @return array{success: bool, price?: float, currency?: string, error?: string}
     */
    public function calculateRate(float $weight, float $desi, CargoIntegration $integration): array
    {
        $effectiveWeight = max($weight, $desi);
        $basePrice = 32.0;
        $perKgPrice = 4.5;

        return [
            'success' => true,
            'price' => $basePrice + ($effectiveWeight * $perKgPrice),
            'currency' => 'TRY',
        ];
    }

    public function getProviderCode(): string
    {
        return 'yurtici';
    }

    /**
     * Entegrasyondan credential'ları al
     *
     * @return array<string, mixed>
     */
    protected function getCredentials(CargoIntegration $integration): array
    {
        $apiCredentials = $integration->api_credentials ?? [];

        return [
            'base_url' => $apiCredentials['base_url'] ?? $this->baseUrl,
            'username' => $apiCredentials['username'] ?? $this->username,
            'password' => $apiCredentials['password'] ?? $this->password,
            'user_language' => $apiCredentials['user_language'] ?? $this->userLanguage,
        ];
    }

    /**
     * CreateShipment XML'i oluştur
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $credentials
     */
    protected function buildCreateShipmentXml(array $data, array $credentials): string
    {
        return <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ship="http://yurticikargo.com/ShippingOrderDispatcher/">
            <soapenv:Header/>
            <soapenv:Body>
                <ship:createShipment>
                    <ship:wsUserName>{$credentials['username']}</ship:wsUserName>
                    <ship:wsPassword>{$credentials['password']}</ship:wsPassword>
                    <ship:wsLanguage>{$credentials['user_language']}</ship:wsLanguage>
                    <ship:ShippingOrderVO>
                        <ship:cargoKey>{$data['invoice_key']}</ship:cargoKey>
                        <ship:invoiceKey>{$data['invoice_key']}</ship:invoiceKey>
                        <ship:receiverCustName>{$data['receiver_name']}</ship:receiverCustName>
                        <ship:receiverPhone>{$data['receiver_phone']}</ship:receiverPhone>
                        <ship:receiverAddress>{$data['receiver_address']}</ship:receiverAddress>
                        <ship:cityCode>{$data['receiver_city_code']}</ship:cityCode>
                        <ship:townName>{$data['receiver_district']}</ship:townName>
                        <ship:desi>{$data['desi']}</ship:desi>
                        <ship:kg>{$data['weight']}</ship:kg>
                        <ship:cargoCount>{$data['piece_count']}</ship:cargoCount>
                        <ship:description>{$data['product_name']}</ship:description>
                        <ship:payorTypeCode>1</ship:payorTypeCode>
                    </ship:ShippingOrderVO>
                </ship:createShipment>
            </soapenv:Body>
        </soapenv:Envelope>
        XML;
    }

    /**
     * Query XML'i oluştur
     *
     * @param  array<string, mixed>  $credentials
     */
    protected function buildQueryXml(string $trackingNumber, array $credentials): string
    {
        return <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ship="http://yurticikargo.com/ShippingOrderDispatcher/">
            <soapenv:Header/>
            <soapenv:Body>
                <ship:queryShipment>
                    <ship:wsUserName>{$credentials['username']}</ship:wsUserName>
                    <ship:wsPassword>{$credentials['password']}</ship:wsPassword>
                    <ship:wsLanguage>{$credentials['user_language']}</ship:wsLanguage>
                    <ship:keys>{$trackingNumber}</ship:keys>
                    <ship:keyType>0</ship:keyType>
                    <ship:addHistoricalData>true</ship:addHistoricalData>
                </ship:queryShipment>
            </soapenv:Body>
        </soapenv:Envelope>
        XML;
    }

    /**
     * Cancel XML'i oluştur
     *
     * @param  array<string, mixed>  $credentials
     */
    protected function buildCancelXml(string $trackingNumber, array $credentials): string
    {
        return <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ship="http://yurticikargo.com/ShippingOrderDispatcher/">
            <soapenv:Header/>
            <soapenv:Body>
                <ship:cancelShipment>
                    <ship:wsUserName>{$credentials['username']}</ship:wsUserName>
                    <ship:wsPassword>{$credentials['password']}</ship:wsPassword>
                    <ship:wsLanguage>{$credentials['user_language']}</ship:wsLanguage>
                    <ship:cargoKeys>{$trackingNumber}</ship:cargoKeys>
                </ship:cancelShipment>
            </soapenv:Body>
        </soapenv:Envelope>
        XML;
    }

    /**
     * Label XML'i oluştur
     *
     * @param  array<string, mixed>  $credentials
     */
    protected function buildLabelXml(string $trackingNumber, array $credentials): string
    {
        return <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ship="http://yurticikargo.com/ShippingOrderDispatcher/">
            <soapenv:Header/>
            <soapenv:Body>
                <ship:getLabelByCargoKey>
                    <ship:wsUserName>{$credentials['username']}</ship:wsUserName>
                    <ship:wsPassword>{$credentials['password']}</ship:wsPassword>
                    <ship:wsLanguage>{$credentials['user_language']}</ship:wsLanguage>
                    <ship:cargoKey>{$trackingNumber}</ship:cargoKey>
                </ship:getLabelByCargoKey>
            </soapenv:Body>
        </soapenv:Envelope>
        XML;
    }

    /**
     * Create yanıtını parse et
     *
     * @return array<string, mixed>
     */
    protected function parseCreateResponse(string $xml): array
    {
        try {
            $xml = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2_$3', $xml);
            $parsed = simplexml_load_string($xml);

            if ($parsed === false) {
                return ['success' => false, 'error' => 'XML parse hatası'];
            }

            $json = json_encode($parsed);
            $array = json_decode($json, true);

            $result = $array['soap_Body']['createShipmentResponse']['ShippingOrderResultVO'] ?? null;

            if ($result && ($result['outFlag'] ?? '') === '0') {
                return [
                    'success' => true,
                    'tracking_number' => $result['shippingOrderDetailVO']['cargoKey'] ?? null,
                    'cargo_reference_id' => $result['jobId'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => $result['errMessage'] ?? 'Kargo oluşturulamadı',
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Yanıtı parse et
     *
     * @return array<string, mixed>
     */
    protected function parseResponse(string $xml): array
    {
        try {
            $xml = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2_$3', $xml);
            $parsed = simplexml_load_string($xml);

            if ($parsed === false) {
                return ['success' => false, 'error' => 'XML parse hatası'];
            }

            $json = json_encode($parsed);
            $array = json_decode($json, true);

            // Generic success check
            $outFlag = $array['soap_Body']['cancelShipmentResponse']['outFlag'] ?? null;

            return [
                'success' => $outFlag === '0',
                'error' => $array['soap_Body']['cancelShipmentResponse']['errMessage'] ?? null,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Takip yanıtını parse et
     *
     * @return array<string, mixed>
     */
    protected function parseTrackingResponse(string $xml): array
    {
        try {
            $xml = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2_$3', $xml);
            $parsed = simplexml_load_string($xml);

            if ($parsed === false) {
                return ['success' => false, 'error' => 'XML parse hatası'];
            }

            $json = json_encode($parsed);
            $array = json_decode($json, true);

            $events = [];
            $status = 'unknown';

            $shipmentData = $array['soap_Body']['queryShipmentResponse']['ShippingDeliveryVO'] ?? null;

            if ($shipmentData) {
                $historyData = $shipmentData['shippingDeliveryDetailVO'] ?? [];

                if (! is_array($historyData)) {
                    $historyData = [$historyData];
                }

                foreach ($historyData as $detail) {
                    $events[] = [
                        'date' => $detail['operationDate'] ?? now()->toIso8601String(),
                        'status' => $detail['operationCode'] ?? '',
                        'description' => $detail['operationMessage'] ?? '',
                        'location' => $detail['unitName'] ?? '',
                    ];
                }

                $operationCode = $shipmentData['operationCode'] ?? '';
                $status = $this->normalizeStatus($operationCode);
            }

            return [
                'success' => true,
                'status' => $status,
                'events' => $events,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Label yanıtını parse et
     *
     * @return array<string, mixed>
     */
    protected function parseLabelResponse(string $xml): array
    {
        try {
            $xml = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2_$3', $xml);
            $parsed = simplexml_load_string($xml);

            if ($parsed === false) {
                return ['success' => false, 'error' => 'XML parse hatası'];
            }

            $json = json_encode($parsed);
            $array = json_decode($json, true);

            $labelData = $array['soap_Body']['getLabelByCargoKeyResponse']['labelData'] ?? null;

            if ($labelData) {
                return [
                    'success' => true,
                    'label_content' => $labelData,
                    'barcode_url' => 'data:application/pdf;base64,' . $labelData,
                ];
            }

            return ['success' => false, 'error' => 'Etiket alınamadı'];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Durumu normalize et
     */
    protected function normalizeStatus(string $status): string
    {
        $statusMap = [
            '1' => 'picked_up',
            '2' => 'in_transit',
            '3' => 'out_for_delivery',
            '4' => 'delivered',
            '5' => 'returned',
            '6' => 'cancelled',
        ];

        return $statusMap[$status] ?? 'in_transit';
    }
}
