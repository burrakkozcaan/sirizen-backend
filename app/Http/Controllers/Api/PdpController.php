<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\PDPService;
use App\Services\UnifiedPdpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PdpController extends Controller
{
    protected PDPService $pdpService;
    protected UnifiedPdpService $unifiedPdpService;

    public function __construct(PDPService $pdpService, UnifiedPdpService $unifiedPdpService)
    {
        $this->pdpService = $pdpService;
        $this->unifiedPdpService = $unifiedPdpService;
    }

    /**
     * UNIFIED PDP API (Trendyol-style)
     *
     * GET /api/pdp/{slug}?context=page|cart|modal|quickview
     *
     * Frontend ASLA karar vermez. Backend block listesi ne diyorsa o render edilir.
     *
     * @queryParam context string Context türü: page, cart, modal, quickview. Default: page
     * @queryParam variant_id int Seçili varyant ID'si
     * @queryParam seller_id int Seçili satıcı ID'si
     *
     * @response {
     *   "success": true,
     *   "data": {
     *     "meta": { "context": "page", "category_group": "giyim", ... },
     *     "product": { ... },
     *     "pricing": { ... },
     *     "variants": { "enabled": true, "attributes": [...], "combinations": [...] },
     *     "vendors": [...],
     *     "badges": [...],
     *     "campaigns": [...],
     *     "blocks": [...],
     *     "rules": { ... },
     *     "social_proof": { ... }
     *   }
     * }
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        // Context parametresi
        $context = $request->input('context', 'page');
        $validContexts = ['page', 'cart', 'modal', 'quickview'];
        if (!in_array($context, $validContexts)) {
            $context = 'page';
        }

        // Options
        $options = [
            'variant_id' => $request->input('variant_id'),
            'seller_id' => $request->input('seller_id'),
            'selected_attributes' => $request->input('attributes', []),
        ];

        // Cache key
        $cacheKey = "pdp:{$slug}:{$context}:" . md5(json_encode($options));
        $cacheTtl = config('pdp.cache.pdp_ttl', 60);

        // Cache'den al veya oluştur
        $pdpData = Cache::remember($cacheKey, $cacheTtl, function () use ($slug, $context, $options) {
            $product = Product::with([
                'category.categoryGroup',
                'categoryGroup',
                'brand',
                'images',
                'variants',
                'attributeValues.attribute',
                'vendor',
                'productSellers.vendor',
                'campaigns',
                'reviews' => fn($q) => $q->limit(5),
                'questions' => fn($q) => $q->where('is_approved', true)->limit(5),
            ])
                ->where('slug', $slug)
                ->where('is_active', true)
                ->first();

            if (!$product) {
                return null;
            }

            return $this->unifiedPdpService->build($product, $context, $options);
        });

        if (!$pdpData) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı',
            ], 404);
        }

        // View tracking (sadece page context'te)
        if ($context === 'page') {
            $this->trackView($slug);
        }

        return response()->json([
            'success' => true,
            'data' => $pdpData,
        ]);
    }

    /**
     * Legacy endpoint (geriye dönük uyumluluk)
     * Yeni endpoint'e yönlendirir
     */
    public function showLegacy(string $slug): JsonResponse
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
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı',
            ], 404);
        }

        // PDP verilerini hazırla (eski format)
        $pdpData = $this->pdpService->getProductData($product);

        // İzleme
        $this->trackView($slug);

        return response()->json([
            'success' => true,
            'data' => $pdpData,
        ]);
    }

    /**
     * Varyant detayını getir
     */
    public function getVariant(Request $request, string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı',
            ], 404);
        }

        $variantId = $request->input('variant_id');
        $attributes = $request->input('attributes', []);

        if ($variantId) {
            $variant = $product->variants()
                ->where('id', $variantId)
                ->where('is_active', true)
                ->first();
        } elseif (!empty($attributes)) {
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
                'barcode' => $variant->barcode,
            ],
        ]);
    }

    /**
     * Sosyal kanıt verisini getir (real-time)
     */
    public function getSocialProof(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı',
            ], 404);
        }

        $socialProof = $this->pdpService->getSocialProof($product);

        return response()->json([
            'success' => true,
            'data' => $socialProof,
        ]);
    }

    /**
     * Ürün yorumlarını getir
     */
    public function getReviews(Request $request, string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı',
            ], 404);
        }

        $reviews = $product->reviews()
            ->with('user')
            ->where('is_approved', true)
            ->latest()
            ->paginate($request->input('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    /**
     * Ürün sorularını getir
     */
    public function getQuestions(Request $request, string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı',
            ], 404);
        }

        $questions = $product->questions()
            ->with(['user', 'vendor'])
            ->where('is_approved', true)
            ->latest()
            ->paginate($request->input('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $questions,
        ]);
    }

    /**
     * Benzer ürünleri getir
     */
    public function getRelatedProducts(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı',
            ], 404);
        }

        $related = Product::with(['brand', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(8)
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
                'badges' => $p->badgeSnapshots->map(fn ($b) => [
                    'key' => $b->badgeDefinition?->key,
                    'label' => $b->label,
                    'color' => $b->color,
                ]),
            ]);

        return response()->json([
            'success' => true,
            'data' => $related,
        ]);
    }

    /**
     * Sadece badge'leri getir
     */
    public function badges(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı',
            ], 404);
        }

        $badges = $this->pdpService->formatForApi(
            $this->pdpService->getBadges($product)
        );

        return response()->json([
            'success' => true,
            'data' => $badges,
        ]);
    }

    /**
     * Sadece highlight'ları getir
     */
    public function highlights(string $slug): JsonResponse
    {
        $product = Product::with('attributeValues.attribute')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı',
            ], 404);
        }

        $highlights = $this->pdpService->getHighlightAttributes($product);

        return response()->json([
            'success' => true,
            'data' => $highlights,
        ]);
    }

    /**
     * Sosyal kanıt - alias (route uyumluluğu için)
     */
    public function socialProof(string $slug): JsonResponse
    {
        return $this->getSocialProof($slug);
    }

    /**
     * Badge'leri yeniden hesapla (admin/debug için)
     */
    public function recalculateBadges(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı',
            ], 404);
        }

        $badges = $this->pdpService->calculateBadges($product, true);

        return response()->json([
            'success' => true,
            'message' => 'Badge\'ler yeniden hesaplandı',
            'data' => $this->pdpService->formatForApi($badges),
        ]);
    }

    /**
     * Görüntülenme takibi
     */
    protected function trackView(string $slug): void
    {
        // View tracking disabled - column doesn't exist yet
        // TODO: Add view_count migration or use analytics service
        // Product::where('slug', $slug)->increment('view_count');
    }
}
