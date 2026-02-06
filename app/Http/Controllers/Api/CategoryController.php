<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\FilterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected FilterService $filterService;

    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    /**
     * Tüm ana kategorileri listele
     */
    public function index(): JsonResponse
    {
        $categories = Category::with(['children' => function ($query) {
                $query->orderBy('order');
            }])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get()
            ->map(fn ($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'icon' => $category->icon,
                'image' => $category->image,
                'category_group_id' => $category->category_group_id,
                'children' => $category->children->map(fn ($child) => [
                    'id' => $child->id,
                    'name' => $child->name,
                    'slug' => $child->slug,
                    'icon' => $child->icon,
                    'image' => $child->image,
                    'category_group_id' => $child->category_group_id,
                ]),
            ]);

        return response()->json([
            'data' => $categories,
        ]);
    }

    /**
     * Kategori detayı ve ürünleri
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $category = Category::with(['children.children', 'categoryGroup'])
            ->where('slug', $slug)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori bulunamadı',
            ], 404);
        }

        // Filtreleri getir
        $filters = $this->filterService->getFiltersForCategory($category);

        // Kategori ve alt kategorilerinin ID'lerini al
        $categoryIds = collect([$category->id]);
        if ($category->children->isNotEmpty()) {
            $categoryIds = $categoryIds->merge($category->children->pluck('id'));
            // Alt kategorilerin de alt kategorilerini ekle (2 seviye)
            foreach ($category->children as $child) {
                if ($child->children) {
                    $categoryIds = $categoryIds->merge($child->children->pluck('id'));
                }
            }
        }

        // Ürün sorgusu - kategori ve alt kategorilerden
        $query = Product::whereIn('category_id', $categoryIds->unique())
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
        $products = $query->paginate($limit);

        // Formatla
        $formattedProducts = $products->through(fn ($product) => [
            'id' => $product->id,
            'title' => $product->title,
            'slug' => $product->slug,
            'price' => $product->price,
            'discount_price' => $product->discount_price,
            'discount_percentage' => $product->discount_percentage,
            'image' => $product->images->first()?->url,
            'images' => $product->images->map(fn ($img) => [
                'id' => $img->id,
                'url' => $img->url,
                'alt' => $img->alt,
            ])->values(),
            'brand' => $product->brand?->name,
            'rating' => $product->rating,
            'reviews_count' => $product->reviews_count,
            'badges' => $product->badgeSnapshots->map(fn ($b) => [
                'key' => $b->badgeDefinition?->key,
                'label' => $b->label,
                'color' => $b->color,
                'bg_color' => $b->bg_color,
            ]),
            'fast_delivery' => $product->fast_delivery ?? false,
            'is_new' => $product->is_new ?? false,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                ],
                'products' => $formattedProducts,
                'filters' => $filters,
                'sub_categories' => $category->children
                    ->map(fn ($c) => [
                        'id' => $c->id,
                        'name' => $c->name,
                        'slug' => $c->slug,
                    ])
                    ->values(),
                'breadcrumbs' => $this->buildBreadcrumbs($category),
                'sort_options' => $this->getSortOptions(),
            ],
        ]);
    }

    /**
     * Kategori ürünlerini getir (ayrı endpoint)
     */
    public function products(Request $request, string $slug): JsonResponse
    {
        $category = Category::with(['children.children', 'categoryGroup'])
            ->where('slug', $slug)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori bulunamadı',
            ], 404);
        }

        // Kategori ve alt kategorilerinin ID'lerini al
        $categoryIds = collect([$category->id]);
        if ($category->children->isNotEmpty()) {
            $categoryIds = $categoryIds->merge($category->children->pluck('id'));
            foreach ($category->children as $child) {
                if ($child->children) {
                    $categoryIds = $categoryIds->merge($child->children->pluck('id'));
                }
            }
        }

        // Ürün sorgusu
        $query = Product::whereIn('category_id', $categoryIds->unique())
            ->with(['brand', 'images', 'badgeSnapshots'])
            ->where('is_active', true);

        // Filtreleri uygula
        $appliedFilters = [];
        
        // Brand filtresini özel olarak işle (hem name hem slug destekle)
        if ($request->has('brand') || $request->has('brand[]')) {
            $brandValues = $request->input('brand[]', $request->input('brand', []));
            if (!is_array($brandValues)) {
                $brandValues = [$brandValues];
            }
            
            if (!empty($brandValues)) {
                $query->whereHas('brand', function ($q) use ($brandValues) {
                    $q->where(function ($subQuery) use ($brandValues) {
                        foreach ($brandValues as $brandValue) {
                            $subQuery->orWhere('name', $brandValue)
                                    ->orWhere('slug', $brandValue)
                                    ->orWhere('name', 'LIKE', '%' . $brandValue . '%');
                        }
                    });
                });
            }
        }
        
        // Fiyat filtresi (min_price ve max_price -> price array)
        if ($request->has('min_price') || $request->has('max_price')) {
            $appliedFilters['price'] = [];
            if ($request->has('min_price')) {
                $appliedFilters['price']['min'] = (float) $request->input('min_price');
            }
            if ($request->has('max_price')) {
                $appliedFilters['price']['max'] = (float) $request->input('max_price');
            }
        }
        
        // Ücretsiz kargo filtresi (free_shipping -> shipping)
        if ($request->input('free_shipping') === 'true') {
            $appliedFilters['shipping'] = ['free'];
        }
        
        // Stok durumu filtresi (in_stock)
        if ($request->input('in_stock') === 'true') {
            $query->where('stock_quantity', '>', 0);
        }
        
        // Rating filtresi
        if ($request->has('rating')) {
            $appliedFilters['rating'] = (float) $request->input('rating');
        }
        
        // Diğer filtreleri FilterService ile uygula
        if (!empty($appliedFilters)) {
            $query = $this->filterService->applyFilters($query, $appliedFilters, $category);
        }

        // Sıralama
        $sort = $request->input('sort_by', $request->input('sort', 'popular'));
        $query = $this->applySorting($query, $sort);

        // Sayfalama
        $perPage = $request->input('per_page', $request->input('limit', 24));
        $page = $request->input('page', 1);
        $products = $query->paginate($perPage, ['*'], 'page', $page);

        // Formatla
        $formattedProducts = $products->through(fn ($product) => [
            'id' => $product->id,
            'title' => $product->title,
            'slug' => $product->slug,
            'price' => $product->price,
            'discount_price' => $product->discount_price,
            'discount_percentage' => $product->discount_percentage,
            'image' => $product->images->first()?->url,
            'images' => $product->images->map(fn ($img) => [
                'id' => $img->id,
                'url' => $img->url,
                'alt' => $img->alt,
            ])->values(),
            'brand' => $product->brand?->name,
            'rating' => $product->rating,
            'reviews_count' => $product->reviews_count,
            'badges' => $product->badgeSnapshots->map(fn ($b) => [
                'key' => $b->badgeDefinition?->key,
                'label' => $b->label,
                'color' => $b->color,
                'bg_color' => $b->bg_color,
            ]),
            'fast_delivery' => $product->fast_delivery ?? false,
            'is_new' => $product->is_new ?? false,
        ]);

        return response()->json([
            'data' => $formattedProducts->items(),
            'meta' => [
                'total' => $products->total(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
            ],
        ]);
    }

    /**
     * Sadece filtreleri getir
     */
    public function getFilters(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori bulunamadı',
            ], 404);
        }

        $filters = $this->filterService->getFiltersForCategory($category);

        return response()->json([
            'success' => true,
            'data' => $filters,
        ]);
    }

    /**
     * Tüm kategoriler (tree)
     */
    public function getTree(): JsonResponse
    {
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get()
            ->map(fn ($c) => $this->formatCategoryTree($c));

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Kategori tree formatla
     */
    protected function formatCategoryTree(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'icon' => $category->icon,
            'children' => $category->children
                ->map(fn ($c) => $this->formatCategoryTree($c))
                ->values(),
        ];
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
