<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SearchLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search products.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q');

        if (! $query || strlen($query) < 2) {
            return response()->json([
                'message' => 'Lütfen en az 2 karakter giriniz.',
                'products' => [],
            ], 400);
        }

        // Log search if user is authenticated
        if ($request->user()) {
            SearchLog::create([
                'user_id' => $request->user()->id,
                'query' => $query,
                'results_count' => 0, // Will update after query
            ]);
        }

        // Search query
        $products = Product::query()
            ->with(['vendor', 'category', 'brand', 'images'])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%")
                    ->orWhere('tags', 'LIKE', "%{$query}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($query) {
                        $categoryQuery->where('name', 'LIKE', "%{$query}%");
                    })
                    ->orWhereHas('brand', function ($brandQuery) use ($query) {
                        $brandQuery->where('name', 'LIKE', "%{$query}%");
                    });
            });

        // Sorting
        $sortBy = $request->input('sort_by', 'relevance');

        if ($sortBy === 'relevance') {
            // Simple relevance: prioritize name matches
            $products->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END", ["%{$query}%"]);
        } else {
            $sortOrder = $request->input('sort_order', 'desc');
            $allowedSorts = ['price', 'rating', 'sales_count', 'created_at'];

            if (in_array($sortBy, $allowedSorts)) {
                $products->orderBy($sortBy, $sortOrder);
            }
        }

        // Filter by brand
        if ($request->has('brand')) {
            $brands = is_array($request->brand) ? $request->brand : [$request->brand];
            $products->whereHas('brand', function ($q) use ($brands) {
                $q->whereIn('slug', $brands);
            });
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $products->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $products->where('price', '<=', $request->max_price);
        }

        // Filter free shipping
        if ($request->boolean('free_shipping')) {
            $products->where('has_free_shipping', true);
        }

        // Filter in stock
        if ($request->boolean('in_stock')) {
            $products->where('stock', '>', 0);
        }

        // Filter by rating
        if ($request->has('rating')) {
            $products->where('rating', '>=', $request->rating);
        }

        // Pagination
        $perPage = min($request->input('per_page', 24), 100);
        $results = $products->paginate($perPage);

        return response()->json([
            'query' => $query,
            'data' => $results->items(),
            'meta' => [
                'total' => $results->total(),
                'last_page' => $results->lastPage(),
                'current_page' => $results->currentPage(),
                'per_page' => $results->perPage(),
            ],
        ]);
    }
}
