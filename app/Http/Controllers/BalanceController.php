<?php

namespace App\Http\Controllers;

use App\Models\VendorBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class BalanceController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $balance = VendorBalance::where('vendor_id', $vendor->id)
            ->first();

        $balanceData = [
            'current_balance' => (string) ($balance?->balance ?? '0.00'),
            'pending_balance' => (string) ($balance?->pending_balance ?? '0.00'),
            'total_earned' => (string) (($balance?->balance ?? 0) + ($balance?->pending_balance ?? 0)),
            'last_updated' => $balance?->updated_at?->toIso8601String() ?? now()->toIso8601String(),
        ];

        return Inertia::render('balance/index', [
            'balance' => $balanceData,
        ]);
    }
}
