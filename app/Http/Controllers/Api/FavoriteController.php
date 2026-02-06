<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Get user's favorite products.
     */
    public function index(Request $request): JsonResponse
    {
        $favorites = Favorite::where('user_id', $request->user()->id)
            ->with(['product.images', 'product.vendor'])
            ->latest()
            ->paginate(24);

        return response()->json($favorites);
    }

    /**
     * Add product to favorites.
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $favorite = Favorite::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
        ]);

        return response()->json([
            'message' => 'Ürün favorilere eklendi.',
            'favorite' => $favorite->load(['product.images']),
        ]);
    }

    /**
     * Remove product from favorites.
     */
    public function remove(Request $request, int $productId): JsonResponse
    {
        Favorite::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->delete();

        return response()->json([
            'message' => 'Ürün favorilerden kaldırıldı.',
        ]);
    }
}
