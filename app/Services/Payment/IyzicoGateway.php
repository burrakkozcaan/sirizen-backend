<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IyzicoGateway implements PaymentGatewayInterface
{
    protected string $apiKey;

    protected string $secretKey;

    protected string $baseUrl;

    protected bool $testMode;

    protected string $locale;

    protected string $currency;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(array $config = [])
    {
        $config = array_merge(config('payment.gateways.iyzico', []), $config);

        $this->apiKey = $config['api_key'] ?? '';
        $this->secretKey = $config['secret_key'] ?? '';
        $this->baseUrl = $config['base_url'] ?? 'https://api.iyzipay.com';
        $this->testMode = $config['test_mode'] ?? true;
        $this->locale = $config['locale'] ?? 'tr';
        $this->currency = $config['currency'] ?? 'TRY';

        if ($this->testMode && ! str_contains($this->baseUrl, 'sandbox')) {
            $this->baseUrl = 'https://sandbox-api.iyzipay.com';
        }
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array{success: bool, checkout_token?: string, checkout_url?: string, html?: string, error?: string}
     */
    public function initiatePayment(Order $order, array $options = []): array
    {
        try {
            $conversationId = $order->order_number;
            $basketId = 'basket_' . $order->id;
            $callbackUrl = url('/api/webhooks/payment/iyzico?order_id=' . $order->id);

            // Buyer (Alıcı) bilgileri
            $buyer = $this->prepareBuyer($order);

            // Adres bilgileri
            $shippingAddress = $this->prepareAddress($order, 'shipping');
            $billingAddress = $this->prepareAddress($order, 'billing');

            // Sepet öğeleri
            $basketItems = $this->prepareBasketItems($order);

            // Taksit
            $enabledInstallments = $options['installments'] ?? [1, 2, 3, 6, 9, 12];

            $request = [
                'locale' => $this->locale,
                'conversationId' => $conversationId,
                'price' => number_format($order->subtotal ?? $order->total, 2, '.', ''),
                'paidPrice' => number_format($order->total, 2, '.', ''),
                'currency' => $this->currency,
                'basketId' => $basketId,
                'paymentGroup' => 'PRODUCT',
                'callbackUrl' => $callbackUrl,
                'enabledInstallments' => $enabledInstallments,
                'buyer' => $buyer,
                'shippingAddress' => $shippingAddress,
                'billingAddress' => $billingAddress,
                'basketItems' => $basketItems,
            ];

            $response = $this->makeRequest('/payment/iyzipos/checkoutform/initialize/auth/ecom', $request);

            if (($response['status'] ?? '') === 'success' && isset($response['token'])) {
                return [
                    'success' => true,
                    'checkout_token' => $response['token'],
                    'checkout_url' => $response['paymentPageUrl'] ?? null,
                    'html' => $response['checkoutFormContent'] ?? '',
                ];
            }

            Log::error('iyzico initiatePayment failed', [
                'order_id' => $order->id,
                'response' => $response,
            ]);

            return [
                'success' => false,
                'error' => $response['errorMessage'] ?? 'iyzico ödeme başlatılamadı',
                'error_code' => $response['errorCode'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('iyzico initiatePayment exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'iyzico bağlantı hatası: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, transaction_id?: string, status?: string, error?: string, raw_response?: array<string, mixed>}
     */
    public function verifyPayment(array $payload): array
    {
        try {
            $token = $payload['token'] ?? '';

            if (empty($token)) {
                return [
                    'success' => false,
                    'error' => 'Token bulunamadı',
                ];
            }

            $request = [
                'locale' => $this->locale,
                'conversationId' => $payload['conversationId'] ?? Str::uuid()->toString(),
                'token' => $token,
            ];

            $response = $this->makeRequest('/payment/iyzipos/checkoutform/auth/ecom/detail', $request);

            if (($response['status'] ?? '') === 'success' && ($response['paymentStatus'] ?? '') === 'SUCCESS') {
                return [
                    'success' => true,
                    'transaction_id' => $response['paymentId'] ?? $token,
                    'status' => 'completed',
                    'raw_response' => $response,
                ];
            }

            return [
                'success' => false,
                'status' => 'failed',
                'error' => $response['errorMessage'] ?? 'Ödeme doğrulanamadı',
                'error_code' => $response['errorCode'] ?? null,
                'raw_response' => $response,
            ];
        } catch (\Throwable $e) {
            Log::error('iyzico verifyPayment exception', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return [
                'success' => false,
                'error' => 'Doğrulama hatası: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * @return array{success: bool, refund_id?: string, error?: string}
     */
    public function refund(Payment $payment, ?float $amount = null): array
    {
        try {
            $refundAmount = $amount ?? $payment->amount;

            $request = [
                'locale' => $this->locale,
                'conversationId' => Str::uuid()->toString(),
                'paymentTransactionId' => $payment->transaction_id,
                'price' => number_format($refundAmount, 2, '.', ''),
                'currency' => $this->currency,
                'ip' => request()->ip() ?? '127.0.0.1',
            ];

            $response = $this->makeRequest('/payment/refund', $request);

            if (($response['status'] ?? '') === 'success') {
                return [
                    'success' => true,
                    'refund_id' => $response['paymentId'] ?? ('refund_' . Str::random(16)),
                    'refund_amount' => $refundAmount,
                ];
            }

            return [
                'success' => false,
                'error' => $response['errorMessage'] ?? 'İade işlemi başarısız',
            ];
        } catch (\Throwable $e) {
            Log::error('iyzico refund exception', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'İade hatası: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * @return array{success: bool, status?: string, error?: string}
     */
    public function queryPaymentStatus(string $transactionId): array
    {
        try {
            $request = [
                'locale' => $this->locale,
                'conversationId' => Str::uuid()->toString(),
                'paymentId' => $transactionId,
            ];

            $response = $this->makeRequest('/payment/detail', $request);

            if (($response['status'] ?? '') === 'success') {
                return [
                    'success' => true,
                    'status' => strtolower($response['paymentStatus'] ?? 'unknown'),
                ];
            }

            return [
                'success' => false,
                'error' => $response['errorMessage'] ?? 'Durum sorgulanamadı',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => 'Sorgulama hatası: ' . $e->getMessage(),
            ];
        }
    }

    public function getProviderName(): string
    {
        return 'iyzico';
    }

    /**
     * iyzico API isteği yap
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function makeRequest(string $endpoint, array $data): array
    {
        $randomString = microtime(true) . Str::random(20);
        $jsonData = json_encode($data);

        $hashString = $this->apiKey . $randomString . $this->secretKey . $jsonData;
        $hash = base64_encode(sha1($hashString, true));
        $authorizationHeader = 'IYZWSv2 ' . base64_encode("{$this->apiKey}:{$hash}");

        $headers = [
            'Authorization' => $authorizationHeader,
            'x-iyzi-rnd' => $randomString,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $response = Http::withHeaders($headers)
            ->timeout(30)
            ->post($this->baseUrl . $endpoint, $data);

        return $response->json() ?? [];
    }

    /**
     * Alıcı bilgilerini hazırla
     *
     * @return array<string, string>
     */
    protected function prepareBuyer(Order $order): array
    {
        $user = $order->user;
        $nameParts = explode(' ', $user->name ?? 'Müşteri Adı', 2);

        return [
            'id' => (string) $user->id,
            'name' => $nameParts[0],
            'surname' => $nameParts[1] ?? $nameParts[0],
            'gsmNumber' => $user->phone ?? '+905000000000',
            'email' => $user->email ?? 'customer@example.com',
            'identityNumber' => '11111111111',
            'lastLoginDate' => $user->updated_at?->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i:s'),
            'registrationDate' => $user->created_at?->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i:s'),
            'registrationAddress' => $order->billing_address ?? 'Adres belirtilmemiş',
            'ip' => request()->ip() ?? '127.0.0.1',
            'city' => $order->billing_city ?? 'İstanbul',
            'country' => 'Turkey',
            'zipCode' => $order->billing_zip ?? '34000',
        ];
    }

    /**
     * Adres bilgilerini hazırla
     *
     * @return array<string, string>
     */
    protected function prepareAddress(Order $order, string $type): array
    {
        $prefix = $type === 'shipping' ? 'shipping_' : 'billing_';

        return [
            'contactName' => $order->user->name ?? 'Müşteri',
            'city' => $order->{$prefix . 'city'} ?? 'İstanbul',
            'country' => 'Turkey',
            'address' => $order->{$prefix . 'address'} ?? 'Adres belirtilmemiş',
            'zipCode' => $order->{$prefix . 'zip'} ?? '34000',
        ];
    }

    /**
     * Sepet öğelerini iyzico formatına dönüştür
     *
     * @return array<int, array<string, string>>
     */
    protected function prepareBasketItems(Order $order): array
    {
        $items = [];

        foreach ($order->items as $item) {
            $vendor = $item->vendor ?? $order->vendor;

            $items[] = [
                'id' => (string) $item->id,
                'name' => $item->product_name ?? 'Ürün',
                'category1' => $item->category_name ?? 'Genel',
                'category2' => '',
                'itemType' => 'PHYSICAL',
                'price' => number_format($item->total_price ?? ($item->unit_price * $item->quantity), 2, '.', ''),
                'subMerchantKey' => $vendor?->iyzico_submerchant_key ?? '',
                'subMerchantPrice' => number_format($item->vendor_amount ?? $item->total_price ?? 0, 2, '.', ''),
            ];
        }

        if (count($items) === 0) {
            $items[] = [
                'id' => 'order_' . $order->id,
                'name' => 'Sipariş #' . $order->order_number,
                'category1' => 'Genel',
                'itemType' => 'PHYSICAL',
                'price' => number_format($order->total, 2, '.', ''),
            ];
        }

        return $items;
    }
}
