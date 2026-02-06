<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Services\FilterService;
use Illuminate\Http\JsonResponse;

class FilterController extends Controller
{
    public function __construct(
        protected FilterService $filterService
    ) {}

    /**
     * Kategori slug'ına göre filtreleri getir
     * GET /api/filters/category/{slug}
     */
    public function byCategory(string $slug): JsonResponse
    {
        $category = Category::with(['children', 'categoryGroup'])
            ->where('slug', $slug)
            ->firstOrFail();

        $filters = $this->filterService->getFiltersForCategory($category);

        // null değerleri filtrele
        $filters = array_values(array_filter($filters));

        return response()->json([
            'filters' => $filters,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'category_group_id' => $category->category_group_id,
            ],
        ]);
    }

    /**
     * Kategori grubu ID'sine göre filtreleri getir
     * GET /api/filters/category-group/{id}
     */
    public function byCategoryGroup(int $id): JsonResponse
    {
        $categoryGroup = CategoryGroup::findOrFail($id);

        // Bu grubun herhangi bir kategorisini kullanarak filtreleri al
        $category = Category::where('category_group_id', $id)->first();

        if (!$category) {
            return response()->json([
                'filters' => [],
                'category_group' => [
                    'id' => $categoryGroup->id,
                    'key' => $categoryGroup->key,
                    'name' => $categoryGroup->name,
                ],
            ]);
        }

        $filters = $this->filterService->getFiltersForCategory($category);
        $filters = array_values(array_filter($filters));

        return response()->json([
            'filters' => $filters,
            'category_group' => [
                'id' => $categoryGroup->id,
                'key' => $categoryGroup->key,
                'name' => $categoryGroup->name,
            ],
        ]);
    }

    /**
     * Kampanya slug'ına göre filtreleri getir
     * GET /api/filters/campaign/{slug}
     */
    public function byCampaign(string $slug): JsonResponse
    {
        $campaign = Campaign::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Kampanya ürünlerinin kategorilerini bul
        $categoryIds = $campaign->products()
            ->distinct()
            ->pluck('category_id')
            ->filter()
            ->values();

        if ($categoryIds->isEmpty()) {
            // Kampanyada ürün yoksa default filtreler
            return response()->json([
                'filters' => $this->getDefaultCampaignFilters(),
                'campaign' => [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'slug' => $campaign->slug,
                ],
            ]);
        }

        // En çok ürün olan kategorinin grubunu bul
        $mainCategory = Category::whereIn('id', $categoryIds)
            ->withCount(['products' => fn ($q) => $q->whereIn('id', $campaign->products()->pluck('products.id'))])
            ->orderByDesc('products_count')
            ->first();

        if ($mainCategory) {
            $filters = $this->filterService->getFiltersForCategory($mainCategory);
        } else {
            $filters = $this->getDefaultCampaignFilters();
        }

        $filters = array_values(array_filter($filters));

        return response()->json([
            'filters' => $filters,
            'campaign' => [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'slug' => $campaign->slug,
            ],
        ]);
    }

    /**
     * Kampanyalar için default filtreler
     */
    protected function getDefaultCampaignFilters(): array
    {
        return [
            [
                'key' => 'price',
                'label' => 'Fiyat',
                'type' => 'range',
                'filter_type' => 'price',
                'is_collapsed' => false,
                'show_count' => false,
                'min' => 0,
                'max' => 10000,
                'step' => 50,
                'unit' => 'TL',
            ],
            [
                'key' => 'brand',
                'label' => 'Marka',
                'type' => 'checkbox',
                'filter_type' => 'brand',
                'is_collapsed' => false,
                'show_count' => true,
                'options' => [],
            ],
            [
                'key' => 'rating',
                'label' => 'Değerlendirme',
                'type' => 'rating',
                'filter_type' => 'rating',
                'is_collapsed' => true,
                'show_count' => false,
            ],
            [
                'key' => 'shipping',
                'label' => 'Kargo',
                'type' => 'checkbox',
                'filter_type' => 'shipping',
                'is_collapsed' => false,
                'show_count' => false,
                'options' => [
                    ['value' => 'free_shipping', 'label' => 'Ücretsiz Kargo'],
                    ['value' => 'fast_delivery', 'label' => 'Hızlı Teslimat'],
                ],
            ],
        ];
    }
}
