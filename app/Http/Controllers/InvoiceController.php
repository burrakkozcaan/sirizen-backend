<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $invoices = Invoice::where('vendor_id', $vendor->id)
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => (string) $invoice->id,
                    'invoice_number' => (string) $invoice->invoice_number,
                    'invoice_type' => (string) $invoice->invoice_type,
                    'subtotal' => (string) $invoice->subtotal,
                    'tax_amount' => (string) $invoice->tax_amount,
                    'total_amount' => (string) $invoice->total_amount,
                    'currency' => (string) $invoice->currency,
                    'status' => (string) $invoice->status,
                    'order_id' => $invoice->order ? (string) $invoice->order->id : null,
                    'order_number' => $invoice->order ? (string) $invoice->order->order_number : null,
                    'sent_at' => $invoice->sent_at?->toIso8601String() ?? null,
                    'delivered_at' => $invoice->delivered_at?->toIso8601String() ?? null,
                    'created_at' => $invoice->created_at?->toIso8601String() ?? '',
                ];
            })
            ->values()
            ->all();

        return Inertia::render('invoice/index', [
            'invoices' => $invoices,
        ]);
    }
}

