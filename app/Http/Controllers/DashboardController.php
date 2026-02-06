<?php

namespace App\Http\Controllers;

use App\Models\CargoIntegration;
use App\Models\Order;
use App\Models\ProductReturn;
use App\Models\SellerPage;
use App\Models\Shipment;
use App\Models\VendorBalance;
use App\Models\VendorDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response|RedirectResponse
    {
        $vendor = Auth::user()->vendor;

        if (! $vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $totalOrders = Order::query()
            ->whereHas('items', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })
            ->count();

        $recentOrders = Order::query()
            ->whereHas('items', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })
            ->with(['items' => function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            }])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function (Order $order): array {
                $items = $order->items;

                return [
                    'id' => (string) $order->id,
                    'order_number' => $order->order_number ?? '',
                    'status' => (string) ($order->status ?? ''),
                    'items_count' => $items->count(),
                    'total_price' => (string) $items->sum('price'),
                    'created_at' => $order->created_at?->toIso8601String() ?? '',
                ];
            })
            ->values()
            ->all();

        $activeShipmentsCount = Shipment::query()
            ->where('vendor_id', $vendor->id)
            ->whereNotIn('status', ['delivered', 'cancelled', 'returned', 'failed'])
            ->count();

        $missingTrackingCount = Shipment::query()
            ->where('vendor_id', $vendor->id)
            ->where(function ($query) {
                $query->whereNull('tracking_number')
                    ->orWhere('tracking_number', '')
                    ->orWhereNull('carrier')
                    ->orWhere('carrier', '');
            })
            ->count();

        $recentShipments = Shipment::query()
            ->with(['orderItem.product', 'order', 'shippingCompany'])
            ->where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function (Shipment $shipment): array {
                return [
                    'id' => (string) $shipment->id,
                    'tracking_number' => (string) ($shipment->tracking_number ?? ''),
                    'status' => (string) ($shipment->status ?? ''),
                    'carrier' => (string) ($shipment->carrier ?? $shipment->shippingCompany?->name ?? ''),
                    'created_at' => $shipment->created_at?->toIso8601String() ?? '',
                    'shipped_at' => $shipment->shipped_at?->toIso8601String(),
                    'order' => $shipment->order ? [
                        'id' => (string) $shipment->order->id,
                        'order_number' => $shipment->order->order_number ?? '',
                    ] : null,
                    'product' => $shipment->orderItem?->product ? [
                        'id' => (string) $shipment->orderItem->product->id,
                        'name' => $shipment->orderItem->product->title,
                    ] : null,
                ];
            })
            ->values()
            ->all();

        $pendingReturnsCount = ProductReturn::query()
            ->where('vendor_id', $vendor->id)
            ->whereNotIn('status', ['refunded', 'rejected'])
            ->count();

        $balance = VendorBalance::where('vendor_id', $vendor->id)->first();

        $missingItems = [];

        if (! CargoIntegration::where('vendor_id', $vendor->id)->exists()) {
            $missingItems[] = [
                'label' => 'Kargo entegrasyonu eklenmedi',
                'href' => route('cargo-integration.index'),
            ];
        }

        if (! VendorDocument::where('vendor_id', $vendor->id)->exists()) {
            $missingItems[] = [
                'label' => 'Belgeler yüklenmedi',
                'href' => route('vendor-document.index'),
            ];
        }

        $sellerPage = SellerPage::where('vendor_id', $vendor->id)->first();
        if (! $sellerPage || ! $sellerPage->description) {
            $missingItems[] = [
                'label' => 'Mağaza sayfası açıklaması eksik',
                'href' => route('seller-page.index'),
            ];
        }

        return Inertia::render('dashboard', [
            'stats' => [
                'total_orders' => $totalOrders,
                'active_shipments' => $activeShipmentsCount,
                'pending_returns' => $pendingReturnsCount,
                'pending_balance' => (string) ($balance?->pending_balance ?? '0.00'),
            ],
            'shipment_alerts' => [
                'missing_tracking' => $missingTrackingCount,
            ],
            'recent_orders' => $recentOrders,
            'recent_shipments' => $recentShipments,
            'commission' => [
                'default_rate' => (float) config('payment.commission.default_rate', 10.00),
                'min_amount' => (float) config('payment.commission.min_amount', 1.00),
                'currency' => (string) config('payment.currency', 'TRY'),
            ],
            'missing_items' => $missingItems,
        ]);
    }
}
