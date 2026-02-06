<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    /**
     * Get vendor collections for homepage display
     * GET /api/collections/vendor
     */
    public function vendorCollections(Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 6), 20);

        $collections = Collection::with(['vendor.sellerPages', 'products.images'])
            ->active()
            ->currentlyValid()
            ->orderBy('order')
            ->limit($limit)
            ->get();

        // If no collections exist, create virtual collections from featured vendors
        if ($collections->isEmpty()) {
            return $this->getFallbackCollections($limit);
        }

        $transformed = $collections->map(function ($collection) {
            return $this->transformCollection($collection);
        });

        return response()->json([
            'data' => $transformed,
        ]);
    }

    /**
     * Get collections by IDs
     * GET /api/collections?ids=1,2,3
     */
    public function index(Request $request): JsonResponse
    {
        $query = Collection::with(['vendor.sellerPages', 'products.images'])
            ->active()
            ->currentlyValid();

        // Filter by IDs if provided
        if ($request->has('ids')) {
            $ids = array_filter(explode(',', $request->input('ids')));
            $query->whereIn('id', $ids);
        }

        $collections = $query->orderBy('order')->get();

        $transformed = $collections->map(function ($collection) {
            return $this->transformCollection($collection);
        });

        return response()->json([
            'data' => $transformed,
        ]);
    }

    /**
     * Get single collection by ID
     * GET /api/collections/{id}
     */
    public function show(int $id): JsonResponse
    {
        $collection = Collection::with(['vendor.sellerPages', 'products.images'])
            ->findOrFail($id);

        return response()->json([
            'data' => $this->transformCollection($collection),
        ]);
    }

    /**
     * Transform collection to API response format
     */
    private function transformCollection(Collection $collection): array
    {
        return [
            'id' => $collection->id,
            'vendor' => [
                'id' => $collection->vendor->id,
                'name' => $collection->vendor->name,
                'slug' => $collection->vendor->slug,
                'logo' => $collection->vendor->sellerPages->first()?->logo ?? null,
            ],
            'title' => $collection->title,
            'subtitle' => $collection->subtitle,
            'date' => $collection->date_range,
            'start_date' => $collection->start_date?->toDateString(),
            'end_date' => $collection->end_date?->toDateString(),
            'products' => $collection->products->take(4)->map(function ($product) {
                return [
                    'id' => $product->id,
                    'slug' => $product->slug,
                    'image' => $product->images->where('is_primary', true)->first()?->url
                        ?? $product->images->first()?->url,
                    'name' => $product->title,
                ];
            })->values()->toArray(),
            'cta' => $collection->cta,
            'badge' => $collection->badge,
            'discount_text' => $collection->discount_text,
            'product_count' => $collection->products->count(),
        ];
    }

    /**
     * Create fallback collections from featured vendors
     * Used when no actual collections exist in database
     */
    private function getFallbackCollections(int $limit): JsonResponse
    {
        $vendors = Vendor::with(['sellerPages', 'products.images'])
            ->where('status', 'active')
            ->whereHas('products')
            ->orderByDesc('rating')
            ->limit($limit)
            ->get();

        // Demo badges and discounts for fallback
        $demoBadges = ['Yeni Koleksiyon', 'Kampanya', 'Özel Fırsat', 'Sezon Sonu', null];
        $demoDiscounts = ['%50\'ye varan indirim', '%30 indirim', 'Sepette %20 indirim', null, null];

        $collections = $vendors->map(function ($vendor, $index) use ($demoBadges, $demoDiscounts) {
            $products = $vendor->products()
                ->with('images')
                ->limit(4)
                ->get();

            if ($products->isEmpty()) {
                return null;
            }

            // Get total product count for vendor
            $totalProductCount = $vendor->products()->count();

            return [
                'id' => $vendor->id, // Use vendor ID as pseudo-collection ID
                'vendor' => [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'slug' => $vendor->slug,
                    'logo' => $vendor->sellerPages->first()?->logo ?? null,
                ],
                'title' => $vendor->name,
                'subtitle' => null,
                'date' => null,
                'start_date' => null,
                'end_date' => null,
                'products' => $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'slug' => $product->slug,
                        'image' => $product->images->where('is_primary', true)->first()?->url
                            ?? $product->images->first()?->url,
                        'name' => $product->title,
                    ];
                })->values()->toArray(),
                'cta' => $vendor->name . ' ürünlerini keşfet',
                'badge' => $demoBadges[$index % count($demoBadges)],
                'discount_text' => $demoDiscounts[$index % count($demoDiscounts)],
                'product_count' => $totalProductCount,
            ];
        })->filter()->values();

        return response()->json([
            'data' => $collections,
        ]);
    }
}
