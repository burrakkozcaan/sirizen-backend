<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductQuestion extends Model
{
    /** @use HasFactory<\Database\Factories\ProductQuestionFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'vendor_id',
        'question',
        'answer',
        'answered_by_vendor',
    ];

    protected function casts(): array
    {
        return [
            'answered_by_vendor' => 'boolean',
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
}
