<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductQuestion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EngagementService
{
    /**
     * Get engagement stats (real-time - client side).
     * Cache: 30 seconds (very short)
     */
    public function getEngagement(int $productId, ?int $userId = null): array
    {
        $cacheKey = "engagement:{$productId}";

        return Cache::remember($cacheKey, now()->addSeconds(30), function () use ($productId, $userId) {
            $product = Product::find($productId);
            if (!$product) {
                return null;
            }

            // Review summary
            $reviewStats = ProductReview::where('product_id', $productId)
                ->selectRaw('
                    COUNT(*) as total,
                    AVG(rating) as avg_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                ')
                ->first();

            // Q&A count
            $qaCount = ProductQuestion::where('product_id', $productId)
                ->whereNotNull('answer')
                ->count();

            // Live stats (from Redis or DB snapshot)
            $liveStats = $this->getLiveStats($productId);

            // User-specific
            $userEngagement = [];
            if ($userId) {
                $userEngagement = [
                    'is_favorite' => \App\Models\Favorite::where('user_id', $userId)
                        ->where('product_id', $productId)
                        ->exists(),
                    'is_in_cart' => \App\Models\CartItem::where('user_id', $userId)
                        ->where('product_id', $productId)
                        ->exists(),
                    'has_price_alert' => \App\Models\PriceAlert::where('user_id', $userId)
                        ->where('product_id', $productId)
                        ->exists(),
                ];
            }

            return [
                'product_id' => $productId,
                'view_count' => (int) ($product->view_count ?? 0),
                'favorite_count' => (int) (\App\Models\Favorite::where('product_id', $productId)->count()),
                'cart_count' => (int) $liveStats['cart_count'],
                'purchase_count' => (int) $liveStats['purchase_count'],
                'reviews' => [
                    'total' => (int) ($reviewStats->total ?? 0),
                    'average_rating' => round((float) ($reviewStats->avg_rating ?? 0), 1),
                    'distribution' => [
                        5 => (int) ($reviewStats->five_star ?? 0),
                        4 => (int) ($reviewStats->four_star ?? 0),
                        3 => (int) ($reviewStats->three_star ?? 0),
                        2 => (int) ($reviewStats->two_star ?? 0),
                        1 => (int) ($reviewStats->one_star ?? 0),
                    ],
                ],
                'qa_count' => (int) $qaCount,
                'user' => $userEngagement,
            ];
        });
    }

    /**
     * Get live stats from Redis or DB snapshot.
     */
    private function getLiveStats(int $productId): array
    {
        // TODO: Implement Redis-based live stats
        // For now, return from DB
        return [
            'cart_count' => \App\Models\CartItem::where('product_id', $productId)->count(),
            'purchase_count' => \App\Models\OrderItem::where('product_id', $productId)
                ->whereHas('order', fn ($q) => $q->where('status', '!=', 'cancelled'))
                ->sum('quantity'),
        ];
    }

    /**
     * Invalidate engagement cache.
     */
    public function invalidate(int $productId): void
    {
        Cache::forget("engagement:{$productId}");
    }
}

