<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartModalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartModalController extends Controller
{
    protected CartModalService $cartModalService;

    public function __construct(CartModalService $cartModalService)
    {
        $this->cartModalService = $cartModalService;
    }

    /**
     * Cart Modal verisini getir
     * 
     * @response {
     *   "success": true,
     *   "data": {
     *     "product": { "id": 123, "title": "...", "price": 1499 },
     *     "layout": [ ... ],
     *     "variants": { "has_variants": true, "options": [...] },
     *     "sellers": [ ... ],
     *     "stock_warning": { "type": "low_stock", "message": "Son 3 adet!" },
     *     "rules": { "disable_add_until_variant_selected": true }
     *   }
     * }
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::with([
            'variants',
            'images',
            'campaigns',
            'category.categoryGroup',
            'badgeSnapshots',
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

        $modalData = $this->cartModalService->getModalData($product);

        return response()->json([
            'success' => true,
            'data' => $modalData,
        ]);
    }

    /**
     * Varyant kombinasyonunun geçerliliğini kontrol et
     * 
     * Bu endpoint cart modal'da varyant seçimi yapılırken kullanılır
     * Anlık stok ve fiyat kontrolü için
     */
    public function validateVariant(Request $request, string $slug): JsonResponse
    {
        $product = Product::with(['variants'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı',
            ], 404);
        }

        $attributes = $request->input('attributes', []);

        // Varyant bul
        $variant = $product->variants
            ->where('is_active', true)
            ->first(function ($v) use ($attributes) {
                $variantAttrs = $v->attribute_values ?? [];
                foreach ($attributes as $key => $value) {
                    if (($variantAttrs[$key] ?? null) !== $value) {
                        return false;
                    }
                }
                return true;
            });

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Bu kombinasyon mevcut değil',
            ], 400);
        }

        if ($variant->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Bu varyant stokta yok',
                'data' => [
                    'variant_id' => $variant->id,
                    'stock' => 0,
                    'available' => false,
                ],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'variant_id' => $variant->id,
                'price' => $variant->discount_price ?? $variant->price,
                'original_price' => $variant->price,
                'stock' => $variant->stock,
                'available' => true,
                'attributes' => $variant->attribute_values,
            ],
        ]);
    }

    /**
     * Cart modal layout config'ini getir (admin için)
     */
    public function getLayoutConfig(string $slug): JsonResponse
    {
        $product = Product::with('category.categoryGroup')
            ->where('slug', $slug)
            ->first();

        if (!$product || !$product->category?->categoryGroup) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori grubu bulunamadı',
            ], 404);
        }

        $categoryGroup = $product->category->categoryGroup;
        $layout = \App\Models\CartModalLayout::getDefaultForCategoryGroup($categoryGroup->id);
        $blocks = \App\Models\CartModalBlock::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'category_group' => [
                    'id' => $categoryGroup->id,
                    'name' => $categoryGroup->name,
                    'key' => $categoryGroup->key,
                ],
                'layout' => $layout,
                'available_blocks' => $blocks,
            ],
        ]);
    }
}
