<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadgeRule extends Model
{
    /** @use HasFactory<\Database\Factories\BadgeRuleFactory> */
    use HasFactory;

    protected $fillable = [
        'badge_definition_id',
        'category_group_id',
        'condition_type',
        'condition_config',
        'priority',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'condition_config' => 'array',
            'priority' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function badgeDefinition(): BelongsTo
    {
        return $this->belongsTo(BadgeDefinition::class);
    }

    public function categoryGroup(): BelongsTo
    {
        return $this->belongsTo(CategoryGroup::class);
    }

    /**
     * Kural koşulunu değerlendir
     */
    public function evaluate(Product $product): bool
    {
        $config = $this->condition_config;
        $operator = $config['operator'] ?? '=';
        $expectedValue = $config['value'] ?? null;
        $field = $config['field'] ?? null;

        $actualValue = match ($this->condition_type) {
            'price_discount' => $product->discount_percentage,
            'review_count' => $product->reviews_count,
            'rating' => $product->rating,
            'stock' => $product->stock,
            'is_new' => $product->is_new,
            'is_bestseller' => $product->is_bestseller,
            'price' => $product->price,
            'discount_price' => $product->discount_price,
            'custom' => $field ? $product->{$field} : null,
            default => null,
        };

        return match ($operator) {
            '=' => $actualValue == $expectedValue,
            '!=' => $actualValue != $expectedValue,
            '>' => $actualValue > $expectedValue,
            '>=' => $actualValue >= $expectedValue,
            '<' => $actualValue < $expectedValue,
            '<=' => $actualValue <= $expectedValue,
            'in' => is_array($expectedValue) && in_array($actualValue, $expectedValue),
            'contains' => is_string($actualValue) && str_contains($actualValue, $expectedValue),
            default => false,
        };
    }
}
