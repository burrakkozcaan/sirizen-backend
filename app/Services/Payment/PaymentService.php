<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Events\PaymentCompleted;
use App\Events\PaymentFailed;
use App\CommissionStatus;
use App\Models\Commission;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\VendorBalance;
use App\PaymentProvider;
use App\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        protected PaymentGatewayFactory $gatewayFactory
    ) {}

    /**
     * Ödeme başlat
     *
     * @param  array<string, mixed>  $options
     * @return array{success: bool, payment?: Payment, checkout_url?: string, html?: string, error?: string}
     */
    public function initiatePayment(Order $order, string|PaymentProvider $provider, array $options = []): array
    {
        $providerEnum = $provider instanceof PaymentProvider ? $provider : PaymentProvider::from($provider);

        try {
            $gateway = $this->gatewayFactory->make($providerEnum);
            $result = $gateway->initiatePayment($order, $options);

            if (! $result['success']) {
                return $result;
            }

            // Payment kaydı oluştur veya güncelle
            $payment = Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'user_id' => $order->user_id,
                    'payment_provider' => $providerEnum,
                    'payment_type' => $options['payment_type'] ?? 'card',
                    'amount' => $order->total,
                    'currency' => config('payment.currency', 'TRY'),
                    'status' => PaymentStatus::Pending,
                    'checkout_token' => $result['checkout_token'] ?? null,
                    'payment_method' => $options['payment_method'] ?? 'credit_card',
                    'installment_count' => $options['installment'] ?? null,
                    'metadata' => [
                        'initiated_at' => now()->toIso8601String(),
                        'user_ip' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ],
                ]
            );

            return [
                'success' => true,
                'payment' => $payment,
                'checkout_token' => $result['checkout_token'] ?? null,
                'checkout_url' => $result['checkout_url'] ?? null,
                'html' => $result['html'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('PaymentService initiatePayment failed', [
                'order_id' => $order->id,
                'provider' => $providerEnum->value,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Webhook/callback işle
     *
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, payment?: Payment, error?: string}
     */
    public function handleCallback(string|PaymentProvider $provider, array $payload, ?Order $order = null): array
    {
        $providerEnum = $provider instanceof PaymentProvider ? $provider : PaymentProvider::from($provider);

        try {
            $gateway = $this->gatewayFactory->make($providerEnum);
            $result = $gateway->verifyPayment($payload);

            // Order'ı bul
            if (! $order) {
                $orderId = $payload['order_id'] ?? null;
                $checkoutToken = $payload['token'] ?? $payload['checkout_token'] ?? null;

                if ($orderId) {
                    $order = Order::find($orderId);
                } elseif ($checkoutToken) {
                    $payment = Payment::where('checkout_token', $checkoutToken)->first();
                    $order = $payment?->order;
                }
            }

            if (! $order) {
                return [
                    'success' => false,
                    'error' => 'Sipariş bulunamadı',
                ];
            }

            $payment = $order->payment ?? Payment::where('order_id', $order->id)->first();

            if (! $payment) {
                return [
                    'success' => false,
                    'error' => 'Ödeme kaydı bulunamadı',
                ];
            }

            // Payment güncelle
            $payment->update([
                'callback_status' => $result['success'] ? 'success' : 'failed',
                'callback_received_at' => now(),
                'transaction_id' => $result['transaction_id'] ?? $payment->transaction_id,
                'status' => $result['success'] ? PaymentStatus::Completed : PaymentStatus::Failed,
                'metadata' => array_merge($payment->metadata ?? [], [
                    'callback_response' => $result['raw_response'] ?? null,
                    'callback_at' => now()->toIso8601String(),
                ]),
            ]);

            if ($result['success']) {
                // Komisyon hesapla ve dağıt
                $this->settleCommission($order, $payment);

                // Order durumunu güncelle
                $order->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                event(new PaymentCompleted($payment));

                return [
                    'success' => true,
                    'payment' => $payment->fresh(),
                ];
            }

            event(new PaymentFailed($payment, $result['error'] ?? 'Ödeme başarısız'));

            return [
                'success' => false,
                'payment' => $payment->fresh(),
                'error' => $result['error'] ?? 'Ödeme doğrulanamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('PaymentService handleCallback failed', [
                'provider' => $providerEnum->value,
                'payload' => $payload,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Sipariş öğesi için komisyon hesapla
     */
    public function calculateCommission(OrderItem $item, ?float $customRate = null): float
    {
        // Vendor'a özel komisyon oranı varsa kullan
        $rate = $customRate
            ?? $item->vendor?->tier?->commission_rate
            ?? config('payment.commission.default_rate', 10.00);

        $commission = ($item->total_price ?? ($item->unit_price * $item->quantity)) * ($rate / 100);

        return max($commission, config('payment.commission.min_amount', 1.00));
    }

    /**
     * Komisyon dağılımını hesapla ve kaydet
     * Her order_item için komisyon kaydı oluşturur/günceller,
     * payment split tutarlarını hesaplayarak payment'ı günceller.
     */
    public function settleCommission(Order $order, Payment $payment): void
    {
        $commissionService = new \App\Services\CommissionService();

        $totalCommission = 0.0;
        $totalVendorAmount = 0.0;

        DB::transaction(function () use ($order, $payment, $commissionService, &$totalCommission, &$totalVendorAmount) {
            foreach ($order->items as $item) {
                $commission = \App\Models\Commission::where('order_item_id', $item->id)->first();

                if ($commission) {
                    $commission->update([
                        'payment_id' => $payment->id,
                        'status'     => CommissionStatus::PAID,
                    ]);
                } else {
                    $commission = $commissionService->createCommission($item);
                    $commission->update([
                        'payment_id' => $payment->id,
                        'status'     => CommissionStatus::PAID,
                    ]);
                }

                $totalCommission    += (float) $commission->commission_amount;
                $totalVendorAmount  += (float) $commission->net_amount;

                // Vendor bakiyesini güncelle
                $balance = VendorBalance::firstOrCreate(
                    ['vendor_id' => $item->vendor_id],
                    [
                        'balance'           => 0,
                        'available_balance' => 0,
                        'pending_balance'   => 0,
                        'total_earnings'    => 0,
                        'total_withdrawn'   => 0,
                        'currency'          => 'TRY',
                    ]
                );
                $balance->increment('pending_balance', $commission->net_amount);
                $balance->increment('total_earnings', $commission->net_amount);
            }

            // Payment split tutarlarını güncelle
            $payment->update([
                'commission_amount' => round($totalCommission, 2),
                'vendor_amount'     => round($totalVendorAmount, 2),
                'platform_amount'   => round($totalCommission, 2),
                'split_status'      => 'settled',
                'paid_at'           => now(),
            ]);
        });
    }

    /**
     * İade işlemi
     *
     * @return array{success: bool, refund_id?: string, error?: string}
     */
    public function processRefund(Payment $payment, ?float $amount = null): array
    {
        try {
            $gateway = $this->gatewayFactory->make($payment->payment_provider);
            $result = $gateway->refund($payment, $amount);

            if ($result['success']) {
                $refundAmount = (float) ($amount ?? $payment->amount);
                $paymentAmount = (float) $payment->amount;

                $payment->update([
                    'status' => $amount !== null && $refundAmount < $paymentAmount
                        ? PaymentStatus::PartiallyRefunded
                        : PaymentStatus::Refunded,
                    'refund_id' => $result['refund_id'] ?? null,
                    'refunded_amount' => ($payment->refunded_amount ?? 0) + $refundAmount,
                    'refunded_at' => now(),
                ]);

                // Komisyonları geri al
                $this->reverseCommissions($payment, $refundAmount);
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('PaymentService processRefund failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Komisyonları geri al
     */
    protected function reverseCommissions(Payment $payment, float $refundAmount): void
    {
        $refundRatio = $refundAmount / $payment->amount;

        foreach ($payment->commissions as $commission) {
            $refundCommission = $commission->commission_amount * $refundRatio;
            $refundVendorAmount = $commission->net_amount * $refundRatio;

            $commission->update([
                'status' => CommissionStatus::REFUNDED,
                'refunded_amount' => $refundCommission,
            ]);

            // VendorBalance güncelle
            if ($commission->vendor_id) {
                $balance = VendorBalance::where('vendor_id', $commission->vendor_id)->first();

                if ($balance) {
                    $balance->decrement('pending_balance', min($refundVendorAmount, $balance->pending_balance));
                    $balance->decrement('total_earnings', $refundVendorAmount);
                }
            }
        }
    }
}
