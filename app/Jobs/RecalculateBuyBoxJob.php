<?php

namespace App\Jobs;

use App\Services\BuyBoxService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RecalculateBuyBoxJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public ?int $productId = null
    ) {}

    public function handle(BuyBoxService $buyBoxService): void
    {
        if ($this->productId) {
            $buyBoxService->calculateForProduct($this->productId);
            Log::info("BuyBox recalculated for product #{$this->productId}");

            return;
        }

        $count = $buyBoxService->recalculateAll();
        Log::info("BuyBox recalculated for {$count} products");
    }
}
