<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Create a product review.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_item_id' => 'required|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = ProductReview::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Değerlendirmeniz kaydedildi. Teşekkürler!',
            'review' => $review,
        ], 201);
    }

    /**
     * Update review.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'rating' => 'integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = ProductReview::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $review->update($request->all());

        return response()->json([
            'message' => 'Değerlendirmeniz güncellendi.',
            'review' => $review,
        ]);
    }

    /**
     * Delete review.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $review = ProductReview::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $review->delete();

        return response()->json([
            'message' => 'Değerlendirmeniz silindi.',
        ]);
    }
}
