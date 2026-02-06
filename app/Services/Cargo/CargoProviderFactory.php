<?php

declare(strict_types=1);

namespace App\Services\Cargo;

use App\CargoProvider;
use App\Contracts\CargoProviderInterface;
use InvalidArgumentException;

class CargoProviderFactory
{
    /**
     * @var array<string, class-string<CargoProviderInterface>>
     */
    protected array $providers = [
        'aras' => ArasKargoProvider::class,
        'yurtici' => YurticiKargoProvider::class,
        'mng' => MngKargoProvider::class,
    ];

    /**
     * Provider adına göre cargo provider instance'ı döndür
     *
     * @param  array<string, mixed>  $config
     */
    public function make(string|CargoProvider $provider, array $config = []): CargoProviderInterface
    {
        $providerKey = $provider instanceof CargoProvider ? $provider->value : $provider;

        if (! isset($this->providers[$providerKey])) {
            throw new InvalidArgumentException("Desteklenmeyen kargo sağlayıcısı: {$providerKey}");
        }

        $providerClass = $this->providers[$providerKey];

        return new $providerClass($config);
    }

    /**
     * Varsayılan provider'ı döndür
     *
     * @param  array<string, mixed>  $config
     */
    public function default(array $config = []): CargoProviderInterface
    {
        $defaultProvider = config('cargo.default', 'aras');

        return $this->make($defaultProvider, $config);
    }

    /**
     * Provider'ın kullanılabilir olup olmadığını kontrol et
     */
    public function supports(string|CargoProvider $provider): bool
    {
        $providerKey = $provider instanceof CargoProvider ? $provider->value : $provider;

        return isset($this->providers[$providerKey]);
    }

    /**
     * Yeni provider kaydet
     *
     * @param  class-string<CargoProviderInterface>  $providerClass
     */
    public function register(string $provider, string $providerClass): void
    {
        $this->providers[$provider] = $providerClass;
    }

    /**
     * Kayıtlı tüm provider'ları döndür
     *
     * @return array<string>
     */
    public function getRegisteredProviders(): array
    {
        return array_keys($this->providers);
    }
}
