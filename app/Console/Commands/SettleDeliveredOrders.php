<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Commission;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\VendorBalance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Teslim edilen siparişlerin bekleyen bakiyelerini çekilebilir bakiyeye taşır.
 *
 * Trendyol Mantığı:
 * - Ürün teslim edilir
 * - +7 gün bekleme süresi geçer
 * - İade yok ise satış kesinleşir
 * - pending_balance → available_balance
 */
class SettleDeliveredOrders extends Command
{
    protected $signature = 'vendor:settle-delivered
                            {--days=7 : Teslimattan sonra bekleme süresi (gün)}
                            {--dry-run : Gerçek işlem yapmadan simüle et}';

    protected $description = 'Teslim edilen siparişlerin satıcı bakiyelerini kesinleştir';

    public function handle(): int
    {
        $waitingDays = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info("Bekleme süresi: {$waitingDays} gün");

        if ($dryRun) {
            $this->warn('DRY RUN modu - gerçek işlem yapılmayacak');
        }

        // Teslim edilmiş ve bekleme süresi geçmiş shipmentları bul
        $settleableShipments = Shipment::query()
            ->where('status', 'delivered')
            ->whereNotNull('delivered_at')
            ->where('delivered_at', '<=', now()->subDays($waitingDays))
            ->whereHas('orderItem.commission', function ($query) {
                $query->where('status', 'paid') // Ödeme alınmış
                      ->where('settled_at', null); // Henüz kesinleştirilmemiş
            })
            ->with(['orderItem.commission', 'vendor'])
            ->get();

        if ($settleableShipments->isEmpty()) {
            $this->info('Kesinleştirilecek sipariş bulunamadı.');
            return Command::SUCCESS;
        }

        $this->info("Kesinleştirilecek shipment sayısı: {$settleableShipments->count()}");

        $settledCount = 0;
        $totalAmount = 0;

        foreach ($settleableShipments as $shipment) {
            $orderItem = $shipment->orderItem;
            $commission = $orderItem?->commission;
            $vendor = $shipment->vendor ?? $orderItem?->vendor;

            if (!$commission || !$vendor) {
                $this->warn("Shipment #{$shipment->id}: Komisyon veya vendor bulunamadı, atlanıyor.");
                continue;
            }

            // İade kontrolü
            $hasReturn = $this->checkForReturn($orderItem);
            if ($hasReturn) {
                $this->warn("OrderItem #{$orderItem->id}: İade talebi var, atlanıyor.");
                continue;
            }

            $netAmount = (float) $commission->net_amount;

            $this->line("  - Shipment #{$shipment->id} | Vendor: {$vendor->name} | Tutar: {$netAmount} TL");

            if (!$dryRun) {
                try {
                    DB::transaction(function () use ($commission, $vendor, $netAmount) {
                        // Komisyonu kesinleştir
                        $commission->update([
                            'settled_at' => now(),
                        ]);

                        // VendorBalance güncelle
                        $balance = VendorBalance::firstOrCreate(
                            ['vendor_id' => $vendor->id],
                            [
                                'balance' => 0,
                                'available_balance' => 0,
                                'pending_balance' => 0,
                                'total_earnings' => 0,
                                'total_withdrawn' => 0,
                                'currency' => 'TRY',
                            ]
                        );

                        // pending → available
                        $balance->decrement('pending_balance', min($netAmount, $balance->pending_balance));
                        $balance->increment('available_balance', $netAmount);
                        $balance->update(['last_settlement_at' => now()]);
                    });

                    $settledCount++;
                    $totalAmount += $netAmount;

                    Log::info('Vendor balance settled', [
                        'shipment_id' => $shipment->id,
                        'vendor_id' => $vendor->id,
                        'amount' => $netAmount,
                    ]);
                } catch (\Throwable $e) {
                    $this->error("Shipment #{$shipment->id} işlenirken hata: {$e->getMessage()}");
                    Log::error('Settle delivered order failed', [
                        'shipment_id' => $shipment->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                $settledCount++;
                $totalAmount += $netAmount;
            }
        }

        $this->newLine();
        $this->info("İşlem tamamlandı:");
        $this->info("  - Kesinleştirilen: {$settledCount} adet");
        $this->info("  - Toplam tutar: " . number_format($totalAmount, 2) . " TL");

        return Command::SUCCESS;
    }

    /**
     * OrderItem için aktif iade talebi var mı?
     */
    private function checkForReturn(OrderItem $orderItem): bool
    {
        // ProductReturn modeli varsa kontrol et
        if (class_exists(\App\Models\ProductReturn::class)) {
            return \App\Models\ProductReturn::where('order_item_id', $orderItem->id)
                ->whereIn('status', ['pending', 'approved', 'processing'])
                ->exists();
        }

        return false;
    }
}
