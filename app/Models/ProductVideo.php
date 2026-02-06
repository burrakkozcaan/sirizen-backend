<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVideo extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVideoFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'title',
        'url',
        'video_type',
        'order',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
