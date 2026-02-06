<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $reviews = ProductReview::with('user', 'product')
            ->where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($review) {
                return [
                    'id' => (string) $review->id,
                    'rating' => (int) $review->rating,
                    'comment' => (string) ($review->comment ?? ''),
                    'created_at' => $review->created_at?->toIso8601String() ?? '',
                    'user' => $review->user ? [
                        'id' => (string) $review->user->id,
                        'name' => $review->user->name,
                        'email' => $review->user->email,
                    ] : null,
                    'product' => $review->product ? [
                        'id' => (string) $review->product->id,
                        'name' => $review->product->title,
                    ] : null,
                ];
            })
            ->values()
            ->all();

        return Inertia::render('review/index', [
            'reviews' => $reviews,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $review = ProductReview::findOrFail($id);

        // Vendor kontrolü
        if ($review->vendor_id !== $vendor->id) {
            abort(403, 'Bu değerlendirmeye erişim yetkiniz yok.');
        }

        $validated = $request->validate([
            'vendor_response' => 'required|string|max:1000',
        ]);

        $review->update([
            'vendor_response' => $validated['vendor_response'],
            'vendor_response_at' => now(),
        ]);

        return redirect()->route('review.index')
            ->with('success', 'Cevabınız başarıyla eklendi.');
    }
}
