<?php

namespace App\Http\Controllers;

use App\Models\VendorAnalytic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class VendorAnalyticController extends Controller
{
    /**
     * Display vendor analytics.
     */
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $analytics = VendorAnalytic::where('vendor_id', $vendor->id)
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get()
            ->map(function ($analytic) {
                return [
                    'id' => (string) $analytic->id,
                    'date' => $analytic->date->format('Y-m-d'),
                    'total_sales' => (string) $analytic->total_sales,
                    'total_orders' => (int) $analytic->total_orders,
                    'average_order_value' => (string) $analytic->average_order_value,
                    'units_sold' => (int) $analytic->units_sold,
                    'commission_amount' => (string) $analytic->commission_amount,
                    'net_earnings' => (string) $analytic->net_earnings,
                    'pending_payout' => (string) $analytic->pending_payout,
                    'active_products' => (int) $analytic->active_products,
                    'out_of_stock_products' => (int) $analytic->out_of_stock_products,
                    'products_views' => (int) $analytic->products_views,
                    'conversion_rate' => (string) $analytic->conversion_rate,
                    'unique_customers' => (int) $analytic->unique_customers,
                    'new_customers' => (int) $analytic->new_customers,
                    'returning_customers' => (int) $analytic->returning_customers,
                    'total_reviews' => (int) $analytic->total_reviews,
                    'average_rating' => (string) $analytic->average_rating,
                    'questions_answered' => (int) $analytic->questions_answered,
                    'response_time_hours' => (string) $analytic->response_time_hours,
                    'shipped_on_time' => (int) $analytic->shipped_on_time,
                    'late_shipments' => (int) $analytic->late_shipments,
                    'cancelled_orders' => (int) $analytic->cancelled_orders,
                    'returned_orders' => (int) $analytic->returned_orders,
                ];
            })
            ->values()
            ->all();

        // Calculate summary stats
        $summary = [
            'total_sales' => (string) VendorAnalytic::where('vendor_id', $vendor->id)->sum('total_sales'),
            'total_orders' => (int) VendorAnalytic::where('vendor_id', $vendor->id)->sum('total_orders'),
            'total_earnings' => (string) VendorAnalytic::where('vendor_id', $vendor->id)->sum('net_earnings'),
            'total_customers' => (int) VendorAnalytic::where('vendor_id', $vendor->id)->max('unique_customers'),
        ];

        return Inertia::render('vendor-analytic/index', [
            'analytics' => $analytics,
            'summary' => $summary,
        ]);
    }
}

