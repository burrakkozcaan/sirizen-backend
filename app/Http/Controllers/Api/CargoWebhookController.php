<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\CargoProvider;
use App\Events\ShipmentStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CargoWebhookController extends Controller
{
    /**
     * Aras Kargo durum bildirimi
     */
    public function handleArasWebhook(Request $request): JsonResponse
    {
        Log::info('Aras Kargo webhook received', [
            'payload' => $request->all(),
        ]);

        try {
            $trackingNumber = $request->input('tracking_number') ?? $request->input('kargo_takip_no');
            $status = $request->input('status') ?? $request->input('durum');
            $description = $request->input('description') ?? $request->input('aciklama');

            if (! $trackingNumber) {
                return response()->json(['error' => 'Takip numarası bulunamadı'], 400);
            }

            $this->processWebhook($trackingNumber, $status, $description, CargoProvider::Aras, $request->all());

            return response()->json(['status' => 'received']);
        } catch (\Throwable $e) {
            Log::error('Aras webhook error', ['error' => $e->getMessage()]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Yurtiçi Kargo durum bildirimi
     */
    public function handleYurticiWebhook(Request $request): JsonResponse
    {
        Log::info('Yurtiçi Kargo webhook received', [
            'payload' => $request->all(),
        ]);

        try {
            $trackingNumber = $request->input('tracking_number') ?? $request->input('cargoKey');
            $status = $request->input('status') ?? $request->input('operationCode');
            $description = $request->input('description') ?? $request->input('operationMessage');

            if (! $trackingNumber) {
                return response()->json(['error' => 'Takip numarası bulunamadı'], 400);
            }

            $this->processWebhook($trackingNumber, $status, $description, CargoProvider::Yurtici, $request->all());

            return response()->json(['status' => 'received']);
        } catch (\Throwable $e) {
            Log::error('Yurtiçi webhook error', ['error' => $e->getMessage()]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * MNG Kargo durum bildirimi
     */
    public function handleMngWebhook(Request $request): JsonResponse
    {
        Log::info('MNG Kargo webhook received', [
            'payload' => $request->all(),
        ]);

        try {
            $trackingNumber = $request->input('tracking_number') ?? $request->input('barcode');
            $status = $request->input('status') ?? $request->input('statusCode');
            $description = $request->input('description') ?? $request->input('statusDescription');

            if (! $trackingNumber) {
                return response()->json(['error' => 'Takip numarası bulunamadı'], 400);
            }

            $this->processWebhook($trackingNumber, $status, $description, CargoProvider::Mng, $request->all());

            return response()->json(['status' => 'received']);
        } catch (\Throwable $e) {
            Log::error('MNG webhook error', ['error' => $e->getMessage()]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Webhook verilerini işle
     *
     * @param  array<string, mixed>  $rawPayload
     */
    protected function processWebhook(
        string $trackingNumber,
        ?string $status,
        ?string $description,
        CargoProvider $provider,
        array $rawPayload
    ): void {
        $shipment = Shipment::where('tracking_number', $trackingNumber)->first();

        if (! $shipment) {
            Log::warning('Shipment not found for tracking number', [
                'tracking_number' => $trackingNumber,
                'provider' => $provider->value,
            ]);

            return;
        }

        // Durum kodunu normalize et
        $normalizedStatus = $this->normalizeStatus($status, $provider);

        // Shipment durumunu güncelle
        $previousStatus = $shipment->status;
        $shipment->update([
            'status' => $normalizedStatus,
            'last_tracking_update' => now(),
        ]);

        // ShipmentEvent oluştur
        ShipmentEvent::create([
            'shipment_id' => $shipment->id,
            'status' => $normalizedStatus,
            'description' => $description,
            'location' => $rawPayload['location'] ?? $rawPayload['konum'] ?? null,
            'occurred_at' => $rawPayload['timestamp'] ?? $rawPayload['tarih'] ?? now(),
            'raw_data' => $rawPayload,
        ]);

        // Event tetikle
        if ($previousStatus !== $normalizedStatus) {
            event(new ShipmentStatusUpdated($shipment, $previousStatus, $normalizedStatus));
        }
    }

    /**
     * Kargo durumunu normalize et
     */
    protected function normalizeStatus(?string $status, CargoProvider $provider): string
    {
        if (! $status) {
            return 'unknown';
        }

        $statusLower = strtolower($status);

        // Provider'a özgü durum eşlemeleri
        $statusMappings = [
            'aras' => [
                'teslim edildi' => 'delivered',
                'dagitimda' => 'out_for_delivery',
                'aktarimda' => 'in_transit',
                'kargo alindi' => 'picked_up',
                'iptal' => 'cancelled',
            ],
            'yurtici' => [
                'teslim edildi' => 'delivered',
                'dagitima cikti' => 'out_for_delivery',
                'transfer' => 'in_transit',
                'kargo alindi' => 'picked_up',
                'iptal' => 'cancelled',
            ],
            'mng' => [
                'delivered' => 'delivered',
                'out_for_delivery' => 'out_for_delivery',
                'in_transit' => 'in_transit',
                'picked_up' => 'picked_up',
                'cancelled' => 'cancelled',
            ],
        ];

        $providerMappings = $statusMappings[$provider->value] ?? [];

        foreach ($providerMappings as $key => $normalizedValue) {
            if (str_contains($statusLower, $key)) {
                return $normalizedValue;
            }
        }

        return $statusLower;
    }
}
