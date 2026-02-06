<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_total_orders',
        'min_rating',
        'max_cancel_rate',
        'max_return_rate',
        'commission_rate',
        'max_products',
        'description',
        'priority_boost',
        'badge_icon',
    ];

    protected function casts(): array
    {
        return [
            'min_rating' => 'decimal:2',
            'max_cancel_rate' => 'decimal:2',
            'max_return_rate' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'max_products' => 'integer',
        ];
    }

    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class, 'tier_id');
    }
}
