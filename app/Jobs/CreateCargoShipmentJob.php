<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\OrderItem;
use App\Models\Shipment;
use App\Services\Cargo\CargoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateCargoShipmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array<int, int>
     */
    public array $backoff = [30, 120, 300];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Shipment $shipment
    ) {}

    /**
     * Execute the job.
     */
    public function handle(CargoService $cargoService): void
    {
        Log::info('Creating cargo shipment', [
            'shipment_id' => $this->shipment->id,
            'order_item_id' => $this->shipment->order_item_id,
        ]);

        $result = $cargoService->createShipment($this->shipment);

        if ($result['success']) {
            Log::info('Cargo shipment created successfully', [
                'shipment_id' => $this->shipment->id,
                'tracking_number' => $result['tracking_number'] ?? null,
            ]);
        } else {
            Log::warning('Cargo shipment creation failed', [
                'shipment_id' => $this->shipment->id,
                'error' => $result['error'] ?? 'Unknown error',
            ]);

            // Hata durumunda shipment'ı güncelle
            $this->shipment->update([
                'status' => 'failed',
                'notes' => $result['error'] ?? 'Kargo oluşturulamadı',
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CreateCargoShipmentJob failed', [
            'shipment_id' => $this->shipment->id,
            'error' => $exception->getMessage(),
        ]);

        $this->shipment->update([
            'status' => 'failed',
            'notes' => 'Job failed: ' . $exception->getMessage(),
        ]);
    }
}
