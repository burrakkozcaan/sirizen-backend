<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'color',
        'size',
        'stock',
        'price',
        'sale_price',
        'original_price',
        'is_default',
        'is_active',
        'weight',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'stock' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'weight' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productSellers(): HasMany
    {
        return $this->hasMany(ProductSeller::class, 'variant_id');
    }
}
