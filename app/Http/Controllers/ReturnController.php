<?php

namespace App\Http\Controllers;

use App\Models\ProductReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ReturnController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $returns = ProductReturn::with('orderItem.product', 'user')
            ->where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($return) {
                return [
                    'id' => (string) $return->id,
                    'reason' => (string) ($return->reason?->value ?? ''),
                    'status' => (string) ($return->status ?? ''),
                    'refund_amount' => (string) ($return->refund_amount ?? '0'),
                    'requested_at' => $return->requested_at?->toIso8601String(),
                    'created_at' => $return->created_at?->toIso8601String() ?? '',
                    'product' => $return->orderItem?->product ? [
                        'id' => (string) $return->orderItem->product->id,
                        'name' => $return->orderItem->product->title,
                    ] : null,
                    'user' => $return->user ? [
                        'id' => (string) $return->user->id,
                        'name' => $return->user->name,
                        'email' => $return->user->email,
                    ] : null,
                ];
            })
            ->values()
            ->all();

        return Inertia::render('return/index', [
            'returns' => $returns,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $return = ProductReturn::findOrFail($id);

        // Vendor kontrolü
        if ($return->vendor_id !== $vendor->id) {
            abort(403, 'Bu iade talebine erişim yetkiniz yok.');
        }

        $validated = $request->validate([
            'status' => 'required|string|max:255',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        $return->update([
            'status' => $validated['status'],
            'refund_amount' => $validated['refund_amount'] ?? $return->refund_amount,
        ]);

        return redirect()->route('return.index')
            ->with('success', 'İade durumu güncellendi.');
    }
}
