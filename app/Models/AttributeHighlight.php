<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeHighlight extends Model
{
    /** @use HasFactory<\Database\Factories\AttributeHighlightFactory> */
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'category_group_id',
        'display_label',
        'icon',
        'color',
        'priority',
        'show_in_pdp',
        'show_in_list',
    ];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'show_in_pdp' => 'boolean',
            'show_in_list' => 'boolean',
        ];
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function categoryGroup(): BelongsTo
    {
        return $this->belongsTo(CategoryGroup::class);
    }
}
