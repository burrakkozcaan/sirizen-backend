<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\PaymentProvider;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    /**
     * @var array<string, class-string<PaymentGatewayInterface>>
     */
    protected array $gateways = [
        'test' => TestPaymentGateway::class,
        'paytr' => PaytrGateway::class,
        'iyzico' => IyzicoGateway::class,
    ];

    /**
     * Provider adına göre gateway instance'ı döndür
     *
     * @param  array<string, mixed>  $config
     */
    public function make(string|PaymentProvider $provider, array $config = []): PaymentGatewayInterface
    {
        $providerKey = $provider instanceof PaymentProvider ? $provider->value : $provider;

        if (! isset($this->gateways[$providerKey])) {
            throw new InvalidArgumentException("Desteklenmeyen ödeme sağlayıcısı: {$providerKey}");
        }

        $gatewayClass = $this->gateways[$providerKey];

        return new $gatewayClass($config);
    }

    /**
     * Varsayılan gateway'i döndür
     *
     * @param  array<string, mixed>  $config
     */
    public function default(array $config = []): PaymentGatewayInterface
    {
        $defaultProvider = config('payment.default', 'test');

        return $this->make($defaultProvider, $config);
    }

    /**
     * Gateway'in kullanılabilir olup olmadığını kontrol et
     */
    public function supports(string|PaymentProvider $provider): bool
    {
        $providerKey = $provider instanceof PaymentProvider ? $provider->value : $provider;

        return isset($this->gateways[$providerKey]);
    }

    /**
     * Yeni gateway kaydet
     *
     * @param  class-string<PaymentGatewayInterface>  $gatewayClass
     */
    public function register(string $provider, string $gatewayClass): void
    {
        $this->gateways[$provider] = $gatewayClass;
    }

    /**
     * Kayıtlı tüm provider'ları döndür
     *
     * @return array<string>
     */
    public function getRegisteredProviders(): array
    {
        return array_keys($this->gateways);
    }
}
