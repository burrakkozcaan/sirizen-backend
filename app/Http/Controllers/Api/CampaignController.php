<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;

class CampaignController extends Controller
{
    /**
     * Get all active campaigns.
     */
    public function active(): JsonResponse
    {
        $campaigns = Campaign::where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->orderBy('starts_at', 'desc')
            ->get()
            ->map(fn (Campaign $campaign) => $this->formatCampaign($campaign));

        return response()->json([
            'data' => $campaigns,
        ]);
    }

    /**
     * Get hero campaigns for homepage carousel (minimum 3, maximum 5 campaigns).
     * Prioritizes active campaigns within date range, then fills with other active campaigns if needed.
     */
    public function hero(): JsonResponse
    {
        // First, get campaigns that are active and within date range
        $activeCampaigns = Campaign::where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->orderBy('starts_at', 'desc')
            ->limit(5)
            ->get();

        // If we have less than 3 campaigns, fill with other active campaigns (even if date range doesn't match)
        if ($activeCampaigns->count() < 3) {
            $additionalCampaigns = Campaign::where('is_active', true)
                ->whereNotIn('id', $activeCampaigns->pluck('id'))
                ->orderBy('starts_at', 'desc')
                ->limit(3 - $activeCampaigns->count())
                ->get();

            $activeCampaigns = $activeCampaigns->merge($additionalCampaigns)->take(5);
        }

        $campaigns = $activeCampaigns->map(fn (Campaign $campaign) => $this->formatCampaign($campaign));

        return response()->json([
            'data' => $campaigns,
        ]);
    }

    /**
     * Get single campaign by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $campaign = Campaign::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'data' => $this->formatCampaign($campaign),
        ]);
    }

    /**
     * Get products for a campaign by slug.
     */
    public function products(string $slug): JsonResponse
    {
        $limit = min(request()->input('limit', 24), 100);
        
        $campaign = Campaign::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = $campaign->products()
            ->with(['vendor', 'category', 'brand', 'images'])
            ->where('is_active', true)
            ->orderBy('rating', 'desc')
            ->orderBy('reviews_count', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => ProductResource::collection($products),
        ]);
    }

    /**
     * Format campaign for API response.
     * Converts discount_type + discount_value to discount_percentage for frontend compatibility.
     */
    private function formatCampaign(Campaign $campaign): array
    {
        // Calculate discount_percentage if discount_type is percentage
        $discountPercentage = null;
        if ($campaign->discount_type === 'percentage') {
            $discountPercentage = (int) $campaign->discount_value;
        }

        // Use banner as image (hero banner)
        $image = $campaign->banner;

        return [
            'id' => $campaign->id,
            'title' => $campaign->title,
            'slug' => $campaign->slug,
            'description' => $campaign->description,
            'image' => $image,
            'banner' => $campaign->banner,
            'discount_percentage' => $discountPercentage,
            'discount_type' => $campaign->discount_type,
            'discount_value' => $campaign->discount_value,
            'start_date' => (string) $campaign->starts_at,
            'end_date' => (string) $campaign->ends_at,
            'is_active' => $campaign->is_active,
        ];
    }
}
