<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimilarProduct extends Model
{
    protected $fillable = [
        'product_id',
        'similar_product_id',
        'score',
        'relation_type',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function similarProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'similar_product_id');
    }
}
