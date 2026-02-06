<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBadgeSnapshot extends Model
{
    /** @use HasFactory<\Database\Factories\ProductBadgeSnapshotFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'badge_definition_id',
        'label',
        'icon',
        'color',
        'bg_color',
        'text_color',
        'priority',
        'calculated_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'calculated_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function badgeDefinition(): BelongsTo
    {
        return $this->belongsTo(BadgeDefinition::class);
    }

    /**
     * API response için formatla
     */
    public function toArray(): array
    {
        return [
            'key' => $this->badgeDefinition?->key ?? 'unknown',
            'label' => $this->label,
            'icon' => $this->icon,
            'color' => $this->color,
            'bg_color' => $this->bg_color,
            'text_color' => $this->text_color,
            'priority' => $this->priority,
        ];
    }
}
