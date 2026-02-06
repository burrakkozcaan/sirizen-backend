<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\FilterService;
use App\Services\PDPService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    protected PDPService $pdpService;
    protected FilterService $filterService;

    public function __construct(
        PDPService $pdpService,
        FilterService $filterService
    ) {
        $this->pdpService = $pdpService;
        $this->filterService = $filterService;
    }

    /**
     * Ürün detay sayfası (PDP Engine ile)
     */
    public function show(string $slug): Response
    {
        $product = Product::with([
            'category',
            'brand',
            'images',
            'variants',
            'attributeValues.attribute',
            'vendor',
            'reviews' => fn ($q) => $q->limit(5),
            'questions' => fn ($q) => $q->where('is_approved', true)->limit(5),
        ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // PDP verilerini hazırla
        $pdpData = $this->pdpService->getProductData($product);

        // Benzer ürünleri getir
        $relatedProducts = $this->getRelatedProducts($product);

        // Son görüntülenen ürünlere ekle
        $this->trackRecentlyViewed($product);

        return Inertia::render('Public/Product/Show', [
            'product' => $pdpData['product'],
            'layout' => $pdpData['layout'],
            'badges' => $pdpData['badges'],
            'highlights' => $pdpData['highlights'],
            'social_proof' => $pdpData['social_proof'],
            'related_products' => $relatedProducts,
            'meta' => [
                'title' => $product->meta_title ?? $product->title,
                'description' => $product->meta_description ?? $product->short_description,
            ],
        ]);
    }

    /**
     * PDP API - AJAX için
     */
    public function apiShow(string $slug)
    {
        $product = Product::with([
            'category',
            'brand',
            'images',
            'variants',
            'attributeValues.attribute',
        ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $pdpData = $this->pdpService->getProductData($product);

        return response()->json([
            'success' => true,
            'data' => $pdpData,
        ]);
    }

    /**
     * Varyant detayını getir
     */
    public function getVariant(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $variantId = $request->input('variant_id');
        $attributes = $request->input('attributes', []);

        if ($variantId) {
            $variant = $product->variants()
                ->where('id', $variantId)
                ->where('is_active', true)
                ->first();
        } elseif (!empty($attributes)) {
            // Attribute'lere göre varyant bul
            $variant = $product->variants()
                ->where('is_active', true)
                ->get()
                ->first(function ($v) use ($attributes) {
                    $variantAttrs = $v->attribute_values ?? [];
                    foreach ($attributes as $key => $value) {
                        if (($variantAttrs[$key] ?? null) !== $value) {
                            return false;
                        }
                    }
                    return true;
                });
        }

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Varyant bulunamadı',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $variant->id,
                'title' => $variant->title,
                'price' => $variant->price,
                'discount_price' => $variant->discount_price,
                'stock' => $variant->stock,
                'attributes' => $variant->attribute_values,
                'image' => $variant->image?->url,
            ],
        ]);
    }

    /**
     * Ürün için sosyal kanıt verisini getir (real-time)
     */
    public function getSocialProof(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $socialProof = $this->pdpService->getSocialProof($product);

        return response()->json([
            'success' => true,
            'data' => $socialProof,
        ]);
    }

    /**
     * Benzer ürünleri getir
     */
    protected function getRelatedProducts(Product $product, int $limit = 8): array
    {
        $related = Product::with(['brand', 'images', 'category'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit($limit)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'slug' => $p->slug,
                'price' => $p->price,
                'discount_price' => $p->discount_price,
                'image' => $p->images->first()?->url,
                'brand' => $p->brand?->name,
                'rating' => $p->rating,
                'reviews_count' => $p->reviews_count,
            ])
            ->toArray();

        return $related;
    }

    /**
     * Son görüntülenen ürünleri takip et
     */
    protected function trackRecentlyViewed(Product $product): void
    {
        $recentlyViewed = session()->get('recently_viewed', []);

        // Ürünü listeden kaldır (tekrar eklemek için)
        $recentlyViewed = array_diff($recentlyViewed, [$product->id]);

        // Başa ekle
        array_unshift($recentlyViewed, $product->id);

        // Son 20 ürünü tut
        $recentlyViewed = array_slice($recentlyViewed, 0, 20);

        session()->put('recently_viewed', $recentlyViewed);
    }

    /**
     * Son görüntülenen ürünleri getir
     */
    public function getRecentlyViewed()
    {
        $ids = session()->get('recently_viewed', []);

        if (empty($ids)) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $products = Product::with(['brand', 'images'])
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->get()
            ->sortBy(fn ($p) => array_search($p->id, $ids))
            ->map(fn ($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'slug' => $p->slug,
                'price' => $p->price,
                'discount_price' => $p->discount_price,
                'image' => $p->images->first()?->url,
                'brand' => $p->brand?->name,
            ])
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
