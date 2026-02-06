<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductReview extends Model
{
    /** @use HasFactory<\Database\Factories\ProductReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'vendor_id',
        'rating',
        'comment',
        'vendor_response',
        'vendor_response_at',
        'is_verified_purchase',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_verified_purchase' => 'boolean',
            'vendor_response_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function helpfulVotes(): HasMany
    {
        return $this->hasMany(ReviewHelpfulVote::class);
    }

    public function reviewImages(): HasMany
    {
        return $this->hasMany(ReviewImage::class);
    }
}
