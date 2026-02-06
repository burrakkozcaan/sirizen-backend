<?php

namespace App\Http\Controllers;

use App\Models\ProductApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProductApprovalController extends Controller
{
    /**
     * Display product approvals for the vendor.
     */
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $approvals = ProductApproval::with('product', 'reviewer')
            ->where('vendor_id', $vendor->id)
            ->orderBy('submitted_at', 'desc')
            ->get()
            ->map(function ($approval) {
                return [
                    'id' => (string) $approval->id,
                    'product_id' => (string) $approval->product_id,
                    'product_name' => $approval->product ? (string) $approval->product->title : 'Bilinmeyen Ürün',
                    'status' => (string) $approval->status,
                    'rejection_reason' => $approval->rejection_reason ? (string) $approval->rejection_reason : null,
                    'admin_notes' => $approval->admin_notes ? (string) $approval->admin_notes : null,
                    'changes_requested' => $approval->changes_requested ? (array) $approval->changes_requested : null,
                    'reviewer_name' => $approval->reviewer ? (string) $approval->reviewer->name : null,
                    'submitted_at' => $approval->submitted_at?->toIso8601String() ?? '',
                    'reviewed_at' => $approval->reviewed_at?->toIso8601String() ?? '',
                ];
            })
            ->values()
            ->all();

        // Count by status
        $statusCounts = [
            'pending' => ProductApproval::where('vendor_id', $vendor->id)->where('status', 'pending')->count(),
            'approved' => ProductApproval::where('vendor_id', $vendor->id)->where('status', 'approved')->count(),
            'rejected' => ProductApproval::where('vendor_id', $vendor->id)->where('status', 'rejected')->count(),
            'needs_changes' => ProductApproval::where('vendor_id', $vendor->id)->where('status', 'needs_changes')->count(),
        ];

        return Inertia::render('product-approval/index', [
            'approvals' => $approvals,
            'statusCounts' => $statusCounts,
        ]);
    }
}

