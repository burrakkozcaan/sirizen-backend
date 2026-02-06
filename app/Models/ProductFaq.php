<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFaq extends Model
{
    protected $fillable = [
        'product_id',
        'vendor_id',
        'category_id',
        'question',
        'answer',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
