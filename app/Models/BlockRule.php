<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockRule extends Model
{
    protected $fillable = [
        'block_id',
        'rule_type',
        'operator',
        'value',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(ProductBlock::class, 'block_id');
    }
}
