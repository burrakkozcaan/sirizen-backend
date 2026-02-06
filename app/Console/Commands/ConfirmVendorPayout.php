<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\VendorBalance;
use App\Models\VendorPayout;
use App\PayoutStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Satıcı ödemesini onayla ve bakiyeden düş.
 *
 * Kullanım:
 * - Tek ödeme onaylama: php artisan vendor:confirm-payout 123
 * - Toplu onaylama: php artisan vendor:confirm-payout --all
 */
class ConfirmVendorPayout extends Command
{
    protected $signature = 'vendor:confirm-payout
                            {payout_id? : Onaylanacak payout ID}
                            {--all : Tüm bekleyen ödemeleri onayla}
                            {--dry-run : Gerçek işlem yapmadan simüle et}';

    protected $description = 'Satıcı ödemesini onayla ve bakiyeden düş';

    public function handle(): int
    {
        $payoutId = $this->argument('payout_id');
        $confirmAll = $this->option('all');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN modu - gerçek işlem yapılmayacak');
        }

        if (!$payoutId && !$confirmAll) {
            $this->error('Payout ID veya --all seçeneği gerekli.');
            return Command::FAILURE;
        }

        $query = VendorPayout::query()
            ->with(['vendor'])
            ->where('status', PayoutStatus::Pending->value ?? 'pending');

        if ($payoutId) {
            $query->where('id', $payoutId);
        }

        $payouts = $query->get();

        if ($payouts->isEmpty()) {
            $this->info('Onaylanacak ödeme bulunamadı.');
            return Command::SUCCESS;
        }

        $this->info("Onaylanacak ödeme sayısı: {$payouts->count()}");

        // Liste göster
        $this->table(
            ['ID', 'Satıcı', 'Tutar', 'Dönem', 'Durum'],
            $payouts->map(function ($payout) {
                return [
                    $payout->id,
                    $payout->vendor->name ?? 'N/A',
                    number_format($payout->amount, 2) . ' TL',
                    $payout->period_start?->format('d.m') . ' - ' . $payout->period_end?->format('d.m.Y'),
                    $payout->status,
                ];
            })->toArray()
        );

        if (!$this->confirm('Bu ödemeler onaylansın mı? (Bakiyeden düşülecek)', !$dryRun)) {
            $this->info('İşlem iptal edildi.');
            return Command::SUCCESS;
        }

        $confirmedCount = 0;
        $totalAmount = 0;

        foreach ($payouts as $payout) {
            $vendor = $payout->vendor;
            $amount = (float) $payout->amount;

            if (!$dryRun) {
                try {
                    DB::transaction(function () use ($payout, $vendor, $amount) {
                        // Payout durumunu güncelle
                        $payout->update([
                            'status' => PayoutStatus::Completed->value ?? 'completed',
                            'paid_at' => now(),
                        ]);

                        // Bakiyeden düş
                        $balance = VendorBalance::where('vendor_id', $vendor->id)->first();

                        if ($balance) {
                            $balance->decrement('available_balance', min($amount, $balance->available_balance));
                            $balance->increment('total_withdrawn', $amount);
                        }
                    });

                    $confirmedCount++;
                    $totalAmount += $amount;

                    Log::info('Vendor payout confirmed', [
                        'payout_id' => $payout->id,
                        'vendor_id' => $vendor->id,
                        'amount' => $amount,
                    ]);

                    $this->line("  ✓ Payout #{$payout->id} onaylandı - {$vendor->name}: " . number_format($amount, 2) . " TL");
                } catch (\Throwable $e) {
                    $this->error("Payout #{$payout->id} işlenirken hata: {$e->getMessage()}");
                    Log::error('Vendor payout confirmation failed', [
                        'payout_id' => $payout->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                $confirmedCount++;
                $totalAmount += $amount;
                $this->line("  [DRY] Payout #{$payout->id} - {$vendor->name}: " . number_format($amount, 2) . " TL");
            }
        }

        $this->newLine();
        $this->info("İşlem tamamlandı:");
        $this->info("  - Onaylanan ödeme: {$confirmedCount} adet");
        $this->info("  - Toplam tutar: " . number_format($totalAmount, 2) . " TL");

        return Command::SUCCESS;
    }
}
