<?php

namespace App\Http\Controllers;

use App\Models\VendorPayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $payments = VendorPayout::where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($payout) {
                return [
                    'id' => (string) $payout->id,
                    'amount' => (string) ($payout->amount ?? '0'),
                    'status' => (string) ($payout->status ?? ''),
                    'payout_method' => (string) ($payout->payout_method ?? ''),
                    'period_start' => $payout->period_start?->format('Y-m-d') ?? '',
                    'period_end' => $payout->period_end?->format('Y-m-d') ?? '',
                    'paid_at' => $payout->paid_at?->toIso8601String(),
                    'created_at' => $payout->created_at?->toIso8601String() ?? '',
                ];
            })
            ->values()
            ->all();

        return Inertia::render('payment/index', [
            'payments' => $payments,
        ]);
    }
}
