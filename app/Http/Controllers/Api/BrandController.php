<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Get all brands.
     */
    public function index(): JsonResponse
    {
        $brands = Brand::orderBy('name')
            ->get()
            ->map(fn ($brand) => [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'logo' => $brand->logo,
                'description' => $brand->description ?? null,
                'product_count' => $brand->products()->where('is_active', true)->count(),
            ]);

        return response()->json([
            'data' => $brands,
        ]);
    }

    /**
     * Get single brand by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $brand = Brand::where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'logo' => $brand->logo,
                'description' => $brand->description,
                'product_count' => $brand->products()->where('is_active', true)->count(),
            ],
        ]);
    }

    /**
     * Get products by brand.
     */
    public function products(string $slug, Request $request): JsonResponse
    {
        $brand = Brand::where('slug', $slug)->firstOrFail();

        $query = $brand->products()
            ->with(['vendor', 'category', 'images'])
            ->where('is_active', true);

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $allowedSorts = ['created_at', 'price', 'rating', 'sales_count'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = min($request->input('per_page', 24), 100);
        $products = $query->paginate($perPage);

        return response()->json($products);
    }
}

