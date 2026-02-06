<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductSeller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get user's cart with items.
     */
    public function index(Request $request): JsonResponse
    {
        $cart = Cart::with([
            'items.product.images',
            'items.product.vendor',
            'items.productSeller.vendor',
        ])
            ->firstOrCreate(['user_id' => $request->user()->id]);

        $total = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return response()->json([
            'cart' => $cart,
            'total' => $total,
            'items_count' => $cart->items->count(),
        ]);
    }

    /**
     * Add item to cart.
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_seller_id' => 'nullable|exists:product_sellers,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

        // Get product seller - use provided ID or find first one for product
        if ($request->product_seller_id) {
            $productSeller = ProductSeller::with('product')->findOrFail($request->product_seller_id);
        } else {
            // Find first available product seller for this product
            $productSeller = ProductSeller::where('product_id', $request->product_id)
                ->where('stock', '>', 0)
                ->first();
            
            if (!$productSeller) {
                return response()->json([
                    'message' => 'Bu ürün için satıcı bulunamadı.',
                ], 404);
            }
        }

        // Check stock
        if ($productSeller->stock < $request->quantity) {
            return response()->json([
                'message' => 'Stokta yeterli ürün yok.',
            ], 400);
        }

        // Check if item already exists
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_seller_id', $request->product_seller_id)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $request->quantity;

            if ($newQuantity > $productSeller->stock) {
                return response()->json([
                    'message' => 'Stokta yeterli ürün yok.',
                ], 400);
            }

            $existingItem->update(['quantity' => $newQuantity]);
            $item = $existingItem;
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'product_seller_id' => $request->product_seller_id,
                'quantity' => $request->quantity,
                'price' => $productSeller->price,
            ]);
        }

        return response()->json([
            'message' => 'Ürün sepete eklendi.',
            'item' => $item->load(['product.images', 'productSeller.vendor']),
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        $item = CartItem::whereHas('cart', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($id);

        // Check stock
        if ($item->productSeller->stock < $request->quantity) {
            return response()->json([
                'message' => 'Stokta yeterli ürün yok.',
            ], 400);
        }

        $item->update(['quantity' => $request->quantity]);

        return response()->json([
            'message' => 'Sepet güncellendi.',
            'item' => $item->load(['product.images', 'productSeller.vendor']),
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function remove(Request $request, int $id): JsonResponse
    {
        $item = CartItem::whereHas('cart', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($id);

        $item->delete();

        return response()->json([
            'message' => 'Ürün sepetten kaldırıldı.',
        ]);
    }

    /**
     * Clear entire cart.
     */
    public function clear(Request $request): JsonResponse
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json([
            'message' => 'Sepet temizlendi.',
        ]);
    }
}
