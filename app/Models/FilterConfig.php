<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilterConfig extends Model
{
    /** @use HasFactory<\Database\Factories\FilterConfigFactory> */
    use HasFactory;

    protected $fillable = [
        'category_group_id',
        'filter_type',
        'attribute_id',
        'display_label',
        'filter_component',
        'order',
        'is_collapsed',
        'show_count',
        'config',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'order' => 'integer',
            'is_collapsed' => 'boolean',
            'show_count' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function categoryGroup(): BelongsTo
    {
        return $this->belongsTo(CategoryGroup::class);
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Filtre seçeneklerini getir
     */
    public function getOptions(): array
    {
        if ($this->filter_type === 'attribute' && $this->attribute) {
            return $this->attribute->getFormattedOptions();
        }

        return $this->config['options'] ?? [];
    }

    /**
     * Filtre key'ini getir
     */
    public function getFilterKey(): string
    {
        return match ($this->filter_type) {
            'attribute' => $this->attribute?->key ?? 'attr_' . $this->attribute_id,
            'price' => 'price',
            'brand' => 'brand',
            'rating' => 'rating',
            'seller' => 'seller',
            'campaign' => 'campaign',
            default => 'filter_' . $this->id,
        };
    }
}
