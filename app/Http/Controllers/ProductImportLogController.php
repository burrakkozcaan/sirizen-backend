<?php

namespace App\Http\Controllers;

use App\Models\ProductImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProductImportLogController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $importLogs = ProductImportLog::where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => (string) $log->id,
                    'file_name' => (string) $log->file_name,
                    'file_type' => (string) $log->file_type,
                    'total_rows' => (int) $log->total_rows,
                    'success_count' => (int) $log->success_count,
                    'failed_count' => (int) $log->failed_count,
                    'skipped_count' => (int) $log->skipped_count,
                    'status' => (string) $log->status,
                    'errors' => $log->errors ? (array) $log->errors : [],
                    'summary' => $log->summary ? (string) $log->summary : null,
                    'started_at' => $log->started_at?->toIso8601String() ?? null,
                    'completed_at' => $log->completed_at?->toIso8601String() ?? null,
                    'created_at' => $log->created_at?->toIso8601String() ?? '',
                ];
            })
            ->values()
            ->all();

        return Inertia::render('product-import-log/index', [
            'importLogs' => $importLogs,
        ]);
    }
}

