<?php

namespace App\Http\Controllers;

use App\Models\ProductQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProductQuestionController extends Controller
{
    /**
     * Display a listing of product questions for the vendor.
     */
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $productQuestions = ProductQuestion::with('user', 'product')
            ->where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($question) {
                return [
                    'id' => (string) $question->id,
                    'question' => (string) $question->question,
                    'answer' => $question->answer ? (string) $question->answer : null,
                    'answered_by_vendor' => (bool) $question->answered_by_vendor,
                    'created_at' => $question->created_at?->toIso8601String() ?? '',
                    'user' => $question->user ? [
                        'id' => (string) $question->user->id,
                        'name' => $question->user->name ? (string) $question->user->name : null,
                        'email' => $question->user->email ? (string) $question->user->email : null,
                    ] : null,
                    'product' => $question->product ? [
                        'id' => (string) $question->product->id,
                        'name' => (string) $question->product->title,
                    ] : null,
                ];
            })
            ->values()
            ->all();

        return Inertia::render('product-question/index', [
            'productQuestions' => $productQuestions,
        ]);
    }

    /**
     * Update the answer for a product question.
     */
    public function update(Request $request, string $id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $question = ProductQuestion::where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();

        $validated = $request->validate([
            'answer' => 'required|string|max:2000',
        ]);

        $question->update([
            'answer' => $validated['answer'],
            'answered_by_vendor' => true,
        ]);

        return redirect()->route('product-question.index')
            ->with('success', 'Cevap başarıyla güncellendi.');
    }
}
