<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessPaymentCallbackJob;
use App\PaymentProvider;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * PayTR server-to-server callback
     */
    public function handlePaytrCallback(Request $request): string
    {
        Log::info('PayTR webhook received', [
            'payload' => $request->all(),
        ]);

        try {
            // Asenkron işleme için kuyruğa at
            ProcessPaymentCallbackJob::dispatch(
                PaymentProvider::Paytr,
                $request->all()
            );

            // PayTR 'OK' yanıtı bekler
            return 'OK';
        } catch (\Throwable $e) {
            Log::error('PayTR webhook error', [
                'error' => $e->getMessage(),
            ]);

            return 'FAIL';
        }
    }

    /**
     * iyzico callback
     */
    public function handleIyzicoCallback(Request $request): JsonResponse
    {
        Log::info('iyzico webhook received', [
            'payload' => $request->all(),
        ]);

        try {
            // Asenkron işleme için kuyruğa at
            ProcessPaymentCallbackJob::dispatch(
                PaymentProvider::Iyzico,
                $request->all()
            );

            return response()->json(['status' => 'received']);
        } catch (\Throwable $e) {
            Log::error('iyzico webhook error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Test gateway callback
     */
    public function handleTestCallback(Request $request): JsonResponse
    {
        Log::info('Test webhook received', [
            'payload' => $request->all(),
        ]);

        try {
            // Test gateway senkron işlenir
            $result = $this->paymentService->handleCallback(
                PaymentProvider::Test,
                $request->all()
            );

            if ($result['success']) {
                $successUrl = config('payment.callbacks.success_url');

                return response()->json([
                    'status' => 'success',
                    'redirect_url' => url($successUrl . '?order_id=' . ($request->input('order_id'))),
                ]);
            }

            $failUrl = config('payment.callbacks.fail_url');

            return response()->json([
                'status' => 'failed',
                'error' => $result['error'] ?? 'Ödeme başarısız',
                'redirect_url' => url($failUrl . '?order_id=' . ($request->input('order_id'))),
            ]);
        } catch (\Throwable $e) {
            Log::error('Test webhook error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
