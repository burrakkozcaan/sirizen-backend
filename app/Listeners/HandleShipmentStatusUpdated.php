<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ShipmentStatusUpdated;
use App\Models\Commission;
use App\Models\VendorBalance;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HandleShipmentStatusUpdated implements ShouldQueue
{
    public function handle(ShipmentStatusUpdated $event): void
    {
        $shipment = $event->shipment;
        $newStatus = $event->newStatus;

        // Sipariş teslim edildiğinde pending_balance → available_balance'a taşı
        if ($newStatus === 'delivered') {
            $this->settleVendorBalance($shipment);
        }

        Log::info('ShipmentStatusUpdated handled', [
            'shipment_id'     => $shipment->id,
            'previous_status' => $event->previousStatus,
            'new_status'      => $newStatus,
        ]);
    }

    private function settleVendorBalance($shipment): void
    {
        // Kargoya ait order_item'ı bul
        $orderItem = $shipment->orderItem;
        if (! $orderItem) {
            return;
        }

        $commission = Commission::where('order_item_id', $orderItem->id)
            ->where('status', 'paid')
            ->first();

        if (! $commission) {
            return;
        }

        DB::transaction(function () use ($commission) {
            $balance = VendorBalance::where('vendor_id', $commission->vendor_id)->first();
            if (! $balance) {
                return;
            }

            // pending_balance → available_balance
            $amount = min($commission->net_amount, $balance->pending_balance);
            $balance->decrement('pending_balance', $amount);
            $balance->increment('available_balance', $amount);
            $balance->update(['last_settlement_at' => now()]);

            // Komisyonu settled olarak işaretle
            $commission->update(['settled_at' => now()]);
        });
    }

    public function failed(ShipmentStatusUpdated $event, \Throwable $exception): void
    {
        Log::error('HandleShipmentStatusUpdated listener failed', [
            'shipment_id' => $event->shipment->id,
            'error'       => $exception->getMessage(),
        ]);
    }
}
