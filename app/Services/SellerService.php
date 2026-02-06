<?php

namespace App\Services;

use App\Models\Vendor;
use Illuminate\Support\Facades\Cache;

class SellerService
{
    /**
     * Get seller info (medium frequency - short cache).
     * Cache: CDN 5-10 minutes
     */
    public function getSeller(int $sellerId, ?int $userId = null): ?array
    {
        $cacheKey = "seller:{$sellerId}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($sellerId, $userId) {
            $vendor = Vendor::with([
                'badges:id,name,icon',
                'tier:id,name',
            ])
                ->where('id', $sellerId)
                ->where('status', 'active')
                ->first();

            if (!$vendor) {
                return null;
            }

            // Check if user follows this seller
            $isFollowing = false;
            if ($userId) {
                $isFollowing = \App\Models\VendorFollower::where('vendor_id', $sellerId)
                    ->where('user_id', $userId)
                    ->exists();
            }

            return [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'slug' => $vendor->slug,
                'logo' => $vendor->logo,
                'rating' => (float) ($vendor->rating ?? 0),
                'review_count' => (int) ($vendor->review_count ?? 0),
                'follower_count' => (int) ($vendor->follower_count ?? 0),
                'product_count' => (int) ($vendor->product_count ?? 0),
                'is_official' => (bool) ($vendor->is_official ?? false),
                'badges' => $vendor->badges->map(fn ($badge) => [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'icon' => $badge->icon,
                ]),
                'tier' => $vendor->tier ? [
                    'id' => $vendor->tier->id,
                    'name' => $vendor->tier->name,
                ] : null,
                'shipping_speed' => $this->getShippingSpeed($vendor->id),
                'return_policy' => $this->getReturnPolicy($vendor->id),
                'is_following' => $isFollowing,
            ];
        });
    }

    /**
     * Get shipping speed info.
     */
    private function getShippingSpeed(int $vendorId): array
    {
        $rule = \App\Models\ShippingRule::where('vendor_id', $vendorId)->first();
        
        if (!$rule) {
            return [
                'estimated_days' => 2,
                'same_day_shipping' => false,
                'cutoff_time' => '16:00',
            ];
        }

        return [
            'estimated_days' => 2, // Default, can be extended
            'same_day_shipping' => (bool) $rule->same_day_shipping,
            'cutoff_time' => $rule->cutoff_time ? $rule->cutoff_time->format('H:i') : '16:00',
            'free_shipping' => (bool) $rule->free_shipping,
            'free_shipping_min_amount' => $rule->free_shipping_min_amount ? (float) $rule->free_shipping_min_amount : null,
        ];
    }

    /**
     * Get return policy.
     */
    private function getReturnPolicy(int $vendorId): array
    {
        $policy = \App\Models\ReturnPolicy::where('vendor_id', $vendorId)->first();
        
        if (!$policy) {
            return [
                'days' => 15,
                'is_free' => true,
                'conditions' => 'Ürün kullanılmamış ve etiketli olmalıdır.',
            ];
        }

        return [
            'days' => (int) $policy->days,
            'is_free' => (bool) $policy->is_free,
            'conditions' => $policy->conditions ?? 'Ürün kullanılmamış ve etiketli olmalıdır.',
        ];
    }

    /**
     * Invalidate seller cache.
     */
    public function invalidate(int $sellerId): void
    {
        Cache::forget("seller:{$sellerId}");
    }
}

