<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;

class PricingService
{
    /**
     * Get product pricing (changes frequently - short cache).
     * Cache: CDN 30-60 seconds, Edge cache
     */
    public function getPricing(int $productId, ?int $variantId = null): ?array
    {
        $cacheKey = $variantId 
            ? "product:pricing:{$productId}:variant:{$variantId}"
            : "product:pricing:{$productId}";

        return Cache::remember($cacheKey, now()->addSeconds(30), function () use ($productId, $variantId) {
            $product = Product::with([
                'variants' => fn ($q) => $q->where('is_active', true),
                'productCampaigns.campaign' => fn ($q) => $q->where('is_active', true)
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now()),
            ])
                ->where('id', $productId)
                ->where('is_active', true)
                ->first();

            if (!$product) {
                return null;
            }

            $variant = $variantId 
                ? $product->variants->firstWhere('id', $variantId)
                : $product->variants->firstWhere('is_default', true) ?? $product->variants->first();

            // If no variant selected and product has variants, use first variant or product price
            if (!$variant && $product->variants->isNotEmpty()) {
                $variant = $product->variants->first();
            }

            // Use variant price if available, otherwise use product price
            $basePrice = $variant ? ($variant->price ?? $product->price) : $product->price;
            $salePrice = $variant ? ($variant->sale_price ?? $variant->price ?? $product->discount_price) : $product->discount_price;
            $originalPrice = $variant ? ($variant->original_price ?? $variant->price ?? $product->original_price ?? $product->price) : ($product->original_price ?? $product->price);
            $stock = $variant ? ($variant->stock ?? 0) : ($product->stock ?? 0);
            $currency = $product->currency ?? 'TRY';

            // Calculate campaign discount
            $campaignDiscount = 0;
            $campaign = $product->productCampaigns->first()?->campaign;
            if ($campaign && $campaign->discount_type === 'percentage') {
                $priceToDiscount = $salePrice ?? $basePrice;
                $campaignDiscount = $priceToDiscount * ($campaign->discount_value / 100);
            }

            $finalPrice = ($salePrice ?? $basePrice) - $campaignDiscount;
            if ($finalPrice < 0) {
                $finalPrice = 0;
            }

            return [
                'product_id' => $productId,
                'variant_id' => $variant?->id,
                'price' => (float) $basePrice,
                'sale_price' => $salePrice ? (float) $salePrice : null,
                'original_price' => $originalPrice ? (float) $originalPrice : null,
                'final_price' => (float) $finalPrice,
                'currency' => $currency,
                'stock' => (int) $stock,
                'is_in_stock' => $stock > 0,
                'campaign' => $campaign ? [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'discount_type' => $campaign->discount_type,
                    'discount_value' => (float) $campaign->discount_value,
                    'discount_amount' => (float) $campaignDiscount,
                ] : null,
                'variants' => $product->variants->map(fn ($v) => [
                    'id' => $v->id,
                    'price' => (float) ($v->price ?? $product->price),
                    'sale_price' => $v->sale_price ? (float) $v->sale_price : null,
                    'stock' => (int) ($v->stock ?? 0),
                    'is_default' => (bool) ($v->is_default ?? false),
                    'value' => $v->value ?? $v->size ?? $v->color ?? "Varyant {$v->id}",
                ])->toArray(),
            ];
        });
    }

    /**
     * Invalidate pricing cache.
     */
    public function invalidate(int $productId, ?int $variantId = null): void
    {
        $keys = [
            "product:pricing:{$productId}",
        ];

        if ($variantId) {
            $keys[] = "product:pricing:{$productId}:variant:{$variantId}";
        }

        // Also invalidate all variants
        $product = Product::find($productId);
        if ($product) {
            foreach ($product->variants as $variant) {
                $keys[] = "product:pricing:{$productId}:variant:{$variant->id}";
            }
        }

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}

