<?php

namespace App\Observers;

use App\Jobs\RecalculateBuyBoxJob;
use App\Models\Order;
use App\Models\ProductSeller;
use App\OrderItemStatus;
use App\OrderStatus;
use Illuminate\Support\Facades\DB;

class OrderObserver
{
    public function updated(Order $order): void
    {
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            $this->syncItemStatuses($order);
            $this->handleStockSync($order, $oldStatus, $newStatus);
        }
    }

    private function syncItemStatuses(Order $order): void
    {
        $newStatus = match ($order->status) {
            OrderStatus::CANCELLED->value, 'cancelled' => OrderItemStatus::CANCELLED->value,
            OrderStatus::REFUNDED->value, 'refunded' => OrderItemStatus::RETURNED->value,
            OrderStatus::CONFIRMED->value, 'confirmed' => OrderItemStatus::PREPARING->value,
            default => null,
        };

        if ($newStatus) {
            $order->items()->update(['status' => $newStatus]);
        }
    }

    private function handleStockSync(Order $order, ?string $oldStatus, string $newStatus): void
    {
        $confirmStatuses = [OrderStatus::CONFIRMED->value, 'confirmed'];
        $cancelStatuses = [OrderStatus::CANCELLED->value, 'cancelled', OrderStatus::REFUNDED->value, 'refunded'];

        $wasConfirmed = in_array($oldStatus, $confirmStatuses);
        $isNowConfirmed = in_array($newStatus, $confirmStatuses);
        $isNowCancelled = in_array($newStatus, $cancelStatuses);

        if (! $wasConfirmed && $isNowConfirmed) {
            $this->decreaseStock($order);
        }

        if ($wasConfirmed && $isNowCancelled) {
            $this->increaseStock($order);
        }
    }

    private function decreaseStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $productIds = [];

            foreach ($order->items as $item) {
                if ($item->product_seller_id) {
                    ProductSeller::where('id', $item->product_seller_id)
                        ->where('stock', '>=', $item->quantity)
                        ->decrement('stock', $item->quantity);

                    $productIds[] = $item->product_id;
                } elseif ($item->variant_id && $item->vendor_id) {
                    ProductSeller::where('product_id', $item->product_id)
                        ->where('variant_id', $item->variant_id)
                        ->where('vendor_id', $item->vendor_id)
                        ->where('stock', '>=', $item->quantity)
                        ->decrement('stock', $item->quantity);

                    $productIds[] = $item->product_id;
                }
            }

            foreach (array_unique($productIds) as $productId) {
                RecalculateBuyBoxJob::dispatch($productId);
            }
        });
    }

    private function increaseStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $productIds = [];

            foreach ($order->items as $item) {
                if ($item->product_seller_id) {
                    ProductSeller::where('id', $item->product_seller_id)
                        ->increment('stock', $item->quantity);

                    $productIds[] = $item->product_id;
                } elseif ($item->variant_id && $item->vendor_id) {
                    ProductSeller::where('product_id', $item->product_id)
                        ->where('variant_id', $item->variant_id)
                        ->where('vendor_id', $item->vendor_id)
                        ->increment('stock', $item->quantity);

                    $productIds[] = $item->product_id;
                }
            }

            foreach (array_unique($productIds) as $productId) {
                RecalculateBuyBoxJob::dispatch($productId);
            }
        });
    }
}
