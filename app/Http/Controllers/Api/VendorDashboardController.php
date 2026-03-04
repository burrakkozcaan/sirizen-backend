<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\VendorBalance;
use App\Models\VendorPayout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorDashboardController extends Controller
{
    /**
     * GET /api/vendor/me/balance
     * Vendor bakiye bilgisi
     */
    public function balance(Request $request): JsonResponse
    {
        $vendor = $request->user()->vendor;

        if (! $vendor) {
            return response()->json(['message' => 'Satıcı hesabı bulunamadı.'], 404);
        }

        $balance = VendorBalance::firstOrCreate(
            ['vendor_id' => $vendor->id],
            [
                'balance' => 0,
                'available_balance' => 0,
                'pending_balance' => 0,
                'total_earnings' => 0,
                'total_withdrawn' => 0,
                'currency' => 'TRY',
            ]
        );

        return response()->json([
            'data' => [
                'balance' => (float) $balance->balance,
                'available_balance' => (float) $balance->available_balance,
                'pending_balance' => (float) $balance->pending_balance,
                'total_earnings' => (float) $balance->total_earnings,
                'total_withdrawn' => (float) $balance->total_withdrawn,
                'currency' => $balance->currency ?? 'TRY',
            ],
        ]);
    }

    /**
     * GET /api/vendor/me/commissions
     * Vendor komisyon listesi (paginated, filtrelenebilir)
     */
    public function commissions(Request $request): JsonResponse
    {
        $vendor = $request->user()->vendor;

        if (! $vendor) {
            return response()->json(['message' => 'Satıcı hesabı bulunamadı.'], 404);
        }

        $query = Commission::where('vendor_id', $vendor->id)
            ->with(['orderItem.order']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $commissions = $query->orderByDesc('created_at')->paginate(20);

        return response()->json($commissions);
    }

    /**
     * GET /api/vendor/me/payouts
     * Vendor ödeme talepleri listesi (paginated)
     */
    public function payouts(Request $request): JsonResponse
    {
        $vendor = $request->user()->vendor;

        if (! $vendor) {
            return response()->json(['message' => 'Satıcı hesabı bulunamadı.'], 404);
        }

        $payouts = VendorPayout::where('vendor_id', $vendor->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($payouts);
    }

    /**
     * POST /api/vendor/me/payout-request
     * Yeni ödeme talebi oluştur
     */
    public function requestPayout(Request $request): JsonResponse
    {
        $vendor = $request->user()->vendor;

        if (! $vendor) {
            return response()->json(['message' => 'Satıcı hesabı bulunamadı.'], 404);
        }

        $balance = VendorBalance::where('vendor_id', $vendor->id)->first();
        $availableBalance = (float) ($balance?->available_balance ?? 0);

        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:1',
                function (string $attribute, mixed $value, \Closure $fail) use ($availableBalance) {
                    if ((float) $value > $availableBalance) {
                        $fail('Talep edilen tutar mevcut bakiyenizi (' . number_format($availableBalance, 2) . ' TRY) aşamaz.');
                    }
                },
            ],
        ]);

        $payout = VendorPayout::create([
            'vendor_id' => $vendor->id,
            'amount' => $validated['amount'],
            'payout_method' => 'bank_transfer',
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Ödeme talebi oluşturuldu.',
            'data' => $payout,
        ], 201);
    }
}
