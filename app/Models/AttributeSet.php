<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeSet extends Model
{
    /** @use HasFactory<\Database\Factories\AttributeSetFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'category_group_id',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function categoryGroup(): BelongsTo
    {
        return $this->belongsTo(CategoryGroup::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_attribute_set')
            ->withPivot('is_required')
            ->withTimestamps();
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class)->orderBy('order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
