<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductLiveStat extends Model
{
    protected $fillable = [
        'product_id',
        'view_count',
        'cart_count',
        'purchase_count',
        'view_count_24h',
    ];

    protected function casts(): array
    {
        return [
            'view_count' => 'integer',
            'cart_count' => 'integer',
            'purchase_count' => 'integer',
            'view_count_24h' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
