<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewHelpfulVote extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewHelpfulVoteFactory> */
    use HasFactory;

    protected $fillable = [
        'product_review_id',
        'user_id',
        'is_helpful',
    ];

    protected function casts(): array
    {
        return [
            'is_helpful' => 'boolean',
        ];
    }

    public function productReview(): BelongsTo
    {
        return $this->belongsTo(ProductReview::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
