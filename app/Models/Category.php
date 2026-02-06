<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'category_group_id',
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'header_text',
        'footer_text',
        'canonical_url',
        'icon',
        'image',
        'order',
        'is_active',
        'commission_rate',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_active' => 'boolean',
            'commission_rate' => 'decimal:2',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'category_vendor');
    }

    public function categoryGroup(): BelongsTo
    {
        return $this->belongsTo(CategoryGroup::class);
    }

    public function attributeSets(): BelongsToMany
    {
        return $this->belongsToMany(AttributeSet::class, 'category_attribute_set')
            ->withPivot('is_required')
            ->withTimestamps();
    }
}
