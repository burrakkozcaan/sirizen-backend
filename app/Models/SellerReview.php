<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerReview extends Model
{
    /** @use HasFactory<\Database\Factories\SellerReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'user_id',
        'delivery_rating',
        'communication_rating',
        'packaging_rating',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'delivery_rating' => 'integer',
            'communication_rating' => 'integer',
            'packaging_rating' => 'integer',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
