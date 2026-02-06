<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SellerBadge extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'seller_badge_assignments', 'badge_id', 'vendor_id')
            ->withTimestamps()
            ->withPivot(['assigned_at', 'expires_at']);
    }
}
