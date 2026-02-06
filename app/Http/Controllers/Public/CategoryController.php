<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\FilterService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    protected FilterService $filterService;

    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    /**
     * Kategori sayfası - ürün listesi ve filtreler
     */
    public function show(Request $request, string $slug): Response
    {
        $category = Category::with(['children', 'categoryGroup'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Filtreleri getir (Trendyol stili)
        $filters = $this->filterService->getFiltersForCategory($category);

        // Ürün sorgusunu başlat
        $query = $category->products()
            ->with(['brand', 'images', 'badgeSnapshots'])
            ->where('is_active', true);

        // Filtreleri uygula
        $appliedFilters = $request->except(['page', 'sort', 'limit']);
        if (!empty($appliedFilters)) {
            $query = $this->filterService->applyFilters($query, $appliedFilters, $category);
        }

        // Sıralama
        $sort = $request->input('sort', 'popular');
        $query = $this->applySorting($query, $sort);

        // Sayfalama
        $limit = $request->input('limit', 24);
        $products = $query->paginate($limit)->withQueryString();

        // Ürünleri formatla
        $formattedProducts = $products->through(fn ($product) => [
            'id' => $product->id,
            'title' => $product->title,
            'slug' => $product->slug,
            'price' => $product->price,
            'discount_price' => $product->discount_price,
            'discount_percentage' => $product->discount_percentage,
            'image' => $product->images->first()?->url,
            'brand' => $product->brand?->name,
            'rating' => $product->rating,
            'reviews_count' => $product->reviews_count,
            'badges' => $product->badgeSnapshots->map(fn ($b) => [
                'key' => $b->badgeDefinition?->key,
                'label' => $b->label,
                'color' => $b->color,
                'bg_color' => $b->bg_color,
            ]),
            'fast_delivery' => $product->fast_delivery,
        ]);

        // Alt kategoriler
        $subCategories = $category->children
            ->where('is_active', true)
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'product_count' => $c->products()->where('is_active', true)->count(),
            ])
            ->values();

        // Breadcrumb
        $breadcrumbs = $this->buildBreadcrumbs($category);

        return Inertia::render('Public/Category/Show', [
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'meta_title' => $category->meta_title,
                'meta_description' => $category->meta_description,
            ],
            'products' => $formattedProducts,
            'filters' => $filters,
            'sub_categories' => $subCategories,
            'breadcrumbs' => $breadcrumbs,
            'applied_filters' => $appliedFilters,
            'sort_options' => $this->getSortOptions(),
            'current_sort' => $sort,
            'meta' => [
                'title' => $category->meta_title ?? $category->name,
                'description' => $category->meta_description ?? $category->description,
            ],
        ]);
    }

    /**
     * Kategori filtre API'si
     */
    public function getFilters(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $filters = $this->filterService->getFiltersForCategory($category);

        return response()->json([
            'success' => true,
            'data' => $filters,
        ]);
    }

    /**
     * Ürün sayısını getir (filtre seçildiğinde sayıları güncellemek için)
     */
    public function getProductCounts(Request $request, string $slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $query = $category->products()->where('is_active', true);

        // Mevcut filtreyi hariç tutarak say
        $currentFilter = $request->input('filter_key');
        $appliedFilters = collect($request->except(['filter_key']))
            ->reject(fn ($_, $key) => $key === $currentFilter || $key === 'filter_key')
            ->toArray();

        if (!empty($appliedFilters)) {
            $query = $this->filterService->applyFilters($query, $appliedFilters, $category);
        }

        // Filtre seçeneklerinin sayılarını hesapla
        // Bu kısım filtre tipine göre değişir

        return response()->json([
            'success' => true,
            'data' => [
                'total_count' => $query->count(),
            ],
        ]);
    }

    /**
     * Sıralama uygula
     */
    protected function applySorting($query, string $sort)
    {
        return match ($sort) {
            'price_asc' => $query->orderByRaw('COALESCE(discount_price, price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(discount_price, price) DESC'),
            'newest' => $query->latest('created_at'),
            'best_seller' => $query->orderByDesc('sold_count'),
            'most_reviewed' => $query->orderByDesc('reviews_count'),
            'rating' => $query->orderByDesc('rating'),
            default => $query->orderByDesc('is_bestseller')
                ->orderByDesc('rating')
                ->orderByDesc('reviews_count'),
        };
    }

    /**
     * Sıralama seçenekleri
     */
    protected function getSortOptions(): array
    {
        return [
            ['key' => 'popular', 'label' => 'Önerilen Sıralama'],
            ['key' => 'newest', 'label' => 'En Yeniler'],
            ['key' => 'best_seller', 'label' => 'En Çok Satanlar'],
            ['key' => 'price_asc', 'label' => 'En Düşük Fiyat'],
            ['key' => 'price_desc', 'label' => 'En Yüksek Fiyat'],
            ['key' => 'most_reviewed', 'label' => 'En Çok Değerlendirilenler'],
            ['key' => 'rating', 'label' => 'En Yüksek Puanlılar'],
        ];
    }

    /**
     * Breadcrumb oluştur
     */
    protected function buildBreadcrumbs(Category $category): array
    {
        $breadcrumbs = [];

        // Parent kategorileri ekle
        $current = $category;
        while ($current) {
            array_unshift($breadcrumbs, [
                'name' => $current->name,
                'slug' => $current->slug,
            ]);
            $current = $current->parent;
        }

        return $breadcrumbs;
    }
}
