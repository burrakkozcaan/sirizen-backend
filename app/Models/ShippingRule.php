<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingRule extends Model
{
    protected $fillable = [
        'vendor_id',
        'user_id',
        'address_id',
        'cutoff_time',
        'same_day_shipping',
        'free_shipping',
        'free_shipping_min_amount',
    ];

    protected function casts(): array
    {
        return [
            'same_day_shipping' => 'boolean',
            'free_shipping' => 'boolean',
            'free_shipping_min_amount' => 'decimal:2',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
