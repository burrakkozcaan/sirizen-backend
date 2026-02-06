<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Str;

class TestPaymentGateway implements PaymentGatewayInterface
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        protected array $config = []
    ) {
        $this->config = array_merge(config('payment.gateways.test', []), $config);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array{success: bool, checkout_token?: string, checkout_url?: string, html?: string, error?: string}
     */
    public function initiatePayment(Order $order, array $options = []): array
    {
        $checkoutToken = 'test_' . Str::uuid()->toString();
        $callbackUrl = url("/api/webhooks/payment/test?token={$checkoutToken}&order_id={$order->id}");

        if ($this->config['auto_approve'] ?? true) {
            return [
                'success' => true,
                'checkout_token' => $checkoutToken,
                'checkout_url' => $callbackUrl,
                'html' => $this->generateTestFormHtml($order, $checkoutToken, $callbackUrl),
                'auto_approve' => true,
            ];
        }

        return [
            'success' => true,
            'checkout_token' => $checkoutToken,
            'checkout_url' => $callbackUrl,
            'html' => $this->generateTestFormHtml($order, $checkoutToken, $callbackUrl),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, transaction_id?: string, status?: string, error?: string, raw_response?: array<string, mixed>}
     */
    public function verifyPayment(array $payload): array
    {
        $token = $payload['token'] ?? null;
        $status = $payload['status'] ?? 'success';

        if (! $token || ! str_starts_with($token, 'test_')) {
            return [
                'success' => false,
                'error' => 'Invalid test token',
            ];
        }

        if ($status === 'fail') {
            return [
                'success' => false,
                'status' => 'failed',
                'error' => 'Test payment failed (simulated)',
                'raw_response' => $payload,
            ];
        }

        return [
            'success' => true,
            'transaction_id' => 'test_txn_' . Str::random(16),
            'status' => 'completed',
            'raw_response' => $payload,
        ];
    }

    /**
     * @return array{success: bool, refund_id?: string, error?: string}
     */
    public function refund(Payment $payment, ?float $amount = null): array
    {
        $refundAmount = $amount ?? $payment->amount;

        return [
            'success' => true,
            'refund_id' => 'test_refund_' . Str::random(16),
            'refund_amount' => $refundAmount,
        ];
    }

    /**
     * @return array{success: bool, status?: string, error?: string}
     */
    public function queryPaymentStatus(string $transactionId): array
    {
        if (! str_starts_with($transactionId, 'test_')) {
            return [
                'success' => false,
                'error' => 'Invalid test transaction ID',
            ];
        }

        return [
            'success' => true,
            'status' => 'completed',
        ];
    }

    public function getProviderName(): string
    {
        return 'test';
    }

    /**
     * Test ödeme formu HTML'i oluştur
     */
    protected function generateTestFormHtml(Order $order, string $checkoutToken, string $callbackUrl): string
    {
        $successUrl = $callbackUrl . '&status=success';
        $failUrl = $callbackUrl . '&status=fail';

        return <<<HTML
        <div class="test-payment-form" style="font-family: sans-serif; max-width: 400px; margin: 0 auto; padding: 20px; border: 2px dashed #fbbf24; border-radius: 8px; background: #fef3c7;">
            <h3 style="margin: 0 0 16px; color: #92400e;">🧪 Test Ödeme Gateway</h3>
            <p style="margin: 0 0 8px; color: #78350f;"><strong>Sipariş:</strong> #{$order->order_number}</p>
            <p style="margin: 0 0 8px; color: #78350f;"><strong>Tutar:</strong> {$order->total} TL</p>
            <p style="margin: 0 0 16px; color: #78350f;"><strong>Token:</strong> {$checkoutToken}</p>
            <div style="display: flex; gap: 8px;">
                <a href="{$successUrl}" style="flex: 1; display: inline-block; padding: 12px 24px; background: #10b981; color: white; text-decoration: none; border-radius: 6px; text-align: center; font-weight: 600;">✓ Başarılı</a>
                <a href="{$failUrl}" style="flex: 1; display: inline-block; padding: 12px 24px; background: #ef4444; color: white; text-decoration: none; border-radius: 6px; text-align: center; font-weight: 600;">✗ Başarısız</a>
            </div>
        </div>
        HTML;
    }
}
