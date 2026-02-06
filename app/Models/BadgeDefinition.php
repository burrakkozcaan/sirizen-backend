<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BadgeDefinition extends Model
{
    /** @use HasFactory<\Database\Factories\BadgeDefinitionFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'icon',
        'color',
        'bg_color',
        'text_color',
        'priority',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function rules(): HasMany
    {
        return $this->hasMany(BadgeRule::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(BadgeTranslation::class);
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(ProductBadgeSnapshot::class);
    }

    /**
     * Kategori grubuna göre çeviri/getir
     */
    public function getTranslation(?int $categoryGroupId): ?BadgeTranslation
    {
        if (! $categoryGroupId) {
            return null;
        }

        return $this->translations()->where('category_group_id', $categoryGroupId)->first();
    }

    /**
     * Kategori grubuna göre görüntüleme verilerini al
     */
    public function getDisplayData(?int $categoryGroupId = null): array
    {
        $translation = $this->getTranslation($categoryGroupId);

        return [
            'key' => $this->key,
            'label' => $translation?->label ?? $this->label,
            'icon' => $translation?->icon ?? $this->icon,
            'color' => $translation?->color ?? $this->color,
            'bg_color' => $translation?->bg_color ?? $this->bg_color,
            'text_color' => $translation?->text_color ?? $this->text_color,
            'priority' => $this->priority,
        ];
    }
}
