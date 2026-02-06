<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PayTRService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * PayTR ödeme token'ı oluştur
     */
    public function createPayTRToken(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'email' => 'required|email',
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'installment' => 'nullable|integer|min:0|max:12',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Siparişin kullanıcıya ait olduğunu kontrol et
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Bu sipariş size ait değil',
            ], 403);
        }

        // Sipariş zaten ödenmiş mi kontrol et
        if ($order->status === 'paid' || $order->status === 'completed') {
            return response()->json([
                'message' => 'Bu sipariş zaten ödenmiş',
            ], 400);
        }

        try {
            $payTRService = new PayTRService();
            
            $customerData = [
                'email' => $request->email,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'installment' => $request->installment ?? 0,
            ];

            $result = $payTRService->createPaymentToken($order, $customerData);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('PayTR Token Creation Error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ödeme token\'ı oluşturulamadı: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PayTR callback'i işle
     */
    public function handlePayTRCallback(Request $request): JsonResponse
    {
        $data = $request->all();

        Log::info('PayTR Callback Received', $data);

        try {
            $payTRService = new PayTRService();

            // Callback'i doğrula
            if (!$payTRService->verifyCallback($data)) {
                Log::warning('PayTR Callback Verification Failed', $data);
                return response()->json(['status' => 'error', 'message' => 'Geçersiz callback'], 400);
            }

            // Merchant OID'den order'ı bul
            $merchantOid = $data['merchant_oid'] ?? null;
            if (!$merchantOid) {
                return response()->json(['status' => 'error', 'message' => 'Merchant OID bulunamadı'], 400);
            }

            // Order'ı bul (merchant_oid payment_reference'da saklanıyor)
            $order = Order::where('payment_reference', $merchantOid)->first();
            if (!$order) {
                Log::warning('PayTR Order Not Found', ['merchant_oid' => $merchantOid]);
                return response()->json(['status' => 'error', 'message' => 'Sipariş bulunamadı'], 404);
            }

            // Ödeme durumunu kontrol et
            if ($data['status'] === 'success') {
                // Ödeme başarılı
                $order->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'payment_status' => 'completed',
                ]);

                Log::info('PayTR Payment Success', [
                    'order_id' => $order->id,
                    'merchant_oid' => $merchantOid,
                ]);

                return response()->json(['status' => 'success']);
            } else {
                // Ödeme başarısız
                $order->update([
                    'payment_status' => 'failed',
                ]);

                Log::warning('PayTR Payment Failed', [
                    'order_id' => $order->id,
                    'merchant_oid' => $merchantOid,
                    'reason' => $data['failed_reason_code'] ?? 'Bilinmeyen',
                ]);

                return response()->json(['status' => 'failed']);
            }
        } catch (\Exception $e) {
            Log::error('PayTR Callback Error', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Callback işlenirken hata oluştu',
            ], 500);
        }
    }

    /**
     * Ödeme durumunu sorgula
     */
    public function checkPaymentStatus(Request $request, string $orderId): JsonResponse
    {
        $order = Order::findOrFail($orderId);

        // Siparişin kullanıcıya ait olduğunu kontrol et
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Bu sipariş size ait değil',
            ], 403);
        }

        if (!$order->payment_reference) {
            return response()->json([
                'success' => false,
                'message' => 'Ödeme referansı bulunamadı',
            ], 400);
        }

        try {
            $payTRService = new PayTRService();
            $status = $payTRService->checkPaymentStatus($order->payment_reference);

            return response()->json([
                'success' => true,
                'data' => $status,
            ]);
        } catch (\Exception $e) {
            Log::error('PayTR Status Check Error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ödeme durumu sorgulanamadı',
            ], 500);
        }
    }
}
