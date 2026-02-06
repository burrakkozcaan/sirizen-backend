<?php

namespace App\Http\Controllers;

use App\Models\VendorDailyStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class VendorDailyStatController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $stats = VendorDailyStat::where('vendor_id', $vendor->id)
            ->orderBy('stat_date', 'desc')
            ->limit(30) // Son 30 gün
            ->get()
            ->map(function ($stat) {
                return [
                    'id' => (string) $stat->id,
                    'stat_date' => $stat->stat_date?->toDateString() ?? '',
                    'total_sales' => (int) $stat->total_sales,
                    'revenue' => (string) $stat->revenue,
                    'commission' => (string) $stat->commission,
                    'net_revenue' => (string) $stat->net_revenue,
                    'orders_count' => (int) $stat->orders_count,
                    'products_sold' => (int) $stat->products_sold,
                    'new_customers' => (int) $stat->new_customers,
                    'returning_customers' => (int) $stat->returning_customers,
                    'avg_order_value' => (string) $stat->avg_order_value,
                    'page_views' => (int) $stat->page_views,
                    'product_views' => (int) $stat->product_views,
                    'conversion_rate' => (string) $stat->conversion_rate,
                ];
            })
            ->values()
            ->all();

        return Inertia::render('vendor-daily-stat/index', [
            'stats' => $stats,
        ]);
    }
}

