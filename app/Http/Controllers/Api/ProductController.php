<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Models\ProductQuestion;
use App\Models\ProductReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get paginated list of products.
     * Supports filtering, sorting, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()
            ->with(['vendor', 'category', 'brand', 'images'])
            ->where('is_active', true);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by brand
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by vendor
        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter bestsellers
        if ($request->boolean('is_bestseller')) {
            $query->where('is_bestseller', true);
        }

        // Filter new products
        if ($request->boolean('is_new')) {
            $query->where('is_new', true);
        }

        // Filter free shipping
        if ($request->boolean('has_free_shipping')) {
            $query->where('has_free_shipping', true);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $allowedSorts = ['created_at', 'price', 'rating', 'sales_count', 'view_count'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = min($request->input('per_page', 24), 100);
        $products = $query->paginate($perPage);

        return response()->json($products);
    }

    /**
     * Get single product by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::with([
            'vendor',
            'category',
            'brand',
            'images',
            'variants.attributeValues',
            'productSellers.vendor',
            'safetyImages',
            'safetyDocuments',
        ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Increment view count
        $product->increment('view_count');

        return response()->json(new ProductResource($product));
    }

    /**
     * Get product reviews.
     */
    public function reviews(int $id, Request $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 10), 50);

        $reviews = ProductReview::where('product_id', $id)
            ->with(['user', 'images', 'helpfulVotes'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($reviews);
    }

    /**
     * Get product questions & answers.
     */
    public function questions(int $id, Request $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 10), 50);

        $questions = ProductQuestion::where('product_id', $id)
            ->with(['user', 'answers.user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($questions);
    }

    /**
     * Get bestseller products.
     */
    public function bestsellers(Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 12), 100);
        
        $products = Product::query()
            ->with(['vendor', 'category', 'brand', 'images'])
            ->where('is_active', true)
            ->where('is_bestseller', true)
            ->orderBy('rating', 'desc')
            ->orderBy('reviews_count', 'desc')
            ->limit($limit)
            ->get();

        return response()->json(['data' => ProductResource::collection($products)]);
    }

    /**
     * Get new arrival products.
     */
    public function newArrivals(Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 12), 100);
        
        $products = Product::query()
            ->with(['vendor', 'category', 'brand', 'images'])
            ->where('is_active', true)
            ->where('is_new', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json(['data' => ProductResource::collection($products)]);
    }

    /**
     * Get "Buy More Save More" products (products with quantity discounts).
     */
    public function buyMoreSaveMore(Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 12), 100);
        
        // Get products with discounts or bestsellers (can be customized based on business logic)
        $products = Product::query()
            ->with(['vendor', 'category', 'brand', 'images'])
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNotNull('discount_price')
                  ->where('discount_price', '>', 0)
                  ->orWhere('is_bestseller', true);
            })
            ->orderByRaw('CASE WHEN discount_price > 0 AND original_price > 0 THEN ((original_price - discount_price) / original_price * 100) ELSE 0 END DESC')
            ->orderBy('rating', 'desc')
            ->limit($limit)
            ->get();

        return response()->json(['data' => ProductResource::collection($products)]);
    }

    /**
     * Get recommended products (mixed bestsellers, new arrivals, and high-rated products).
     */
    public function recommended(Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 12), 100);

        // Get a mix of bestsellers, new arrivals, and high-rated products
        $products = Product::query()
            ->with(['vendor', 'category', 'brand', 'images'])
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('is_bestseller', true)
                  ->orWhere('is_new', true)
                  ->orWhere('rating', '>=', 4.0);
            })
            ->orderByRaw('CASE
                WHEN is_bestseller = true THEN 1
                WHEN is_new = true THEN 2
                ELSE 3
            END')
            ->orderBy('rating', 'desc')
            ->orderBy('reviews_count', 'desc')
            ->limit($limit)
            ->get();

        return response()->json(['data' => ProductResource::collection($products)]);
    }

    /**
     * Get flash sale products (products with significant discounts ending soon).
     */
    public function flashSales(Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 12), 100);

        $products = Product::query()
            ->with(['vendor', 'category', 'brand', 'images'])
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNotNull('discount_price')
                  ->where('discount_price', '>', 0)
                  ->whereColumn('discount_price', '<', 'original_price');
            })
            ->orderByRaw('((original_price - discount_price) / original_price * 100) DESC')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json(['data' => ProductResource::collection($products)]);
    }

    /**
     * Get product bundles for a product (Trendyol "Birlikte Al" mantığı)
     */
    public function bundles(int $id): JsonResponse
    {
        $bundles = ProductBundle::where('main_product_id', $id)
            ->where('is_active', true)
            ->with([
                'mainProduct:id,title,slug,price,images',
                'products:id,title,slug,price,original_price,images,brand_id,vendor_id',
                'products.brand:id,name,slug',
                'products.vendor:id,name,slug',
            ])
            ->get()
            ->map(function ($bundle) {
                // Bundle fiyatlarını hesapla
                $products = $bundle->products;
                $totalPrice = $products->sum('price');
                $bundlePrice = $totalPrice;
                
                // İndirim varsa uygula
                if ($bundle->discount_rate > 0) {
                    $bundlePrice = $totalPrice * (1 - ($bundle->discount_rate / 100));
                }
                
                $savings = $totalPrice - $bundlePrice;

                return [
                    'id' => $bundle->id,
                    'main_product_id' => $bundle->main_product_id,
                    'title' => $bundle->title,
                    'bundle_type' => $bundle->bundle_type,
                    'discount_rate' => (float) $bundle->discount_rate,
                    'is_active' => $bundle->is_active,
                    'products' => $products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'title' => $product->title,
                            'slug' => $product->slug,
                            'price' => (float) $product->price,
                            'original_price' => $product->original_price ? (float) $product->original_price : null,
                            'images' => $product->images->map(fn($img) => [
                                'id' => $img->id,
                                'url' => $img->url,
                                'is_primary' => $img->is_primary ?? false,
                            ])->toArray(),
                            'brand' => $product->brand ? [
                                'id' => $product->brand->id,
                                'name' => $product->brand->name,
                                'slug' => $product->brand->slug,
                            ] : null,
                            'vendor' => $product->vendor ? [
                                'id' => $product->vendor->id,
                                'name' => $product->vendor->name,
                                'slug' => $product->vendor->slug,
                            ] : null,
                        ];
                    })->toArray(),
                    'total_price' => round($totalPrice, 2),
                    'bundle_price' => round($bundlePrice, 2),
                    'savings' => round($savings, 2),
                ];
            });

        return response()->json(['data' => $bundles]);
    }
}
