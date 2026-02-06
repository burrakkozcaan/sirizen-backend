<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InitiatePaymentRequest;
use App\Models\Order;
use App\PaymentProvider;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CheckoutController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Ödeme başlat
     */
    public function pay(InitiatePaymentRequest $request): JsonResponse
    {
        $order = Order::findOrFail($request->validated('order_id'));

        // Kullanıcı yetkisi kontrolü
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'error' => 'Bu siparişe erişim yetkiniz yok',
            ], 403);
        }

        // Sipariş durumu kontrolü
        if ($order->status !== 'pending' && $order->status !== 'awaiting_payment') {
            return response()->json([
                'success' => false,
                'error' => 'Bu sipariş için ödeme yapılamaz',
            ], 400);
        }

        $provider = PaymentProvider::from($request->validated('gateway'));
        $options = [
            'installment' => $request->validated('installment'),
        ];

        $result = $this->paymentService->initiatePayment($order, $provider, $options);

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Ödeme başlatılamadı',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'payment_id' => $result['payment']->id,
            'checkout_token' => $result['checkout_token'],
            'checkout_url' => $result['checkout_url'],
            'html' => $result['html'],
        ]);
    }

    /**
     * Başarılı ödeme redirect
     */
    public function success(Request $request): InertiaResponse|JsonResponse
    {
        $orderId = $request->query('order_id');
        $order = $orderId ? Order::with('payment')->find($orderId) : null;

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ödeme başarıyla tamamlandı',
                'order_id' => $orderId,
                'order' => $order,
            ]);
        }

        return Inertia::render('Checkout/Success', [
            'order' => $order,
        ]);
    }

    /**
     * Başarısız ödeme redirect
     */
    public function fail(Request $request): InertiaResponse|JsonResponse
    {
        $orderId = $request->query('order_id');
        $error = $request->query('error', 'Ödeme işlemi başarısız oldu');
        $order = $orderId ? Order::with('payment')->find($orderId) : null;

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $error,
                'order_id' => $orderId,
                'order' => $order,
            ], 400);
        }

        return Inertia::render('Checkout/Fail', [
            'order' => $order,
            'error' => $error,
        ]);
    }
}
