<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductSeller;
use Illuminate\Support\Facades\DB;

class BuyBoxService
{
    private const WEIGHT_PRICE = 0.40;

    private const WEIGHT_RATING = 0.25;

    private const WEIGHT_FREE_SHIPPING = 0.20;

    private const WEIGHT_DELIVERY = 0.15;

    public function calculateWinner(int $productId, ?int $variantId = null): ?ProductSeller
    {
        $sellers = ProductSeller::query()
            ->where('product_id', $productId)
            ->where(function ($query) use ($variantId) {
                if ($variantId) {
                    $query->where('variant_id', $variantId);
                } else {
                    $query->whereNull('variant_id');
                }
            })
            ->where('stock', '>', 0)
            ->with('vendor')
            ->get();

        if ($sellers->isEmpty()) {
            return null;
        }

        $maxPrice = $sellers->max('price') ?: 1;
        $maxDispatchDays = $sellers->max('dispatch_days') ?: 1;

        $scored = $sellers->map(function ($seller) use ($maxPrice, $maxDispatchDays) {
            $priceScore = (1 - ($seller->price / $maxPrice)) * 100;
            $ratingScore = ($seller->vendor->rating ?? 0) * 10;
            $shippingScore = $seller->free_shipping ? 100 : 0;
            $deliveryScore = (1 - ($seller->dispatch_days / $maxDispatchDays)) * 100;

            $totalScore = ($priceScore * self::WEIGHT_PRICE)
                + ($ratingScore * self::WEIGHT_RATING)
                + ($shippingScore * self::WEIGHT_FREE_SHIPPING)
                + ($deliveryScore * self::WEIGHT_DELIVERY);

            return [
                'seller' => $seller,
                'score' => $totalScore,
            ];
        });

        $sorted = $scored->sortByDesc('score');
        $winner = $sorted->first()['seller'];

        DB::transaction(function () use ($sellers, $winner) {
            foreach ($sellers as $seller) {
                $seller->update([
                    'is_buybox_winner' => $seller->id === $winner->id,
                ]);
            }
        });

        return $winner;
    }

    public function calculateForProduct(int $productId): void
    {
        $this->calculateWinner($productId, null);

        $variantIds = ProductSeller::query()
            ->where('product_id', $productId)
            ->whereNotNull('variant_id')
            ->distinct()
            ->pluck('variant_id');

        foreach ($variantIds as $variantId) {
            $this->calculateWinner($productId, $variantId);
        }
    }

    public function recalculateAll(): int
    {
        $productIds = ProductSeller::query()
            ->where('stock', '>', 0)
            ->distinct()
            ->pluck('product_id');

        $count = 0;
        foreach ($productIds as $productId) {
            $this->calculateForProduct($productId);
            $count++;
        }

        return $count;
    }

    public function getWinner(int $productId, ?int $variantId = null): ?ProductSeller
    {
        return ProductSeller::query()
            ->where('product_id', $productId)
            ->where(function ($query) use ($variantId) {
                if ($variantId) {
                    $query->where('variant_id', $variantId);
                } else {
                    $query->whereNull('variant_id');
                }
            })
            ->where('is_buybox_winner', true)
            ->where('stock', '>', 0)
            ->with('vendor')
            ->first();
    }
}
