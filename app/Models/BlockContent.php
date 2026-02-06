<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockContent extends Model
{
    protected $fillable = [
        'block_id',
        'title',
        'description',
        'icon',
        'image',
        'color',
        'cta_text',
        'cta_link',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(ProductBlock::class, 'block_id');
    }
}
