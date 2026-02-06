<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentGatewaySetting;
use App\PaymentProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PayTRService
{
    private PaymentGatewaySetting $gateway;

    public function __construct()
    {
        $this->gateway = PaymentGatewaySetting::forProvider(PaymentProvider::Paytr)
            ?? throw new \RuntimeException('PayTR gateway ayarları bulunamadı');
    }

    /**
     * PayTR ödeme token'ı oluştur
     */
    public function createPaymentToken(Order $order, array $customerData): array
    {
        $config = $this->gateway->getConfig();
        
        $merchantId = $config['merchant_id'] ?? null;
        $merchantKey = $config['merchant_key'] ?? null;
        $merchantSalt = $config['merchant_salt'] ?? null;

        if (!$merchantId || !$merchantKey || !$merchantSalt) {
            throw new \RuntimeException('PayTR kimlik bilgileri eksik');
        }

        // PayTR için gerekli bilgiler
        $email = $customerData['email'] ?? $order->user->email;
        $paymentAmount = (int) ($order->total_price * 100); // Kuruş cinsinden
        $merchantOid = 'ORDER-' . $order->id . '-' . time();
        $userName = $customerData['name'] ?? $order->user->name ?? 'Müşteri';
        $userAddress = $customerData['address'] ?? '';
        $userPhone = $customerData['phone'] ?? $order->address->phone ?? '';
        $userBasket = $this->formatBasket($order);
        $installment = $customerData['installment'] ?? 0; // 0 = tek çekim
        $currency = 'TL';
        $testMode = $config['test_mode'] ?? false ? 1 : 0;

        // PayTR hash oluştur
        $hashStr = $merchantId . $merchantOid . $email . $paymentAmount . $userBasket . $installment . $currency . $testMode;
        $token = base64_encode(hash_hmac('sha256', $hashStr . $merchantSalt, $merchantKey, true));

        // PayTR API'ye istek gönder
        $postData = [
            'merchant_id' => $merchantId,
            'merchant_key' => $merchantKey,
            'merchant_salt' => $merchantSalt,
            'email' => $email,
            'payment_amount' => $paymentAmount,
            'merchant_oid' => $merchantOid,
            'user_name' => $userName,
            'user_address' => $userAddress,
            'user_phone' => $userPhone,
            'user_basket' => $userBasket,
            'installment_count' => $installment,
            'currency' => $currency,
            'test_mode' => $testMode,
            'merchant_ok_url' => config('app.frontend_url') . '/checkout/success?order_id=' . $order->id,
            'merchant_fail_url' => config('app.frontend_url') . '/checkout/failed?order_id=' . $order->id,
            'timeout_limit' => 30,
            'lang' => 'tr',
        ];

        // PayTR API'ye POST isteği
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.paytr.com/odeme/api/get-token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            Log::error('PayTR API Error', [
                'http_code' => $httpCode,
                'response' => $response,
            ]);
            throw new \RuntimeException('PayTR API hatası: ' . $response);
        }

        $result = json_decode($response, true);

        if ($result['status'] === 'success') {
            // Order'a merchant_oid'i kaydet
            $order->update([
                'payment_reference' => $merchantOid,
                'payment_provider' => PaymentProvider::Paytr->value,
            ]);

            return [
                'token' => $result['token'],
                'merchant_oid' => $merchantOid,
                'iframe_url' => 'https://www.paytr.com/odeme/guvenli/' . $result['token'],
            ];
        }

        throw new \RuntimeException('PayTR token oluşturulamadı: ' . ($result['reason'] ?? 'Bilinmeyen hata'));
    }

    /**
     * PayTR callback'i doğrula
     */
    public function verifyCallback(array $data): bool
    {
        $config = $this->gateway->getConfig();
        
        $merchantKey = $config['merchant_key'] ?? null;
        $merchantSalt = $config['merchant_salt'] ?? null;

        if (!$merchantKey || !$merchantSalt) {
            return false;
        }

        // PayTR hash doğrulama
        $hashStr = $data['merchant_oid'] . $merchantSalt . $data['status'] . $data['total_amount'];
        $hash = base64_encode(hash_hmac('sha256', $hashStr, $merchantKey, true));

        return hash_equals($hash, $data['hash']);
    }

    /**
     * Sepet bilgisini PayTR formatına çevir
     */
    private function formatBasket(Order $order): string
    {
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                $item->product->title ?? 'Ürün',
                (float) $item->price,
                $item->quantity,
            ];
        }

        return base64_encode(json_encode($items));
    }

    /**
     * Ödeme durumunu kontrol et
     */
    public function checkPaymentStatus(string $merchantOid): array
    {
        $config = $this->gateway->getConfig();
        
        $merchantId = $config['merchant_id'] ?? null;
        $merchantKey = $config['merchant_key'] ?? null;
        $merchantSalt = $config['merchant_salt'] ?? null;

        if (!$merchantId || !$merchantKey || !$merchantSalt) {
            throw new \RuntimeException('PayTR kimlik bilgileri eksik');
        }

        // PayTR sorgulama API'si
        $hashStr = $merchantId . $merchantOid . $merchantSalt;
        $hash = base64_encode(hash_hmac('sha256', $hashStr, $merchantKey, true));

        $postData = [
            'merchant_id' => $merchantId,
            'merchant_oid' => $merchantOid,
            'hash' => $hash,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.paytr.com/odeme/durum-sorgula');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \RuntimeException('PayTR sorgulama hatası');
        }

        return json_decode($response, true);
    }
}
