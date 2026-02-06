<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingCompany extends Model
{
    protected $fillable = [
        'name',
        'code',
        'logo',
        'tracking_url',
        'api_url',
        'api_credentials',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'api_credentials' => 'encrypted:array',
        ];
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
