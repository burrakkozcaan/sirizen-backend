<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBadge extends Model
{
    /** @use HasFactory<\Database\Factories\ProductBadgeFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'label',
        'color',
        'icon',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
