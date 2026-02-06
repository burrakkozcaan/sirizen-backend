<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CommissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Get user's orders.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::where('user_id', $request->user()->id)
            ->with([
                'items.product.images',
                'items.product.brand',
                'items.vendor',
                'address'
            ]);
        
        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $status = $request->status;
            if ($status === 'shipped') {
                // Include both shipped and partially_shipped
                $query->whereIn('status', ['shipped', 'partially_shipped']);
            } elseif ($status === 'pending') {
                // "pending" tab includes pending, confirmed, and processing
                $query->whereIn('status', ['pending', 'confirmed', 'processing']);
            } else {
                $query->where('status', $status);
            }
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        // Transform orders to include total and item totals
        $transformedOrders = $orders->getCollection()->map(function ($order) {
            $orderArray = $order->toArray();
            $orderArray['total'] = (float) $order->total_price;
            // Ensure status is explicitly set (in case toArray() doesn't include it)
            $orderArray['status'] = $order->status;
            $orderArray['items'] = $order->items->map(function ($item) {
                $itemArray = $item->toArray();
                $itemArray['total'] = (float) ($item->price * $item->quantity);
                
                // Ensure product data is properly included and consistent
                if ($item->product) {
                    $product = $item->product;
                    $productArray = [
                        'id' => $product->id,
                        'title' => $product->title,
                        'name' => $product->title, // Add name field for compatibility
                        'slug' => $product->slug,
                        'price' => (float) $product->price,
                        'original_price' => $product->original_price ? (float) $product->original_price : null,
                        'images' => $product->images ? $product->images->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'url' => $image->url,
                                'is_primary' => $image->is_primary ?? false,
                            ];
                        })->toArray() : [],
                        'brand' => $product->brand ? [
                            'id' => $product->brand->id,
                            'name' => $product->brand->name,
                            'slug' => $product->brand->slug,
                        ] : null,
                    ];
                    $itemArray['product'] = $productArray;
                }
                
                return $itemArray;
            })->toArray();
            return $orderArray;
        });

        return response()->json([
            'data' => $transformedOrders,
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'per_page' => $orders->perPage(),
            'total' => $orders->total(),
        ]);
    }

    /**
     * Get single order details.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        // Support both ID and order_number
        $order = Order::where('user_id', $request->user()->id)
            ->where(function ($query) use ($id) {
                // Try to find by ID if it's numeric
                if (is_numeric($id)) {
                    $query->where('id', $id);
                }
                // Always try order_number
                $query->orWhere('order_number', $id);
            })
            ->with([
                'items.product.images',
                'items.product.brand',
                'items.vendor',
                'items.shipment',
                'address',
            ])
            ->firstOrFail();

        // Transform order to include total and item totals
        $orderArray = $order->toArray();
        $orderArray['total'] = (float) $order->total_price;
        $orderArray['subtotal'] = (float) $order->total_price; // For now, same as total
        $orderArray['shipping_total'] = 0; // Can be calculated from items if needed
        $orderArray['discount_total'] = 0; // Can be calculated if discounts exist
        $orderArray['shipping_address'] = $order->address ? $order->address->toArray() : null;
        $orderArray['items'] = $order->items->map(function ($item) {
            $itemArray = $item->toArray();
            $itemArray['total'] = (float) ($item->price * $item->quantity);
            // Add tracking number and coordinates from shipment if exists
            if ($item->shipment) {
                $itemArray['tracking_number'] = $item->shipment->tracking_number;
                $itemArray['tracking_url'] = $item->shipment->tracking_url;
                $itemArray['carrier'] = $item->shipment->carrier;
                $itemArray['current_latitude'] = $item->shipment->current_latitude;
                $itemArray['current_longitude'] = $item->shipment->current_longitude;
            }
            return $itemArray;
        })->toArray();
        
        // Add shipping address coordinates if available
        if ($order->address) {
            $orderArray['shipping_address']['latitude'] = $order->address->latitude;
            $orderArray['shipping_address']['longitude'] = $order->address->longitude;
        }

        return response()->json(['data' => $orderArray]);
    }

    /**
     * Create new order from cart or items array.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'address_id' => 'required|exists:addresses,id',
                'payment_method' => 'required|in:credit_card,debit_card,bank_transfer,cash_on_delivery',
                'items' => 'sometimes|array',
                'items.*.product_id' => 'required_with:items|exists:products,id',
                'items.*.quantity' => 'required_with:items|integer|min:1',
                'items.*.price' => 'required_with:items|numeric|min:0',
                'items.*.variant_id' => 'nullable|exists:product_variants,id',
                'use_cart' => 'sometimes|boolean',
                'reordered_from_order_id' => 'nullable|exists:orders,id',
            ]);

            $useCart = $request->boolean('use_cart', true);
            $items = [];

            if ($useCart) {
                // Use cart
                $cart = Cart::where('user_id', $request->user()->id)
                    ->with('items.productSeller.vendor')
                    ->first();

                if (!$cart || $cart->items->isEmpty()) {
                    return response()->json([
                        'message' => 'Sepetiniz boş.',
                    ], 400);
                }

                // Convert cart items to order items format
                foreach ($cart->items as $cartItem) {
                    // Get vendor_id from productSeller or product
                    $vendorId = $cartItem->productSeller->vendor_id ?? null;
                    
                    // If productSeller doesn't have vendor_id, get from product
                    if (!$vendorId && $cartItem->product_id) {
                        $product = \App\Models\Product::find($cartItem->product_id);
                        $vendorId = $product?->vendor_id;
                    }

                    if (!$vendorId) {
                        Log::error("Cannot determine vendor_id for cart item", [
                            'cart_item_id' => $cartItem->id,
                            'product_id' => $cartItem->product_id,
                            'product_seller_id' => $cartItem->product_seller_id,
                        ]);
                        return response()->json([
                            'message' => 'Sepetinizdeki bir ürünün satıcı bilgisi bulunamadı. Lütfen sepete tekrar ekleyin.',
                        ], 400);
                    }

                    $items[] = [
                        'product_id' => $cartItem->product_id,
                        'variant_id' => $cartItem->variant_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->price,
                        'vendor_id' => $vendorId,
                    ];
                }
            } else {
                // Use items array from request (check both 'items' and 'order_items')
                $requestItems = $request->input('items', $request->input('order_items', []));
                
                if (empty($requestItems)) {
                    return response()->json([
                        'message' => 'Sepetiniz boş.',
                    ], 400);
                }

                // Get vendor_id from products
                foreach ($requestItems as $item) {
                    $product = \App\Models\Product::with('vendor')->find($item['product_id']);
                    if (!$product) {
                        Log::warning("Product not found for order item", ['product_id' => $item['product_id']]);
                        continue;
                    }

                    // Get vendor_id - try multiple sources
                    $vendorId = $product->vendor_id;
                    
                    // If product doesn't have vendor_id, try to get from first productSeller
                    if (!$vendorId) {
                        $productSeller = $product->productSellers()->first();
                        $vendorId = $productSeller?->vendor_id;
                    }

                    if (!$vendorId) {
                        Log::error("Cannot determine vendor_id for product", [
                            'product_id' => $product->id,
                            'product_title' => $product->title,
                        ]);
                        return response()->json([
                            'message' => 'Ürün satıcı bilgisi bulunamadı. Lütfen tekrar deneyin.',
                        ], 400);
                    }

                    $items[] = [
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['variant_id'] ?? null,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'vendor_id' => $vendorId,
                    ];
                }
            }

            if (empty($items)) {
                return response()->json([
                    'message' => 'Sepetiniz boş.',
                ], 400);
            }

            // Calculate total
            $total = collect($items)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            // Prevent duplicate orders: Check if user has ANY pending order with same items in last 60 seconds
            // Use database transaction to ensure atomicity
            $addressId = $request->address_id;
            $recentOrder = DB::transaction(function () use ($request, $total, $items, $addressId) {
                return Order::where('user_id', $request->user()->id)
                    ->where('status', 'pending')
                    ->where('total_price', $total)
                    ->where('address_id', $addressId)
                    ->where('created_at', '>=', now()->subSeconds(60))
                    ->withCount('items')
                    ->lockForUpdate() // Lock row to prevent concurrent inserts
                    ->first();
            });

            if ($recentOrder && $recentOrder->items_count === count($items)) {
                // Check if items match
                $recentOrderItems = $recentOrder->items()->get();
                $itemsMatch = true;
                
                foreach ($items as $item) {
                    $found = $recentOrderItems->first(function ($orderItem) use ($item) {
                        return $orderItem->product_id == $item['product_id'] 
                            && $orderItem->quantity == $item['quantity']
                            && abs($orderItem->price - $item['price']) < 0.01; // Allow small floating point differences
                    });
                    if (!$found) {
                        $itemsMatch = false;
                        break;
                    }
                }

                if ($itemsMatch) {
                    Log::warning("Duplicate order attempt prevented", [
                        'user_id' => $request->user()->id,
                        'recent_order_id' => $recentOrder->id,
                        'recent_order_number' => $recentOrder->order_number,
                    ]);
                    return response()->json([
                        'message' => 'Siparişiniz zaten oluşturuldu.',
                        'data' => [
                            'id' => $recentOrder->id,
                            'order_number' => $recentOrder->order_number,
                            'order' => $recentOrder->load(['items.product', 'items.vendor']),
                        ],
                    ], 200);
                }
            }

            // Use database transaction for order creation to prevent race conditions
            $order = DB::transaction(function () use ($request, $total, $items, $addressId, $useCart) {
                // Create order
                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'order_number' => 'ORD-'.strtoupper(Str::random(10)),
                    'total_price' => $total,
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                    'address_id' => $addressId,
                    'reordered_from_order_id' => $request->input('reordered_from_order_id'),
                ]);

                // Create order items - validate vendor_id before creating
                foreach ($items as $item) {
                    if (empty($item['vendor_id'])) {
                        Log::error("Missing vendor_id for order item", [
                            'order_id' => $order->id,
                            'product_id' => $item['product_id'],
                            'item' => $item,
                        ]);
                        throw new \Exception('Ürün satıcı bilgisi bulunamadı.');
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'vendor_id' => $item['vendor_id'],
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['variant_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'status' => 'pending',
                    ]);
                }

                // Order'ı yeniden yükle (ilişkiler için)
                $order->load(['items.product', 'items.vendor', 'items.product.category']);

                // Komisyonları hesapla ve kaydet (Trendyol mantığı)
                $commissionService = new CommissionService();
                $commissionService->createCommissionsForOrder($order);

                // Clear cart if used
                if ($useCart) {
                    $cart = Cart::where('user_id', $request->user()->id)->first();
                    if ($cart) {
                        $cart->items()->delete();
                    }
                }

                return $order;
            });

            return response()->json([
                'message' => 'Siparişiniz oluşturuldu!',
                'data' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'order' => $order->load(['items.product', 'items.vendor']),
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Girdiğiniz bilgileri kontrol edin.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error creating order: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->user()->id ?? null,
            ]);
            return response()->json([
                'message' => 'Sipariş oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.',
            ], 500);
        }
    }

    /**
     * Get order tracking information.
     */
    public function tracking(Request $request, int $id): JsonResponse
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with([
                'items.shipment',
                'items.product',
                'items.vendor',
                'address',
            ])
            ->findOrFail($id);

        $trackingData = [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product->name,
                    'tracking_number' => $item->shipment?->tracking_number,
                    'tracking_url' => $item->shipment?->tracking_url,
                    'carrier' => $item->shipment?->carrier,
                    'status' => $item->shipment?->status,
                    'current_location' => $item->shipment?->current_location,
                    'current_latitude' => $item->shipment?->current_latitude,
                    'current_longitude' => $item->shipment?->current_longitude,
                    'progress_percent' => $item->shipment?->progress_percent ?? 0,
                    'estimated_delivery' => $item->shipment?->estimated_delivery,
                ];
            })->toArray(),
            'shipping_address' => $order->address ? [
                'title' => $order->address->title,
                'address_line' => $order->address->address_line,
                'city' => $order->address->city,
                'district' => $order->address->district,
                'latitude' => $order->address->latitude,
                'longitude' => $order->address->longitude,
            ] : null,
        ];

        return response()->json(['data' => $trackingData]);
    }

    /**
     * Get order status counts for the authenticated user.
     */
    public function statusCounts(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Count all orders
        $allCount = Order::where('user_id', $user->id)->count();
        
        // Count by status - "pending" tab includes pending, confirmed, and processing
        $pendingCount = Order::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed', 'processing'])
            ->count();
        
        $counts = [
            'all' => $allCount,
            'pending' => $pendingCount, // Combined count for pending/confirmed/processing
            'confirmed' => Order::where('user_id', $user->id)->where('status', 'confirmed')->count(),
            'processing' => Order::where('user_id', $user->id)->where('status', 'processing')->count(),
            'shipped' => Order::where('user_id', $user->id)->whereIn('status', ['shipped', 'partially_shipped'])->count(),
            'delivered' => Order::where('user_id', $user->id)->where('status', 'delivered')->count(),
            'cancelled' => Order::where('user_id', $user->id)->where('status', 'cancelled')->count(),
            'refunded' => Order::where('user_id', $user->id)->where('status', 'refunded')->count(),
        ];

        return response()->json(['data' => $counts]);
    }

    /**
     * Cancel an order.
     */
    public function cancel(Request $request, string $id): JsonResponse
    {
        try {
            $order = Order::where('user_id', $request->user()->id)
                ->where(function ($query) use ($id) {
                    if (is_numeric($id)) {
                        $query->where('id', $id);
                    }
                    $query->orWhere('order_number', $id);
                })
                ->firstOrFail();

            // Check if order can be cancelled
            if (!in_array($order->status, ['pending', 'confirmed', 'processing'])) {
                return response()->json([
                    'message' => 'Bu sipariş iptal edilemez.',
                    'errors' => ['status' => ['Sadece bekleyen, onaylanmış veya hazırlanmakta olan siparişler iptal edilebilir.']],
                ], 422);
            }

            // Update order status
            $order->status = 'cancelled';
            $order->save();

            return response()->json([
                'message' => 'Sipariş başarıyla iptal edildi.',
                'data' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Sipariş bulunamadı.',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Order cancellation error: ' . $e->getMessage(), [
                'order_id' => $id,
                'user_id' => $request->user()->id,
                'exception' => $e,
            ]);

            return response()->json([
                'message' => 'Sipariş iptal edilirken bir hata oluştu.',
            ], 500);
        }
    }
}
