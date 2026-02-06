<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductQuestionController extends Controller
{
    /**
     * Get user's questions.
     */
    public function index(Request $request): JsonResponse
    {
        $questions = ProductQuestion::where('user_id', $request->user()->id)
            ->with(['product.images', 'vendor'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $transformedQuestions = $questions->getCollection()->map(function ($question) {
            return [
                'id' => $question->id,
                'question' => $question->question,
                'answer' => $question->answer,
                'answered_by_vendor' => $question->answered_by_vendor,
                'created_at' => $question->created_at,
                'updated_at' => $question->updated_at,
                'product' => $question->product ? [
                    'id' => $question->product->id,
                    'title' => $question->product->title,
                    'slug' => $question->product->slug,
                    'images' => $question->product->images ? $question->product->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->url,
                        ];
                    })->toArray() : [],
                ] : null,
                'vendor' => $question->vendor ? [
                    'id' => $question->vendor->id,
                    'name' => $question->vendor->name,
                    'slug' => $question->vendor->slug,
                ] : null,
            ];
        });

        return response()->json([
            'data' => $transformedQuestions,
            'current_page' => $questions->currentPage(),
            'last_page' => $questions->lastPage(),
            'per_page' => $questions->perPage(),
            'total' => $questions->total(),
        ]);
    }

    /**
     * Store a new question.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'question' => 'required|string|min:10|max:1000',
        ]);

        $product = \App\Models\Product::findOrFail($request->product_id);
        
        $question = ProductQuestion::create([
            'product_id' => $request->product_id,
            'user_id' => $request->user()->id,
            'vendor_id' => $product->vendor_id,
            'question' => $request->question,
            'answer' => null,
            'answered_by_vendor' => false,
        ]);

        return response()->json([
            'message' => 'Soru başarıyla eklendi',
            'data' => $question->load(['product', 'vendor']),
        ], 201);
    }
}
