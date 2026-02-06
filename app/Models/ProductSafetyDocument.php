<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSafetyDocument extends Model
{
    /** @use HasFactory<\Database\Factories\ProductSafetyDocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'title',
        'file',
        'order',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
