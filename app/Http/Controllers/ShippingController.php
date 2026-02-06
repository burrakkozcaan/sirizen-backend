<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ShippingController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $shipments = Shipment::with('orderItem.product', 'order.user', 'shippingCompany')
            ->where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($shipment) {
                return [
                    'id' => (string) $shipment->id,
                    'tracking_number' => (string) ($shipment->tracking_number ?? ''),
                    'status' => (string) ($shipment->status ?? ''),
                    'carrier' => (string) ($shipment->carrier ?? ''),
                    'shipped_at' => $shipment->shipped_at?->toIso8601String(),
                    'delivered_at' => $shipment->delivered_at?->toIso8601String(),
                    'created_at' => $shipment->created_at?->toIso8601String() ?? '',
                    'product' => $shipment->orderItem?->product ? [
                        'id' => (string) $shipment->orderItem->product->id,
                        'name' => $shipment->orderItem->product->title,
                    ] : null,
                    'order' => $shipment->order ? [
                        'id' => (string) $shipment->order->id,
                        'order_number' => $shipment->order->order_number ?? '',
                    ] : null,
                ];
            })
            ->values()
            ->all();

        return Inertia::render('shipping/index', [
            'shipments' => $shipments,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $shipment = Shipment::findOrFail($id);

        // Vendor kontrolü
        if ($shipment->vendor_id !== $vendor->id) {
            abort(403, 'Bu kargo gönderisine erişim yetkiniz yok.');
        }

        $validated = $request->validate([
            'status' => 'required|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
            'carrier' => 'nullable|string|max:255',
        ]);

        $shipment->update([
            'status' => $validated['status'],
            'tracking_number' => $validated['tracking_number'] ?? $shipment->tracking_number,
            'carrier' => $validated['carrier'] ?? $shipment->carrier,
        ]);

        return redirect()->route('shipping.index')
            ->with('success', 'Kargo bilgileri güncellendi.');
    }
}
