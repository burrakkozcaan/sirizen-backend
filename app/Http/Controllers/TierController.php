<?php

namespace App\Http\Controllers;

use App\Models\VendorTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TierController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $currentTier = $vendor->tier;

        // Tüm seviyeleri getir
        $allTiers = VendorTier::orderBy('min_total_orders')->get()->map(function ($tier) use ($currentTier) {
            return [
                'id' => (string) $tier->id,
                'name' => (string) $tier->name,
                'min_total_orders' => (int) ($tier->min_total_orders ?? 0),
                'min_rating' => (string) ($tier->min_rating ?? '0'),
                'max_cancel_rate' => (string) ($tier->max_cancel_rate ?? '0'),
                'max_return_rate' => (string) ($tier->max_return_rate ?? '0'),
                'priority_boost' => (int) ($tier->priority_boost ?? 0),
                'is_current' => $currentTier && $currentTier->id === $tier->id,
            ];
        })->values()->all();

        $tierData = [
            'current_tier' => $currentTier ? [
                'id' => (string) $currentTier->id,
                'name' => (string) $currentTier->name,
            ] : null,
            'vendor_stats' => [
                'total_orders' => (int) ($vendor->total_orders ?? 0),
                'rating' => (string) ($vendor->rating ?? '0'),
                'followers' => (int) ($vendor->followers ?? 0),
                'cancel_rate' => (string) ($vendor->cancel_rate ?? '0'),
                'return_rate' => (string) ($vendor->return_rate ?? '0'),
            ],
            'all_tiers' => $allTiers,
        ];

        return Inertia::render('tier/index', [
            'tier' => $tierData,
        ]);
    }
}
