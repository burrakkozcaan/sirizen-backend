<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryGroup extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryGroupFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'icon',
        'color',
        'metadata',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function attributeSets(): HasMany
    {
        return $this->hasMany(AttributeSet::class);
    }

    public function badgeRules(): HasMany
    {
        return $this->hasMany(BadgeRule::class);
    }

    public function badgeTranslations(): HasMany
    {
        return $this->hasMany(BadgeTranslation::class);
    }

    public function attributeHighlights(): HasMany
    {
        return $this->hasMany(AttributeHighlight::class);
    }

    public function pdpLayouts(): HasMany
    {
        return $this->hasMany(PdpLayout::class);
    }

    public function socialProofRules(): HasMany
    {
        return $this->hasMany(SocialProofRule::class);
    }

    public function filterConfigs(): HasMany
    {
        return $this->hasMany(FilterConfig::class);
    }

    /**
     * Varsayılan PDP layout'u getir
     */
    public function defaultPdpLayout(): ?PdpLayout
    {
        return $this->pdpLayouts()->where('is_default', true)->where('is_active', true)->first()
            ?? $this->pdpLayouts()->where('is_active', true)->first();
    }
}
