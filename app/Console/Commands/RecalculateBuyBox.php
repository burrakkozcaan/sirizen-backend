<?php

namespace App\Console\Commands;

use App\Services\BuyBoxService;
use Illuminate\Console\Command;

class RecalculateBuyBox extends Command
{
    protected $signature = 'buybox:recalculate
                            {--product= : Belirli bir ürün için hesapla}';

    protected $description = 'Tüm ürünler için BuyBox winner\'ı yeniden hesapla';

    public function handle(BuyBoxService $buyBoxService): int
    {
        $productId = $this->option('product');

        if ($productId) {
            $this->info("Ürün #{$productId} için BuyBox hesaplanıyor...");
            $buyBoxService->calculateForProduct((int) $productId);
            $this->info('Tamamlandı!');

            return self::SUCCESS;
        }

        $this->info('Tüm ürünler için BuyBox hesaplanıyor...');

        $bar = $this->output->createProgressBar();
        $bar->start();

        $count = $buyBoxService->recalculateAll();

        $bar->finish();
        $this->newLine();

        $this->info("{$count} ürün için BuyBox güncellendi.");

        return self::SUCCESS;
    }
}
