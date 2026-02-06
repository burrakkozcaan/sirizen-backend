<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Shipment;
use App\Services\Cargo\CargoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncShipmentTrackingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ?int $batchSize = null
    ) {
        $this->batchSize = $batchSize ?? config('cargo.tracking_sync.batch_size', 100);
    }

    /**
     * Execute the job.
     */
    public function handle(CargoService $cargoService): void
    {
        if (! config('cargo.tracking_sync.enabled', true)) {
            Log::info('Shipment tracking sync is disabled');

            return;
        }

        Log::info('Starting shipment tracking sync', [
            'batch_size' => $this->batchSize,
        ]);

        // Aktif kargoları al (teslim edilmemiş, iptal edilmemiş)
        $shipments = Shipment::whereNotIn('status', ['delivered', 'cancelled', 'returned', 'failed'])
            ->whereNotNull('tracking_number')
            ->orderBy('last_tracking_update', 'asc')
            ->limit($this->batchSize)
            ->get();

        $successCount = 0;
        $failCount = 0;

        foreach ($shipments as $shipment) {
            try {
                $result = $cargoService->trackShipment($shipment);

                if ($result['success']) {
                    $successCount++;
                } else {
                    $failCount++;
                }

                // API rate limiting için kısa bekle
                usleep(100000); // 100ms
            } catch (\Throwable $e) {
                $failCount++;
                Log::warning('Tracking sync failed for shipment', [
                    'shipment_id' => $shipment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Shipment tracking sync completed', [
            'total' => $shipments->count(),
            'success' => $successCount,
            'failed' => $failCount,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SyncShipmentTrackingJob failed', [
            'error' => $exception->getMessage(),
        ]);
    }
}
