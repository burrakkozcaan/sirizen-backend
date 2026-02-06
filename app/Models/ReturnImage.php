<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnImage extends Model
{
    /** @use HasFactory<\Database\Factories\ReturnImageFactory> */
    use HasFactory;

    protected $fillable = [
        'product_return_id',
        'image',
    ];

    public function productReturn(): BelongsTo
    {
        return $this->belongsTo(ProductReturn::class);
    }
}
