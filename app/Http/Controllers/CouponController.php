<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CouponController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $coupons = Coupon::where('vendor_id', $vendor->id)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($coupon) {
                return [
                    'id' => (string) $coupon->id,
                    'code' => (string) $coupon->code,
                    'title' => (string) $coupon->title,
                    'description' => (string) ($coupon->description ?? ''),
                    'discount_type' => (string) $coupon->discount_type,
                    'discount_value' => (string) $coupon->discount_value,
                    'min_order_amount' => (string) ($coupon->min_order_amount ?? '0'),
                    'usage_limit' => (int) ($coupon->usage_limit ?? 0),
                    'starts_at' => $coupon->starts_at?->toIso8601String() ?? '',
                    'expires_at' => $coupon->expires_at?->toIso8601String() ?? '',
                    'is_active' => (bool) $coupon->is_active,
                    'created_at' => $coupon->created_at?->toIso8601String() ?? '',
                    'product' => $coupon->product ? [
                        'id' => (string) $coupon->product->id,
                        'name' => $coupon->product->title,
                    ] : null,
                ];
            })
            ->values()
            ->all();

        return Inertia::render('coupon/index', [
            'coupons' => $coupons,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $coupon = Coupon::findOrFail($id);

        // Vendor kontrolü
        if ($coupon->vendor_id !== $vendor->id) {
            abort(403, 'Bu kupona erişim yetkiniz yok.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|string|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $coupon->update($validated);

        return redirect()->route('coupon.index')
            ->with('success', 'Kupon başarıyla güncellendi.');
    }
}
