<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Order;
use App\Models\Payment;

interface PaymentGatewayInterface
{
    /**
     * Ödeme oturumu başlat
     *
     * @param  array<string, mixed>  $options
     * @return array{success: bool, checkout_token?: string, checkout_url?: string, html?: string, error?: string}
     */
    public function initiatePayment(Order $order, array $options = []): array;

    /**
     * Callback/webhook doğrula
     *
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, transaction_id?: string, status?: string, error?: string, raw_response?: array<string, mixed>}
     */
    public function verifyPayment(array $payload): array;

    /**
     * İade işlemi
     *
     * @return array{success: bool, refund_id?: string, error?: string}
     */
    public function refund(Payment $payment, ?float $amount = null): array;

    /**
     * Ödeme durumu sorgula
     *
     * @return array{success: bool, status?: string, error?: string}
     */
    public function queryPaymentStatus(string $transactionId): array;

    /**
     * Provider adını döndür
     */
    public function getProviderName(): string;
}
