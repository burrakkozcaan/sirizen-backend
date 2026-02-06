<?php

declare(strict_types=1);

namespace App\Services\Cargo;

use App\Contracts\CargoProviderInterface;
use App\Models\CargoIntegration;
use App\Models\Shipment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MngKargoProvider implements CargoProviderInterface
{
    protected string $baseUrl;

    protected string $apiKey;

    protected string $apiSecret;

    protected string $customerNumber;

    protected bool $testMode;

    protected int $timeout;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(array $config = [])
    {
        $config = array_merge(config('cargo.providers.mng', []), $config);

        $this->baseUrl = $config['base_url'] ?? 'https://api.mngkargo.com.tr';
        $this->apiKey = $config['api_key'] ?? '';
        $this->apiSecret = $config['api_secret'] ?? '';
        $this->customerNumber = $config['customer_number'] ?? '';
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

            $payload = [
                'order' => [
                    'referenceId' => $order?->order_number ?? (string) $shipment->id,
                    'barcode' => $shipment->tracking_number,
                    'billOfLandingId' => $order?->order_number,
                    'isCOD' => false,
                    'codAmount' => 0,
                    'packagingType' => 1,
                    'content' => $orderItem?->product_name ?? 'Ürün',
                    'smsNotification1' => true,
                    'smsNotification2' => true,
                    'paymentType' => 1,
                    'deliveryType' => 1,
                    'description' => $orderItem?->product_name ?? 'Ürün',
                    'marketPlaceShortCode' => '',
                    'marketPlaceSaleCode' => $order?->order_number ?? '',
                ],
                'orderPieceList' => [
                    [
                        'barcode' => $shipment->tracking_number . '-1',
                        'desi' => $shipment->desi ?? 1,
                        'kg' => $shipment->weight ?? 1,
                        'content' => $orderItem?->product_name ?? 'Ürün',
                    ],
                ],
                'recipient' => [
                    'customerId' => (string) ($order?->user_id ?? $shipment->id),
                    'refCustomerId' => '',
                    'cityCode' => $address->city?->plate_code ?? 34,
                    'cityName' => $address->city?->name ?? $address->city_name ?? 'İstanbul',
                    'districtName' => $address->district?->name ?? $address->district_name ?? '',
                    'address' => $address->full_address ?? $address->address_line,
                    'bussinessPhoneNumber' => '',
                    'email' => $order?->user?->email ?? '',
                    'taxOffice' => '',
                    'taxNumber' => '',
                    'fullName' => $address->full_name ?? $order?->user?->name ?? 'Alıcı',
                    'homePhoneNumber' => '',
                    'mobilePhoneNumber' => $address->phone ?? $order?->user?->phone ?? '',
                ],
            ];

            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders($credentials))
                ->post($credentials['base_url'] . '/mngapi/api/standardcmdapi/createOrder', $payload);

            $result = $response->json();

            if ($response->successful() && isset($result['data']['barcode'])) {
                return [
                    'success' => true,
                    'tracking_number' => $result['data']['barcode'],
                    'cargo_reference_id' => $result['data']['referenceId'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => $result['message'] ?? 'Kargo oluşturulamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('MNG createShipment failed', [
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

            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders($credentials))
                ->get($credentials['base_url'] . '/mngapi/api/standardqueryapi/getShipmentStatus', [
                    'barcode' => $trackingNumber,
                ]);

            $result = $response->json();

            if ($response->successful() && isset($result['data'])) {
                $events = [];
                $status = 'unknown';

                if (isset($result['data']['statusHistory'])) {
                    foreach ($result['data']['statusHistory'] as $history) {
                        $events[] = [
                            'date' => $history['date'] ?? now()->toIso8601String(),
                            'status' => $history['statusCode'] ?? '',
                            'description' => $history['statusDescription'] ?? '',
                            'location' => $history['location'] ?? '',
                        ];
                    }
                }

                $status = $this->normalizeStatus($result['data']['statusCode'] ?? '');

                return [
                    'success' => true,
                    'status' => $status,
                    'events' => $events,
                ];
            }

            return [
                'success' => false,
                'error' => $result['message'] ?? 'Takip bilgisi alınamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('MNG trackShipment failed', [
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

            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders($credentials))
                ->post($credentials['base_url'] . '/mngapi/api/standardcmdapi/cancelOrder', [
                    'barcode' => $trackingNumber,
                ]);

            $result = $response->json();

            if ($response->successful() && ($result['success'] ?? false)) {
                return ['success' => true];
            }

            return [
                'success' => false,
                'error' => $result['message'] ?? 'Kargo iptal edilemedi',
            ];
        } catch (\Throwable $e) {
            Log::error('MNG cancelShipment failed', [
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

            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders($credentials))
                ->get($credentials['base_url'] . '/mngapi/api/standardqueryapi/getLabel', [
                    'barcode' => $trackingNumber,
                    'type' => 'PDF',
                ]);

            $result = $response->json();

            if ($response->successful() && isset($result['data']['labelData'])) {
                return [
                    'success' => true,
                    'label_content' => $result['data']['labelData'],
                    'barcode_url' => 'data:application/pdf;base64,' . $result['data']['labelData'],
                ];
            }

            return [
                'success' => false,
                'error' => $result['message'] ?? 'Etiket alınamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('MNG getLabel failed', [
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
        $basePrice = 28.0;
        $perKgPrice = 4.0;

        return [
            'success' => true,
            'price' => $basePrice + ($effectiveWeight * $perKgPrice),
            'currency' => 'TRY',
        ];
    }

    public function getProviderCode(): string
    {
        return 'mng';
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
            'api_key' => $apiCredentials['api_key'] ?? $this->apiKey,
            'api_secret' => $apiCredentials['api_secret'] ?? $this->apiSecret,
            'customer_number' => $apiCredentials['customer_number'] ?? $this->customerNumber,
        ];
    }

    /**
     * API header'larını oluştur
     *
     * @param  array<string, mixed>  $credentials
     * @return array<string, string>
     */
    protected function getHeaders(array $credentials): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-IBM-Client-Id' => $credentials['api_key'],
            'X-IBM-Client-Secret' => $credentials['api_secret'],
            'X-Mng-Kargo-CustomerNumber' => $credentials['customer_number'],
        ];
    }

    /**
     * Durumu normalize et
     */
    protected function normalizeStatus(string $status): string
    {
        $statusMap = [
            'CREATED' => 'pending',
            'PICKED_UP' => 'picked_up',
            'IN_TRANSIT' => 'in_transit',
            'OUT_FOR_DELIVERY' => 'out_for_delivery',
            'DELIVERED' => 'delivered',
            'RETURNED' => 'returned',
            'CANCELLED' => 'cancelled',
        ];

        return $statusMap[strtoupper($status)] ?? 'in_transit';
    }
}
