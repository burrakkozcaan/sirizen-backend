<?php

declare(strict_types=1);

namespace App\Services\Cargo;

use App\CargoProvider;
use App\Events\ShipmentStatusUpdated;
use App\Models\CargoIntegration;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CargoService
{
    public function __construct(
        protected CargoProviderFactory $providerFactory
    ) {}

    /**
     * Shipment için kargo oluştur
     *
     * @return array{success: bool, tracking_number?: string, error?: string}
     */
    public function createShipment(Shipment $shipment): array
    {
        $integration = $this->getIntegration($shipment);

        if (! $integration) {
            return ['success' => false, 'error' => 'Kargo entegrasyonu bulunamadı'];
        }

        try {
            $providerCode = $integration->shipping_company_code ?? config('cargo.default', 'aras');
            $provider = $this->providerFactory->make($providerCode);

            // Takip numarası yoksa oluştur
            if (! $shipment->tracking_number) {
                $shipment->update([
                    'tracking_number' => $this->generateTrackingNumber($providerCode),
                ]);
            }

            $result = $provider->createShipment($shipment, $integration);

            if ($result['success']) {
                $shipment->update([
                    'tracking_number' => $result['tracking_number'] ?? $shipment->tracking_number,
                    'cargo_reference_id' => $result['cargo_reference_id'] ?? null,
                    'label_url' => $result['label_url'] ?? null,
                    'barcode_url' => $result['barcode_url'] ?? null,
                    'status' => 'created',
                    'shipped_at' => now(),
                ]);

                // İlk event'i kaydet
                ShipmentEvent::create([
                    'shipment_id' => $shipment->id,
                    'status' => 'created',
                    'description' => 'Kargo oluşturuldu',
                    'occurred_at' => now(),
                ]);
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('CargoService createShipment failed', [
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
    public function trackShipment(Shipment $shipment): array
    {
        $integration = $this->getIntegration($shipment);

        if (! $integration) {
            return ['success' => false, 'error' => 'Kargo entegrasyonu bulunamadı'];
        }

        if (! $shipment->tracking_number) {
            return ['success' => false, 'error' => 'Takip numarası bulunamadı'];
        }

        try {
            $providerCode = $integration->shipping_company_code ?? config('cargo.default', 'aras');
            $provider = $this->providerFactory->make($providerCode);

            $result = $provider->trackShipment($shipment->tracking_number, $integration);

            if ($result['success']) {
                $previousStatus = $shipment->status;
                $newStatus = $result['status'] ?? $shipment->status;

                // Shipment durumunu güncelle
                $shipment->update([
                    'status' => $newStatus,
                    'last_tracking_update' => now(),
                ]);

                // Yeni event'leri kaydet
                $this->saveNewEvents($shipment, $result['events'] ?? []);

                // Durum değiştiyse event tetikle
                if ($previousStatus !== $newStatus) {
                    event(new ShipmentStatusUpdated($shipment, $previousStatus, $newStatus));
                }
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('CargoService trackShipment failed', [
                'shipment_id' => $shipment->id,
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
    public function cancelShipment(Shipment $shipment): array
    {
        $integration = $this->getIntegration($shipment);

        if (! $integration) {
            return ['success' => false, 'error' => 'Kargo entegrasyonu bulunamadı'];
        }

        if (! $shipment->tracking_number) {
            return ['success' => false, 'error' => 'Takip numarası bulunamadı'];
        }

        try {
            $providerCode = $integration->shipping_company_code ?? config('cargo.default', 'aras');
            $provider = $this->providerFactory->make($providerCode);

            $result = $provider->cancelShipment($shipment->tracking_number, $integration);

            if ($result['success']) {
                $previousStatus = $shipment->status;

                $shipment->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                ]);

                // İptal event'i kaydet
                ShipmentEvent::create([
                    'shipment_id' => $shipment->id,
                    'status' => 'cancelled',
                    'description' => 'Kargo iptal edildi',
                    'occurred_at' => now(),
                ]);

                event(new ShipmentStatusUpdated($shipment, $previousStatus, 'cancelled'));
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('CargoService cancelShipment failed', [
                'shipment_id' => $shipment->id,
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
    public function getLabel(Shipment $shipment): array
    {
        $integration = $this->getIntegration($shipment);

        if (! $integration) {
            return ['success' => false, 'error' => 'Kargo entegrasyonu bulunamadı'];
        }

        if (! $shipment->tracking_number) {
            return ['success' => false, 'error' => 'Takip numarası bulunamadı'];
        }

        try {
            $providerCode = $integration->shipping_company_code ?? config('cargo.default', 'aras');
            $provider = $this->providerFactory->make($providerCode);

            $result = $provider->getLabel($shipment->tracking_number, $integration);

            if ($result['success']) {
                $shipment->update([
                    'label_url' => $result['label_url'] ?? $shipment->label_url,
                    'barcode_url' => $result['barcode_url'] ?? $shipment->barcode_url,
                ]);
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('CargoService getLabel failed', [
                'shipment_id' => $shipment->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Tüm aktif kargoları toplu sorgula
     *
     * @return array{total: int, success: int, failed: int}
     */
    public function bulkTrackAll(?int $limit = null): array
    {
        $limit = $limit ?? config('cargo.tracking_sync.batch_size', 100);

        $shipments = Shipment::whereNotIn('status', ['delivered', 'cancelled', 'returned', 'failed'])
            ->whereNotNull('tracking_number')
            ->orderBy('last_tracking_update', 'asc')
            ->limit($limit)
            ->get();

        $successCount = 0;
        $failCount = 0;

        foreach ($shipments as $shipment) {
            $result = $this->trackShipment($shipment);

            if ($result['success']) {
                $successCount++;
            } else {
                $failCount++;
            }

            // API rate limiting
            usleep(100000); // 100ms
        }

        return [
            'total' => $shipments->count(),
            'success' => $successCount,
            'failed' => $failCount,
        ];
    }

    /**
     * Shipment için entegrasyon bul
     */
    protected function getIntegration(Shipment $shipment): ?CargoIntegration
    {
        // Önce shipment'a bağlı entegrasyon
        if ($shipment->cargo_integration_id) {
            return CargoIntegration::find($shipment->cargo_integration_id);
        }

        // Sonra vendor'ın varsayılan entegrasyonu
        $vendor = $shipment->orderItem?->vendor ?? $shipment->orderItem?->order?->vendor;

        if ($vendor) {
            $integration = CargoIntegration::where('vendor_id', $vendor->id)
                ->where('is_active', true)
                ->first();

            if ($integration) {
                return $integration;
            }
        }

        // Sonra platform varsayılan entegrasyonu
        return CargoIntegration::whereNull('vendor_id')
            ->where('is_active', true)
            ->first();
    }

    /**
     * Takip numarası oluştur
     */
    protected function generateTrackingNumber(string $providerCode): string
    {
        $prefix = strtoupper(substr($providerCode, 0, 3));
        $timestamp = now()->format('ymdHis');
        $random = strtoupper(Str::random(4));

        return "{$prefix}{$timestamp}{$random}";
    }

    /**
     * Yeni event'leri kaydet
     *
     * @param  array<int, array{date: string, status: string, location?: string, description?: string}>  $events
     */
    protected function saveNewEvents(Shipment $shipment, array $events): void
    {
        foreach ($events as $eventData) {
            // Event zaten var mı kontrol et
            $exists = ShipmentEvent::where('shipment_id', $shipment->id)
                ->where('status', $eventData['status'])
                ->where('occurred_at', $eventData['date'])
                ->exists();

            if (! $exists) {
                ShipmentEvent::create([
                    'shipment_id' => $shipment->id,
                    'status' => $eventData['status'],
                    'description' => $eventData['description'] ?? null,
                    'location' => $eventData['location'] ?? null,
                    'occurred_at' => $eventData['date'],
                ]);
            }
        }
    }
}
