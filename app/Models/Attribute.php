<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    /** @use HasFactory<\Database\Factories\AttributeFactory> */
    use HasFactory;

    protected $fillable = [
        'attribute_set_id',
        'key',
        'label',
        'type',
        'options',
        'unit',
        'is_filterable',
        'is_required',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_filterable' => 'boolean',
            'is_required' => 'boolean',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function attributeSet(): BelongsTo
    {
        return $this->belongsTo(AttributeSet::class);
    }

    public function productValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(AttributeHighlight::class);
    }

    public function filterConfigs(): HasMany
    {
        return $this->hasMany(FilterConfig::class);
    }

    /**
     * Options listesini formatlı getir
     */
    public function getFormattedOptions(): array
    {
        if (! $this->options) {
            return [];
        }

        return collect($this->options)->map(fn ($option) => [
            'value' => is_array($option) ? ($option['value'] ?? $option) : $option,
            'label' => is_array($option) ? ($option['label'] ?? $option['value'] ?? $option) : $option,
        ])->toArray();
    }
}
