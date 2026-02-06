<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    /** @use HasFactory<\Database\Factories\CartItemFactory> */
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_seller_id',
        'quantity',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'cart_id' => 'integer',
            'product_id' => 'integer',
            'product_seller_id' => 'integer',
            'quantity' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productSeller(): BelongsTo
    {
        return $this->belongsTo(ProductSeller::class);
    }
}
