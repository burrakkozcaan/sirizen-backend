<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductQuestionCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_question_category');
    }

    public function productQuestions(): HasMany
    {
        return $this->hasMany(ProductQuestion::class);
    }
}
