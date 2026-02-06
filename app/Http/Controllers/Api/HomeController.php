<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use App\Models\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Get homepage sections with resolved data
     * GET /api/home
     */
    public function index(Request $request): JsonResponse
    {
        $sections = HomeSection::active()
            ->currentlyValid()
            ->ordered()
            ->get();

        // Transform sections and resolve collection data
        $transformedSections = $sections->map(function ($section) {
            $data = [
                'id' => $section->id,
                'position' => $section->position,
                'section_type' => $section->section_type,
                'config_json' => $section->config_json,
                'is_active' => $section->is_active,
            ];

            // If section has collection IDs, resolve them to full collection data
            if ($section->section_type === 'vendor_collection_grid' &&
                isset($section->config_json['collections']) &&
                is_array($section->config_json['collections'])) {

                $collectionIds = $section->config_json['collections'];

                // Check if these are IDs (numbers) or already objects
                if (!empty($collectionIds) && is_numeric($collectionIds[0])) {
                    $collections = $this->resolveCollections($collectionIds);
                    $data['config_json']['collections'] = $collections;
                }
            }

            return $data;
        });

        return response()->json([
            'data' => $transformedSections,
        ]);
    }

    /**
     * Resolve collection IDs to full collection data with vendor and products
     */
    private function resolveCollections(array $collectionIds): array
    {
        $collections = Collection::with(['vendor', 'products.images'])
            ->whereIn('id', $collectionIds)
            ->active()
            ->currentlyValid()
            ->get();

        return $collections->map(function ($collection) {
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
            ];
        })->toArray();
    }
}
