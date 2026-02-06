<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $orders = OrderItem::with('order.user', 'order.address', 'product')
            ->where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('order_id')
            ->map(function ($items, $orderId) {
                $order = $items->first()->order;
                return [
                    'id' => (string) $orderId,
                    'order_number' => $order?->order_number ?? '',
                    'total_price' => (string) $items->sum('price'),
                    'status' => $order?->status ?? '',
                    'payment_method' => $order?->payment_method ?? '',
                    'created_at' => $order?->created_at?->toIso8601String() ?? '',
                    'user' => $order?->user ? [
                        'id' => (string) $order->user->id,
                        'name' => $order->user->name,
                        'email' => $order->user->email,
                    ] : null,
                    'address' => $order?->address ? [
                        'id' => (string) $order->address->id,
                        'full_name' => $order->address->full_name,
                        'phone' => $order->address->phone,
                        'address_line' => $order->address->address_line,
                        'city' => $order->address->city,
                        'district' => $order->address->district,
                        'neighborhood' => $order->address->neighborhood,
                        'postal_code' => $order->address->postal_code,
                    ] : null,
                    'items_count' => $items->count(),
                ];
            })
            ->values()
            ->all();

        return Inertia::render('order/index', [
            'orders' => $orders,
        ]);
    }

    public function show(string $id): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $order = Order::with(['user', 'address', 'items' => function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id)
                ->with(['product', 'variant', 'shipment']);
        }])
            ->findOrFail($id);

        // Bu siparişte vendor'ın ürünü var mı kontrol et
        $hasVendorItems = $order->items()->where('vendor_id', $vendor->id)->exists();
        if (!$hasVendorItems) {
            abort(403, 'Bu siparişe erişim yetkiniz yok.');
        }

        $orderData = [
            'id' => (string) $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'total_price' => (string) $order->total_price,
            'payment_method' => $order->payment_method,
            'created_at' => $order->created_at?->toIso8601String() ?? '',
            'user' => $order->user ? [
                'id' => (string) $order->user->id,
                'name' => $order->user->name,
                'email' => $order->user->email,
            ] : null,
            'address' => $order->address ? [
                'id' => (string) $order->address->id,
                'full_name' => $order->address->full_name,
                'phone' => $order->address->phone,
                'address_line' => $order->address->address_line,
                'city' => $order->address->city,
                'district' => $order->address->district,
                'postal_code' => $order->address->postal_code,
            ] : null,
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => (string) $item->id,
                    'product' => $item->product ? [
                        'id' => (string) $item->product->id,
                        'title' => $item->product->title,
                    ] : null,
                    'variant_info' => $item->variant_info,
                    'quantity' => $item->quantity,
                    'unit_price' => (string) $item->unit_price,
                    'price' => (string) $item->price,
                    'status' => $item->status,
                    'shipment' => $item->shipment ? [
                        'tracking_number' => $item->shipment->tracking_number,
                        'status' => $item->shipment->status,
                    ] : null,
                ];
            }),
        ];

        return Inertia::render('order/show', [
            'order' => $orderData,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $order = Order::with('address')->findOrFail($id);

        // Bu siparişte vendor'ın ürünü var mı kontrol et
        $hasVendorItems = $order->items()->where('vendor_id', $vendor->id)->exists();
        if (!$hasVendorItems) {
            abort(403, 'Bu siparişe erişim yetkiniz yok.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_method' => 'nullable|string|max:255',
            'address' => 'nullable|array',
            'address.full_name' => 'nullable|string|max:255',
            'address.phone' => 'nullable|string|max:255',
            'address.address_line' => 'nullable|string',
            'address.city' => 'nullable|string|max:255',
            'address.district' => 'nullable|string|max:255',
            'address.neighborhood' => 'nullable|string|max:255',
            'address.postal_code' => 'nullable|string|max:20',
        ]);

        // Sipariş bilgilerini güncelle
        $order->update([
            'status' => $validated['status'],
            'payment_method' => $validated['payment_method'] ?? $order->payment_method,
        ]);

        // Adres bilgilerini güncelle
        if (isset($validated['address']) && $order->address) {
            $order->address->update([
                'full_name' => $validated['address']['full_name'] ?? $order->address->full_name,
                'phone' => $validated['address']['phone'] ?? $order->address->phone,
                'address_line' => $validated['address']['address_line'] ?? $order->address->address_line,
                'city' => $validated['address']['city'] ?? $order->address->city,
                'district' => $validated['address']['district'] ?? $order->address->district,
                'neighborhood' => $validated['address']['neighborhood'] ?? $order->address->neighborhood,
                'postal_code' => $validated['address']['postal_code'] ?? $order->address->postal_code,
            ]);
        }

        return redirect()->route('order.index')
            ->with('success', 'Sipariş başarıyla güncellendi.');
    }
}
