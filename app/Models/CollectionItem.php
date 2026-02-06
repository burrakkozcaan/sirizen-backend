<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionItem extends Model
{
    protected $fillable = [
        'collection_id',
        'product_id',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
