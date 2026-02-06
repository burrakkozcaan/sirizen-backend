<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewImage extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewImageFactory> */
    use HasFactory;

    protected $fillable = [
        'product_review_id',
        'image_path',
        'alt_text',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function productReview(): BelongsTo
    {
        return $this->belongsTo(ProductReview::class);
    }
}
