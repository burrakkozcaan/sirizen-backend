<?php

namespace App\Http\Controllers;

use App\Models\VendorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class VendorDocumentController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $documents = VendorDocument::where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($document) {
                return [
                    'id' => (string) $document->id,
                    'document_type' => (string) $document->document_type,
                    'file_name' => (string) $document->file_name,
                    'file_size' => (int) $document->file_size,
                    'status' => (string) $document->status,
                    'rejection_reason' => $document->rejection_reason ? (string) $document->rejection_reason : null,
                    'verified_at' => $document->verified_at?->toIso8601String() ?? null,
                    'notes' => $document->notes ? (string) $document->notes : null,
                    'created_at' => $document->created_at?->toIso8601String() ?? '',
                ];
            })
            ->values()
            ->all();

        return Inertia::render('vendor-document/index', [
            'documents' => $documents,
        ]);
    }
}

