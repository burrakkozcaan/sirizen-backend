<?php

declare(strict_types=1);

namespace App\Models;

use App\PaymentProvider;
use Illuminate\Database\Eloquent\Model;

class PaymentGatewaySetting extends Model
{
    protected $fillable = [
        'provider',
        'display_name',
        'is_active',
        'is_test_mode',
        'credentials',
        'configuration',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'provider' => PaymentProvider::class,
            'is_active' => 'boolean',
            'is_test_mode' => 'boolean',
            'credentials' => 'encrypted:array',
            'configuration' => 'array',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Aktif gateway'leri getir
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, self>
     */
    public static function active(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Provider'a göre ayarları getir
     */
    public static function forProvider(string|PaymentProvider $provider): ?self
    {
        $providerValue = $provider instanceof PaymentProvider ? $provider->value : $provider;

        return static::where('provider', $providerValue)->first();
    }

    /**
     * Credential'ları al (decrypted)
     *
     * @return array<string, mixed>
     */
    public function getDecryptedCredentials(): array
    {
        return $this->credentials ?? [];
    }

    /**
     * Konfigürasyonları al
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return array_merge(
            $this->configuration ?? [],
            $this->getDecryptedCredentials(),
            ['test_mode' => $this->is_test_mode]
        );
    }
}
