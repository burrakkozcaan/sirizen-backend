<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuickLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'icon',
        'link_type',
        'path',
        'category_slug',
        'campaign_slug',
        'product_id',
        'color',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_slug', 'slug');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_slug', 'slug');
    }
}
