<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeValue extends Model
{
    /** @use HasFactory<\Database\Factories\ProductAttributeValueFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'attribute_id',
        'value',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Formatlı değer getir
     */
    public function getFormattedValue(): string
    {
        $value = $this->value;
        $unit = $this->attribute?->unit;

        return $unit ? "{$value} {$unit}" : $value;
    }
}
