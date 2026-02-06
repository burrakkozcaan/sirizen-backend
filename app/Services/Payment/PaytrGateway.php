<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaytrGateway implements PaymentGatewayInterface
{
    protected string $merchantId;

    protected string $merchantKey;

    protected string $merchantSalt;

    protected string $baseUrl;

    protected bool $testMode;

    protected int $timeout;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(array $config = [])
    {
        $config = array_merge(config('payment.gateways.paytr', []), $config);

        $this->merchantId = $config['merchant_id'] ?? '';
        $this->merchantKey = $config['merchant_key'] ?? '';
        $this->merchantSalt = $config['merchant_salt'] ?? '';
        $this->baseUrl = $config['base_url'] ?? 'https://www.paytr.com';
        $this->testMode = $config['test_mode'] ?? true;
        $this->timeout = $config['timeout'] ?? 30;
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array{success: bool, checkout_token?: string, checkout_url?: string, html?: string, error?: string}
     */
    public function initiatePayment(Order $order, array $options = []): array
    {
        try {
            $merchantOid = $order->order_number . '_' . time();
            $userIp = request()->ip() ?? '127.0.0.1';
            $email = $order->user->email ?? 'customer@example.com';
            $paymentAmount = (int) ($order->total * 100); // Kuruş cinsinden
            $currency = $options['currency'] ?? 'TL';

            // Kullanıcı bilgileri
            $userName = $order->user->name ?? 'Müşteri';
            $userAddress = $order->billing_address ?? 'Adres belirtilmemiş';
            $userPhone = $order->user->phone ?? '0000000000';

            // Sepet içeriği
            $basketItems = $this->prepareBasketItems($order);
            $userBasket = base64_encode(json_encode($basketItems));

            // Taksit ayarları
            $installmentCount = $options['installment'] ?? 0;
            $noInstallment = $installmentCount > 0 ? 0 : 1;
            $maxInstallment = config('payment.gateways.paytr.max_installment', 12);

            // Callback URL'leri
            $merchantOkUrl = url(config('payment.callbacks.success_url') . '?order_id=' . $order->id);
            $merchantFailUrl = url(config('payment.callbacks.fail_url') . '?order_id=' . $order->id);

            // Test modu
            $debugOn = $this->testMode ? 1 : 0;
            $testMode = $this->testMode ? 1 : 0;

            // PayTR hash token
            $hashStr = $this->merchantId .
                $userIp .
                $merchantOid .
                $email .
                $paymentAmount .
                $userBasket .
                $noInstallment .
                $maxInstallment .
                $currency .
                $testMode;

            $paytrToken = base64_encode(hash_hmac('sha256', $hashStr . $this->merchantSalt, $this->merchantKey, true));

            $postData = [
                'merchant_id' => $this->merchantId,
                'user_ip' => $userIp,
                'merchant_oid' => $merchantOid,
                'email' => $email,
                'payment_amount' => $paymentAmount,
                'paytr_token' => $paytrToken,
                'user_basket' => $userBasket,
                'debug_on' => $debugOn,
                'no_installment' => $noInstallment,
                'max_installment' => $maxInstallment,
                'user_name' => $userName,
                'user_address' => $userAddress,
                'user_phone' => $userPhone,
                'merchant_ok_url' => $merchantOkUrl,
                'merchant_fail_url' => $merchantFailUrl,
                'timeout_limit' => 30,
                'currency' => $currency,
                'test_mode' => $testMode,
                'lang' => config('payment.gateways.paytr.lang', 'tr'),
            ];

            $response = Http::timeout($this->timeout)
                ->asForm()
                ->post($this->baseUrl . '/odeme/api/get-token', $postData);

            $result = $response->json();

            if (($result['status'] ?? '') === 'success' && isset($result['token'])) {
                $iframeUrl = $this->baseUrl . '/odeme/guvenli/' . $result['token'];

                return [
                    'success' => true,
                    'checkout_token' => $result['token'],
                    'checkout_url' => $iframeUrl,
                    'html' => $this->generateIframeHtml($iframeUrl),
                    'merchant_oid' => $merchantOid,
                ];
            }

            Log::error('PayTR initiatePayment failed', [
                'order_id' => $order->id,
                'response' => $result,
            ]);

            return [
                'success' => false,
                'error' => $result['reason'] ?? 'PayTR token alınamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('PayTR initiatePayment exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'PayTR bağlantı hatası: ' . $e->getMessage(),
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
            $merchantOid = $payload['merchant_oid'] ?? '';
            $status = $payload['status'] ?? '';
            $totalAmount = $payload['total_amount'] ?? '';
            $hash = $payload['hash'] ?? '';

            // Hash doğrulama
            $hashStr = $merchantOid . $this->merchantSalt . $status . $totalAmount;
            $expectedHash = base64_encode(hash_hmac('sha256', $hashStr, $this->merchantKey, true));

            if ($hash !== $expectedHash) {
                Log::warning('PayTR invalid hash', [
                    'merchant_oid' => $merchantOid,
                    'received_hash' => $hash,
                ]);

                return [
                    'success' => false,
                    'error' => 'Geçersiz hash değeri',
                    'raw_response' => $payload,
                ];
            }

            if ($status === 'success') {
                return [
                    'success' => true,
                    'transaction_id' => $merchantOid,
                    'status' => 'completed',
                    'raw_response' => $payload,
                ];
            }

            $failedReasonCode = $payload['failed_reason_code'] ?? '';
            $failedReasonMsg = $payload['failed_reason_msg'] ?? 'Ödeme başarısız';

            return [
                'success' => false,
                'status' => 'failed',
                'error' => $failedReasonMsg,
                'error_code' => $failedReasonCode,
                'raw_response' => $payload,
            ];
        } catch (\Throwable $e) {
            Log::error('PayTR verifyPayment exception', [
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
            $refundAmount = (int) (($amount ?? $payment->amount) * 100);
            $merchantOid = $payment->transaction_id;

            $hashStr = $this->merchantId . $merchantOid . $refundAmount . $this->merchantSalt;
            $paytrToken = base64_encode(hash_hmac('sha256', $hashStr, $this->merchantKey, true));

            $postData = [
                'merchant_id' => $this->merchantId,
                'merchant_oid' => $merchantOid,
                'return_amount' => $refundAmount,
                'paytr_token' => $paytrToken,
            ];

            $response = Http::timeout($this->timeout)
                ->asForm()
                ->post($this->baseUrl . '/odeme/iade', $postData);

            $result = $response->json();

            if (($result['status'] ?? '') === 'success') {
                return [
                    'success' => true,
                    'refund_id' => $result['return_id'] ?? ('refund_' . Str::random(16)),
                    'refund_amount' => $amount ?? $payment->amount,
                ];
            }

            return [
                'success' => false,
                'error' => $result['err_msg'] ?? 'İade işlemi başarısız',
            ];
        } catch (\Throwable $e) {
            Log::error('PayTR refund exception', [
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
        // PayTR'de doğrudan durum sorgulama API'si sınırlıdır
        // Genellikle callback mekanizması kullanılır
        return [
            'success' => true,
            'status' => 'unknown',
            'message' => 'PayTR durum sorgulama webhook üzerinden yapılır',
        ];
    }

    public function getProviderName(): string
    {
        return 'paytr';
    }

    /**
     * Sepet öğelerini PayTR formatına dönüştür
     *
     * @return array<int, array<int, string|int>>
     */
    protected function prepareBasketItems(Order $order): array
    {
        $items = [];

        foreach ($order->items as $item) {
            $items[] = [
                $item->product_name ?? 'Ürün',
                number_format($item->unit_price, 2, '.', ''),
                $item->quantity,
            ];
        }

        if (count($items) === 0) {
            $items[] = [
                'Sipariş #' . $order->order_number,
                number_format($order->total, 2, '.', ''),
                1,
            ];
        }

        return $items;
    }

    /**
     * PayTR iframe HTML'i oluştur
     */
    protected function generateIframeHtml(string $iframeUrl): string
    {
        return <<<HTML
        <iframe src="{$iframeUrl}" id="paytriframe" frameborder="0" scrolling="no" style="width: 100%; min-height: 500px;"></iframe>
        <script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
        <script>iFrameResize({}, '#paytriframe');</script>
        HTML;
    }
}
