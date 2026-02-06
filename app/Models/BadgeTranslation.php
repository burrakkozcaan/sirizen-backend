<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadgeTranslation extends Model
{
    /** @use HasFactory<\Database\Factories\BadgeTranslationFactory> */
    use HasFactory;

    protected $fillable = [
        'badge_definition_id',
        'category_group_id',
        'label',
        'icon',
        'color',
        'bg_color',
        'text_color',
    ];

    public function badgeDefinition(): BelongsTo
    {
        return $this->belongsTo(BadgeDefinition::class);
    }

    public function categoryGroup(): BelongsTo
    {
        return $this->belongsTo(CategoryGroup::class);
    }
}
