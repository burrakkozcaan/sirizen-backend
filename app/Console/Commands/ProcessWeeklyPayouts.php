<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Vendor;
use App\Models\VendorBalance;
use App\Models\VendorPayout;
use App\PayoutStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Haftalık satıcı hakediş hesaplaması ve ödeme kaydı oluşturma.
 *
 * Trendyol Mantığı:
 * - Haftada 1 gün (örn. Pazartesi) çalışır
 * - Tüm satıcıları tarar
 * - Çekilebilir bakiyeleri toplar
 * - Ödeme kaydı oluşturur (admin onayı bekler)
 */
class ProcessWeeklyPayouts extends Command
{
    protected $signature = 'vendor:process-payouts
                            {--min-amount=100 : Minimum ödeme tutarı (TL)}
                            {--dry-run : Gerçek işlem yapmadan simüle et}
                            {--force : Bakiye kontrolü olmadan tüm satıcıları işle}';

    protected $description = 'Haftalık satıcı hakediş hesaplaması ve ödeme kaydı oluştur';

    public function handle(): int
    {
        $minAmount = (float) $this->option('min-amount');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info("Minimum ödeme tutarı: {$minAmount} TL");

        if ($dryRun) {
            $this->warn('DRY RUN modu - gerçek işlem yapılmayacak');
        }

        // Dönem hesapla (geçen hafta Pazartesi - Pazar)
        $periodEnd = now()->startOfWeek()->subDay(); // Geçen Pazar
        $periodStart = $periodEnd->copy()->subDays(6); // Geçen Pazartesi

        $this->info("Dönem: {$periodStart->format('d.m.Y')} - {$periodEnd->format('d.m.Y')}");

        // Çekilebilir bakiyesi olan satıcıları bul
        $query = VendorBalance::query()
            ->with(['vendor.user'])
            ->whereHas('vendor', function ($q) {
                $q->where('status', 'active');
            });

        if (!$force) {
            $query->where('available_balance', '>=', $minAmount);
        }

        $vendorBalances = $query->get();

        if ($vendorBalances->isEmpty()) {
            $this->info('Ödeme yapılacak satıcı bulunamadı.');
            return Command::SUCCESS;
        }

        $this->info("İşlenecek satıcı sayısı: {$vendorBalances->count()}");
        $this->newLine();

        // Tablo başlıkları
        $this->table(
            ['Satıcı ID', 'Satıcı Adı', 'IBAN', 'Çekilebilir Bakiye', 'Durum'],
            $vendorBalances->map(function ($balance) use ($minAmount) {
                $vendor = $balance->vendor;
                $amount = (float) $balance->available_balance;
                $status = $amount >= $minAmount ? '✓ Ödeme yapılacak' : '✗ Minimum altında';

                return [
                    $vendor->id,
                    $vendor->name,
                    $this->maskIban($vendor->iban ?? '-'),
                    number_format($amount, 2) . ' TL',
                    $status,
                ];
            })->toArray()
        );

        if (!$this->confirm('Ödeme kayıtları oluşturulsun mu?', !$dryRun)) {
            $this->info('İşlem iptal edildi.');
            return Command::SUCCESS;
        }

        $createdCount = 0;
        $totalAmount = 0;
        $payoutIds = [];

        foreach ($vendorBalances as $balance) {
            $vendor = $balance->vendor;
            $amount = (float) $balance->available_balance;

            if ($amount < $minAmount && !$force) {
                continue;
            }

            if (!$vendor->iban) {
                $this->warn("Vendor #{$vendor->id} ({$vendor->name}): IBAN eksik, atlanıyor.");
                continue;
            }

            if (!$dryRun) {
                try {
                    $payout = DB::transaction(function () use ($vendor, $balance, $amount, $periodStart, $periodEnd) {
                        // Payout kaydı oluştur
                        $payout = VendorPayout::create([
                            'vendor_id' => $vendor->id,
                            'amount' => $amount,
                            'payout_method' => 'bank_transfer',
                            'status' => PayoutStatus::Pending->value ?? 'pending',
                            'period_start' => $periodStart,
                            'period_end' => $periodEnd,
                        ]);

                        // Bakiyeyi sıfırla (ödeme onaylandığında tekrar düşülecek)
                        // NOT: Şimdilik sadece kayıt oluşturuyoruz, bakiye admin onayından sonra düşülecek
                        // $balance->decrement('available_balance', $amount);
                        // $balance->increment('total_withdrawn', $amount);

                        return $payout;
                    });

                    $payoutIds[] = $payout->id;
                    $createdCount++;
                    $totalAmount += $amount;

                    Log::info('Weekly payout created', [
                        'payout_id' => $payout->id,
                        'vendor_id' => $vendor->id,
                        'amount' => $amount,
                        'period' => "{$periodStart->format('Y-m-d')} - {$periodEnd->format('Y-m-d')}",
                    ]);
                } catch (\Throwable $e) {
                    $this->error("Vendor #{$vendor->id} işlenirken hata: {$e->getMessage()}");
                    Log::error('Weekly payout creation failed', [
                        'vendor_id' => $vendor->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                $createdCount++;
                $totalAmount += $amount;
            }
        }

        $this->newLine();
        $this->info("İşlem tamamlandı:");
        $this->info("  - Oluşturulan ödeme kaydı: {$createdCount} adet");
        $this->info("  - Toplam tutar: " . number_format($totalAmount, 2) . " TL");

        if (!empty($payoutIds)) {
            $this->newLine();
            $this->info("Ödeme kayıtları admin panelden onaylanabilir:");
            $this->info("  → /admin/vendor-payouts");
        }

        return Command::SUCCESS;
    }

    /**
     * IBAN'ı maskele (güvenlik için)
     */
    private function maskIban(?string $iban): string
    {
        if (!$iban || strlen($iban) < 10) {
            return $iban ?? '-';
        }

        return substr($iban, 0, 4) . '****' . substr($iban, -4);
    }
}
