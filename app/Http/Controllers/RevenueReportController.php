<?php

namespace App\Http\Controllers;

use App\Models\PlatformRevenueReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RevenueReportController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        // Platform geneli raporlar, vendor kendi verilerini görebilir
        $reports = PlatformRevenueReport::orderBy('report_date', 'desc')
            ->limit(30) // Son 30 rapor
            ->get()
            ->map(function ($report) {
                return [
                    'id' => (string) $report->id,
                    'report_date' => $report->report_date?->toDateString() ?? '',
                    'period_type' => (string) $report->period_type,
                    'total_revenue' => (string) $report->total_revenue,
                    'total_commission' => (string) $report->total_commission,
                    'vendor_payouts' => (string) $report->vendor_payouts,
                    'total_orders' => (int) $report->total_orders,
                    'total_vendors' => (int) $report->total_vendors,
                    'active_vendors' => (int) $report->active_vendors,
                    'new_vendors' => (int) $report->new_vendors,
                    'total_customers' => (int) $report->total_customers,
                    'new_customers' => (int) $report->new_customers,
                    'total_products' => (int) $report->total_products,
                    'avg_order_value' => (string) $report->avg_order_value,
                    'top_categories' => $report->top_categories ? (array) $report->top_categories : [],
                    'top_vendors' => $report->top_vendors ? (array) $report->top_vendors : [],
                ];
            })
            ->values()
            ->all();

        return Inertia::render('revenue-report/index', [
            'reports' => $reports,
        ]);
    }
}

