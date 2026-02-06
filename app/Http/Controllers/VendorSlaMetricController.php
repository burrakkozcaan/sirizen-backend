<?php

namespace App\Http\Controllers;

use App\Models\VendorSlaMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class VendorSlaMetricController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $metrics = VendorSlaMetric::where('vendor_id', $vendor->id)
            ->orderBy('metric_date', 'desc')
            ->limit(30) // Son 30 gün
            ->get()
            ->map(function ($metric) {
                return [
                    'id' => (string) $metric->id,
                    'metric_date' => $metric->metric_date?->toDateString() ?? '',
                    'total_orders' => (int) $metric->total_orders,
                    'cancelled_orders' => (int) $metric->cancelled_orders,
                    'returned_orders' => (int) $metric->returned_orders,
                    'late_shipments' => (int) $metric->late_shipments,
                    'on_time_shipments' => (int) $metric->on_time_shipments,
                    'cancel_rate' => (string) $metric->cancel_rate,
                    'return_rate' => (string) $metric->return_rate,
                    'late_shipment_rate' => (string) $metric->late_shipment_rate,
                    'avg_shipment_time' => (int) $metric->avg_shipment_time,
                    'avg_response_time' => (int) $metric->avg_response_time,
                    'total_questions_answered' => (int) $metric->total_questions_answered,
                    'total_reviews_responded' => (int) $metric->total_reviews_responded,
                    'customer_satisfaction_score' => (string) $metric->customer_satisfaction_score,
                    'sla_violations' => $metric->sla_violations ? (array) $metric->sla_violations : [],
                ];
            })
            ->values()
            ->all();

        return Inertia::render('vendor-sla-metric/index', [
            'metrics' => $metrics,
        ]);
    }
}

