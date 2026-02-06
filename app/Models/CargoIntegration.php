<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CargoIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_company_id',
        'vendor_id',
        'integration_type',
        'api_endpoint',
        'api_key',
        'api_secret',
        'customer_code',
        'api_credentials',
        'configuration',
        'is_active',
        'is_test_mode',
        'last_sync_at',
        'last_error',
    ];

    protected function casts(): array
    {
        return [
            'api_credentials' => 'encrypted:array',
            'configuration' => 'array',
            'is_active' => 'boolean',
            'is_test_mode' => 'boolean',
            'last_sync_at' => 'datetime',
        ];
    }

    public function shippingCompany(): BelongsTo
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
