<?php

declare(strict_types=1);

namespace App\Services\Cargo;

use App\Contracts\CargoProviderInterface;
use App\Models\CargoIntegration;
use App\Models\Shipment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ArasKargoProvider implements CargoProviderInterface
{
    protected string $baseUrl;

    protected string $username;

    protected string $password;

    protected string $customerCode;

    protected bool $testMode;

    protected int $timeout;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(array $config = [])
    {
        $config = array_merge(config('cargo.providers.aras', []), $config);

        $this->baseUrl = $config['base_url'] ?? 'https://customerservices.araskargo.com.tr';
        $this->username = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->customerCode = $config['customer_code'] ?? '';
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

            $xml = $this->buildSetOrderXml([
                'tracking_number' => $shipment->tracking_number,
                'receiver_name' => $address->full_name ?? $order?->user?->name ?? 'Alıcı',
                'receiver_phone' => $address->phone ?? $order?->user?->phone ?? '',
                'receiver_address' => $address->full_address ?? $address->address_line,
                'receiver_city' => $address->city?->name ?? $address->city_name ?? '',
                'receiver_district' => $address->district?->name ?? $address->district_name ?? '',
                'weight' => $shipment->weight ?? 1,
                'desi' => $shipment->desi ?? 1,
                'piece_count' => $shipment->piece_count ?? 1,
                'product_name' => $orderItem?->product_name ?? 'Ürün',
                'order_number' => $order?->order_number ?? $shipment->id,
            ], $credentials);

            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'text/xml; charset=utf-8'])
                ->send('POST', $credentials['base_url'] . '/ArasCargoIntegrationService/ArasCargoIntegrationService.svc', [
                    'body' => $xml,
                ]);

            $result = $this->parseResponse($response->body());

            if ($result['success']) {
                return [
                    'success' => true,
                    'tracking_number' => $result['tracking_number'] ?? $shipment->tracking_number,
                    'cargo_reference_id' => $result['reference_id'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => $result['error'] ?? 'Kargo oluşturulamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('Aras createShipment failed', [
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
                ->send('POST', $credentials['base_url'] . '/ArasCargoIntegrationService/ArasCargoIntegrationService.svc', [
                    'body' => $xml,
                ]);

            return $this->parseTrackingResponse($response->body());
        } catch (\Throwable $e) {
            Log::error('Aras trackShipment failed', [
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
                ->send('POST', $credentials['base_url'] . '/ArasCargoIntegrationService/ArasCargoIntegrationService.svc', [
                    'body' => $xml,
                ]);

            $result = $this->parseResponse($response->body());

            return [
                'success' => $result['success'],
                'error' => $result['error'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('Aras cancelShipment failed', [
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
                ->send('POST', $credentials['base_url'] . '/ArasCargoIntegrationService/ArasCargoIntegrationService.svc', [
                    'body' => $xml,
                ]);

            $result = $this->parseLabelResponse($response->body());

            return $result;
        } catch (\Throwable $e) {
            Log::error('Aras getLabel failed', [
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
        // Aras Kargo fiyat sorgulama API'si farklı bir endpoint kullanır
        // Şimdilik statik değer döndürüyoruz
        $effectiveWeight = max($weight, $desi);
        $basePrice = 30.0;
        $perKgPrice = 5.0;

        return [
            'success' => true,
            'price' => $basePrice + ($effectiveWeight * $perKgPrice),
            'currency' => 'TRY',
        ];
    }

    public function getProviderCode(): string
    {
        return 'aras';
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
            'customer_code' => $apiCredentials['customer_code'] ?? $this->customerCode,
        ];
    }

    /**
     * SetOrder XML'i oluştur
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $credentials
     */
    protected function buildSetOrderXml(array $data, array $credentials): string
    {
        return <<<XML
        <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:ars="http://tempuri.org/">
            <soap:Header/>
            <soap:Body>
                <ars:SetOrder>
                    <ars:loginInfo>
                        <ars:UserName>{$credentials['username']}</ars:UserName>
                        <ars:Password>{$credentials['password']}</ars:Password>
                        <ars:CustomerCode>{$credentials['customer_code']}</ars:CustomerCode>
                    </ars:loginInfo>
                    <ars:orderInfo>
                        <ars:TradingWaybillNumber>{$data['tracking_number']}</ars:TradingWaybillNumber>
                        <ars:InvoiceNumber>{$data['order_number']}</ars:InvoiceNumber>
                        <ars:ReceiverName>{$data['receiver_name']}</ars:ReceiverName>
                        <ars:ReceiverPhone>{$data['receiver_phone']}</ars:ReceiverPhone>
                        <ars:ReceiverAddress>{$data['receiver_address']}</ars:ReceiverAddress>
                        <ars:ReceiverCityName>{$data['receiver_city']}</ars:ReceiverCityName>
                        <ars:ReceiverTownName>{$data['receiver_district']}</ars:ReceiverTownName>
                        <ars:PieceCount>{$data['piece_count']}</ars:PieceCount>
                        <ars:Weight>{$data['weight']}</ars:Weight>
                        <ars:VolumetricWeight>{$data['desi']}</ars:VolumetricWeight>
                        <ars:Description>{$data['product_name']}</ars:Description>
                        <ars:PayorTypeCode>1</ars:PayorTypeCode>
                    </ars:orderInfo>
                </ars:SetOrder>
            </soap:Body>
        </soap:Envelope>
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
        <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:ars="http://tempuri.org/">
            <soap:Header/>
            <soap:Body>
                <ars:GetOrderWithQueryXml>
                    <ars:loginInfo>
                        <ars:UserName>{$credentials['username']}</ars:UserName>
                        <ars:Password>{$credentials['password']}</ars:Password>
                        <ars:CustomerCode>{$credentials['customer_code']}</ars:CustomerCode>
                    </ars:loginInfo>
                    <ars:queryInfo>
                        <ars:QueryType>1</ars:QueryType>
                        <ars:IntegrationCode>{$trackingNumber}</ars:IntegrationCode>
                    </ars:queryInfo>
                </ars:GetOrderWithQueryXml>
            </soap:Body>
        </soap:Envelope>
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
        <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:ars="http://tempuri.org/">
            <soap:Header/>
            <soap:Body>
                <ars:CancelDispatch>
                    <ars:loginInfo>
                        <ars:UserName>{$credentials['username']}</ars:UserName>
                        <ars:Password>{$credentials['password']}</ars:Password>
                        <ars:CustomerCode>{$credentials['customer_code']}</ars:CustomerCode>
                    </ars:loginInfo>
                    <ars:integrationCode>{$trackingNumber}</ars:integrationCode>
                </ars:CancelDispatch>
            </soap:Body>
        </soap:Envelope>
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
        <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:ars="http://tempuri.org/">
            <soap:Header/>
            <soap:Body>
                <ars:GetBarcodeBase64>
                    <ars:loginInfo>
                        <ars:UserName>{$credentials['username']}</ars:UserName>
                        <ars:Password>{$credentials['password']}</ars:Password>
                        <ars:CustomerCode>{$credentials['customer_code']}</ars:CustomerCode>
                    </ars:loginInfo>
                    <ars:integrationCode>{$trackingNumber}</ars:integrationCode>
                </ars:GetBarcodeBase64>
            </soap:Body>
        </soap:Envelope>
        XML;
    }

    /**
     * SOAP yanıtını parse et
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

            // ResultCode kontrolü
            $resultCode = $array['s_Body']['SetOrderResponse']['SetOrderResult']['ResultCode'] ?? null;

            if ($resultCode === '0' || $resultCode === 0) {
                return [
                    'success' => true,
                    'tracking_number' => $array['s_Body']['SetOrderResponse']['SetOrderResult']['TrackingNumber'] ?? null,
                    'reference_id' => $array['s_Body']['SetOrderResponse']['SetOrderResult']['ReferenceId'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => $array['s_Body']['SetOrderResponse']['SetOrderResult']['ResultMessage'] ?? 'Bilinmeyen hata',
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

            // Parse events from response
            $shippingDetails = $array['s_Body']['GetOrderWithQueryXmlResponse']['GetOrderWithQueryXmlResult']['ShippingDeliveryDetailVO'] ?? [];

            if (! empty($shippingDetails)) {
                foreach ((array) $shippingDetails as $detail) {
                    $events[] = [
                        'date' => $detail['EventDate'] ?? now()->toIso8601String(),
                        'status' => $detail['EventCode'] ?? '',
                        'description' => $detail['EventCodeDescription'] ?? '',
                        'location' => $detail['UnitName'] ?? '',
                    ];
                }

                // Son event'ten durumu al
                $lastEvent = end($events);
                $status = $this->normalizeStatus($lastEvent['status'] ?? '');
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

            $base64 = $array['s_Body']['GetBarcodeBase64Response']['GetBarcodeBase64Result']['BarcodeBase64'] ?? null;

            if ($base64) {
                return [
                    'success' => true,
                    'label_content' => $base64,
                    'barcode_url' => 'data:image/png;base64,' . $base64,
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
