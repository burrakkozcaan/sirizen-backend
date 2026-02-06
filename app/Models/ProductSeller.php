<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSeller extends Model
{
    /** @use HasFactory<\Database\Factories\ProductSellerFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_id',
        'vendor_id',
        'seller_sku',
        'price',
        'sale_price',
        'stock',
        'dispatch_days',
        'shipping_type',
        'free_shipping',
        'is_featured',
        'is_buybox_winner',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock' => 'integer',
            'dispatch_days' => 'integer',
            'is_featured' => 'boolean',
            'free_shipping' => 'boolean',
            'is_buybox_winner' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }
}
