<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'vendor_id',
        'product_seller_id',
        'product_id',
        'variant_id',
        'variant_snapshot',
        'quantity',
        'unit_price',
        'price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'price' => 'decimal:2',
            'variant_snapshot' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function productSeller(): BelongsTo
    {
        return $this->belongsTo(ProductSeller::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    public function commission(): HasOne
    {
        return $this->hasOne(Commission::class);
    }

    public function getVariantInfoAttribute(): ?string
    {
        if ($this->variant_snapshot) {
            $parts = [];
            if (isset($this->variant_snapshot['color'])) {
                $parts[] = $this->variant_snapshot['color'];
            }
            if (isset($this->variant_snapshot['size'])) {
                $parts[] = $this->variant_snapshot['size'];
            }

            return implode(' / ', $parts) ?: null;
        }

        return $this->variant?->color.' / '.$this->variant?->size;
    }

    /**
     * Alias for price for backward compatibility
     */
    public function getTotalPriceAttribute(): float
    {
        return (float) $this->price;
    }
}
