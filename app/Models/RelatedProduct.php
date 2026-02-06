<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelatedProduct extends Model
{
    protected $fillable = [
        'product_id',
        'related_product_id',
        'type',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function relatedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'related_product_id');
    }
}
