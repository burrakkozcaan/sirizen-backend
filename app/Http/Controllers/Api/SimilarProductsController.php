<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SimilarProduct;
use App\Models\RelatedProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SimilarProductsController extends Controller
{
    /**
     * Get similar products for a product.
     * GET /api/products/{productId}/similar
     */
    public function similar(int $productId, Request $request): JsonResponse
    {
        $limit = min($request->integer('limit', 12), 24);

        // Get similar products from database
        $similarProducts = SimilarProduct::where('product_id', $productId)
            ->with(['similarProduct' => fn ($q) => $q->where('is_active', true)
                ->with(['images', 'brand', 'vendor'])])
            ->orderBy('score', 'desc')
            ->limit($limit)
            ->get()
            ->pluck('similarProduct')
            ->filter()
            ->map(function ($product) {
                $variant = $product->variants->first();
                $mainImage = $product->images->first();
                
                return [
                    'id' => $product->id,
                    'name' => $product->title,
                    'slug' => $product->slug,
                    'description' => $product->description ?? '',
                    'brand' => $product->brand ? $product->brand->name : '',
                    'brand_slug' => $product->brand ? $product->brand->slug : '',
                    'category_id' => $product->category_id,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                        'slug' => $product->category->slug,
                        'parent_id' => $product->category->parent_id,
                    ] : null,
                    'vendor_id' => $product->vendor_id,
                    'vendor' => $product->vendor ? [
                        'id' => $product->vendor->id,
                        'name' => $product->vendor->name,
                        'slug' => $product->vendor->slug,
                        'rating' => (float) ($product->vendor->rating ?? 0),
                        'review_count' => (int) ($product->vendor->review_count ?? 0),
                    ] : null,
                    'price' => (float) ($variant?->price ?? 0),
                    'original_price' => $variant?->sale_price && $variant->sale_price < $variant->price 
                        ? (float) $variant->price 
                        : null,
                    'discount_percentage' => $variant && $variant->sale_price && $variant->sale_price < $variant->price
                        ? (int) round((($variant->price - $variant->sale_price) / $variant->price) * 100)
                        : null,
                    'currency' => 'TRY',
                    'images' => $product->images->map(fn ($img) => [
                        'id' => $img->id,
                        'url' => $img->url,
                        'alt' => $product->title,
                        'is_primary' => $img->is_primary ?? ($img->id === $mainImage?->id),
                    ]),
                    'rating' => (float) ($product->rating ?? 0),
                    'review_count' => (int) ($product->reviews_count ?? 0),
                    'question_count' => (int) ($product->questions()->count()),
                    'stock' => (int) ($variant?->stock ?? 0),
                    'is_in_stock' => ($variant?->stock ?? 0) > 0,
                    'is_bestseller' => (bool) ($product->is_bestseller ?? false),
                    'is_new' => (bool) ($product->is_new ?? false),
                    'has_free_shipping' => (bool) ($product->has_free_shipping ?? false),
                    'created_at' => $product->created_at?->toIso8601String() ?? '',
                    'updated_at' => $product->updated_at?->toIso8601String() ?? '',
                ];
            });

        // If not enough similar products, fill with same category products
        if ($similarProducts->count() < $limit) {
            $product = Product::find($productId);
            if ($product && $product->category_id) {
                $categoryProducts = Product::where('category_id', $product->category_id)
                    ->where('id', '!=', $productId)
                    ->where('is_active', true)
                    ->with(['images', 'brand', 'vendor', 'variants', 'category'])
                    ->limit($limit - $similarProducts->count())
                    ->get()
                    ->map(function ($p) {
                        $variant = $p->variants->first();
                        $mainImage = $p->images->first();
                        
                        return [
                            'id' => $p->id,
                            'name' => $p->title,
                            'slug' => $p->slug,
                            'description' => $p->description ?? '',
                            'brand' => $p->brand ? $p->brand->name : '',
                            'brand_slug' => $p->brand ? $p->brand->slug : '',
                            'category_id' => $p->category_id,
                            'category' => $p->category ? [
                                'id' => $p->category->id,
                                'name' => $p->category->name,
                                'slug' => $p->category->slug,
                                'parent_id' => $p->category->parent_id,
                            ] : null,
                            'vendor_id' => $p->vendor_id,
                            'vendor' => $p->vendor ? [
                                'id' => $p->vendor->id,
                                'name' => $p->vendor->name,
                                'slug' => $p->vendor->slug,
                                'rating' => (float) ($p->vendor->rating ?? 0),
                                'review_count' => (int) ($p->vendor->review_count ?? 0),
                            ] : null,
                            'price' => (float) ($variant?->price ?? 0),
                            'original_price' => $variant?->sale_price && $variant->sale_price < $variant->price 
                                ? (float) $variant->price 
                                : null,
                            'discount_percentage' => $variant && $variant->sale_price && $variant->sale_price < $variant->price
                                ? (int) round((($variant->price - $variant->sale_price) / $variant->price) * 100)
                                : null,
                            'currency' => 'TRY',
                            'images' => $p->images->map(fn ($img) => [
                                'id' => $img->id,
                                'url' => $img->url,
                                'alt' => $p->title,
                                'is_primary' => $img->is_primary ?? ($img->id === $mainImage?->id),
                            ]),
                            'rating' => (float) ($p->rating ?? 0),
                            'review_count' => (int) ($p->reviews_count ?? 0),
                            'question_count' => (int) ($p->questions()->count()),
                            'stock' => (int) ($variant?->stock ?? 0),
                            'is_in_stock' => ($variant?->stock ?? 0) > 0,
                            'is_bestseller' => (bool) ($p->is_bestseller ?? false),
                            'is_new' => (bool) ($p->is_new ?? false),
                            'has_free_shipping' => (bool) ($p->has_free_shipping ?? false),
                            'created_at' => $p->created_at?->toIso8601String() ?? '',
                            'updated_at' => $p->updated_at?->toIso8601String() ?? '',
                        ];
                    });

                $similarProducts = $similarProducts->merge($categoryProducts);
            }
        }

        return response()->json([
            'data' => $similarProducts->take($limit)->values(),
        ]);
    }

    /**
     * Get related products (cross-sell, up-sell, also-bought).
     * GET /api/products/{productId}/related?type=cross|up|also_bought
     */
    public function related(int $productId, Request $request): JsonResponse
    {
        $type = $request->input('type', 'cross');
        $limit = min($request->integer('limit', 12), 24);

        $relatedProducts = RelatedProduct::where('product_id', $productId)
            ->where('type', $type)
            ->with(['relatedProduct' => fn ($q) => $q->where('is_active', true)
                ->with(['images', 'brand', 'vendor'])])
            ->orderBy('order')
            ->limit($limit)
            ->get()
            ->pluck('relatedProduct')
            ->filter()
            ->map(function ($product) {
                $variant = $product->variants->first();
                $mainImage = $product->images->first();
                
                return [
                    'id' => $product->id,
                    'name' => $product->title,
                    'slug' => $product->slug,
                    'description' => $product->description ?? '',
                    'brand' => $product->brand ? $product->brand->name : '',
                    'brand_slug' => $product->brand ? $product->brand->slug : '',
                    'category_id' => $product->category_id,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                        'slug' => $product->category->slug,
                        'parent_id' => $product->category->parent_id,
                    ] : null,
                    'vendor_id' => $product->vendor_id,
                    'vendor' => $product->vendor ? [
                        'id' => $product->vendor->id,
                        'name' => $product->vendor->name,
                        'slug' => $product->vendor->slug,
                        'rating' => (float) ($product->vendor->rating ?? 0),
                        'review_count' => (int) ($product->vendor->review_count ?? 0),
                    ] : null,
                    'price' => (float) ($variant?->price ?? 0),
                    'original_price' => $variant?->sale_price && $variant->sale_price < $variant->price 
                        ? (float) $variant->price 
                        : null,
                    'discount_percentage' => $variant && $variant->sale_price && $variant->sale_price < $variant->price
                        ? (int) round((($variant->price - $variant->sale_price) / $variant->price) * 100)
                        : null,
                    'currency' => 'TRY',
                    'images' => $product->images->map(fn ($img) => [
                        'id' => $img->id,
                        'url' => $img->url,
                        'alt' => $product->title,
                        'is_primary' => $img->is_primary ?? ($img->id === $mainImage?->id),
                    ]),
                    'rating' => (float) ($product->rating ?? 0),
                    'review_count' => (int) ($product->reviews_count ?? 0),
                    'question_count' => (int) ($product->questions()->count()),
                    'stock' => (int) ($variant?->stock ?? 0),
                    'is_in_stock' => ($variant?->stock ?? 0) > 0,
                    'is_bestseller' => (bool) ($product->is_bestseller ?? false),
                    'is_new' => (bool) ($product->is_new ?? false),
                    'has_free_shipping' => (bool) ($product->has_free_shipping ?? false),
                    'created_at' => $product->created_at?->toIso8601String() ?? '',
                    'updated_at' => $product->updated_at?->toIso8601String() ?? '',
                ];
            });

        return response()->json([
            'data' => $relatedProducts,
        ]);
    }
}

