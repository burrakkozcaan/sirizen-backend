<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Coupon;
use Illuminate\Support\Facades\Cache;

class CampaignService
{
    /**
     * Get campaigns for product (very dynamic - user-based cache).
     * Cache: User-based, Redis
     */
    public function getCampaigns(int $productId, ?int $userId = null): array
    {
        $cacheKey = $userId 
            ? "campaigns:product:{$productId}:user:{$userId}"
            : "campaigns:product:{$productId}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($productId, $userId) {
            // Product-specific campaigns
            $productCampaigns = \App\Models\ProductCampaign::where('product_id', $productId)
                ->with('campaign')
                ->whereHas('campaign', fn ($q) => $q->where('is_active', true)
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now()))
                ->get()
                ->map(fn ($pc) => [
                    'id' => $pc->campaign->id,
                    'title' => $pc->campaign->title,
                    'type' => 'discount',
                    'discount_type' => $pc->campaign->discount_type,
                    'discount_value' => (float) $pc->campaign->discount_value,
                ]);

            // Available coupons for user
            $coupons = [];
            if ($userId) {
                $coupons = Coupon::where('is_active', true)
                    ->where('expires_at', '>', now())
                    ->where(function ($q) use ($productId) {
                        $q->whereNull('product_id')
                            ->orWhere('product_id', $productId);
                    })
                    ->get()
                    ->map(fn ($c) => [
                        'id' => $c->id,
                        'code' => $c->code,
                        'title' => $c->title,
                        'discount_type' => $c->discount_type,
                        'discount_value' => (float) $c->discount_value,
                        'min_order' => $c->min_order_amount ? (float) $c->min_order_amount : null,
                    ]);
            }

            // Follow & earn rewards
            $followRewards = [];
            if ($userId) {
                $vendorId = \App\Models\Product::find($productId)?->vendor_id;
                if ($vendorId) {
                    $isFollowing = \App\Models\VendorFollower::where('vendor_id', $vendorId)
                        ->where('user_id', $userId)
                        ->exists();

                    if (!$isFollowing) {
                        $followRewards = [
                            [
                                'type' => 'follow_discount',
                                'title' => 'Takip Et %5 İndirim Kazan',
                                'discount_rate' => 5,
                                'action' => 'follow_vendor',
                                'target_id' => $vendorId,
                            ],
                        ];
                    }
                }
            }

            // Bundle offers (together buy)
            $bundles = \App\Models\ProductBundle::where('main_product_id', $productId)
                ->with('products:id,name,slug')
                ->where('is_active', true)
                ->get()
                ->map(fn ($bundle) => [
                    'id' => $bundle->id,
                    'type' => 'bundle',
                    'title' => $bundle->title ?? 'Birlikte Al',
                    'discount_rate' => (float) ($bundle->discount_rate ?? 0),
                    'items' => $bundle->products->map(fn ($product) => [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_slug' => $product->slug,
                    ]),
                ]);

            return [
                'product_campaigns' => $productCampaigns,
                'coupons' => $coupons,
                'follow_rewards' => $followRewards,
                'bundles' => $bundles,
            ];
        });
    }

    /**
     * Invalidate campaign cache.
     */
    public function invalidate(int $productId, ?int $userId = null): void
    {
        $keys = [
            "campaigns:product:{$productId}",
        ];

        if ($userId) {
            $keys[] = "campaigns:product:{$productId}:user:{$userId}";
        }

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}

