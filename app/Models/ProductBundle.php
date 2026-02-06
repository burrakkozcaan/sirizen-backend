<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductBundle extends Model
{
    protected $fillable = [
        'main_product_id',
        'title',
        'bundle_type',
        'discount_rate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function mainProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'main_product_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_bundle_items', 'bundle_id', 'product_id')
            ->withPivot('order')
            ->withTimestamps()
            ->orderBy('product_bundle_items.order');
    }
}
