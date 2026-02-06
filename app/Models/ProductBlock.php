<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductBlock extends Model
{
    protected $fillable = [
        'product_id',
        'block_type',
        'position',
        'priority',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'priority' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(BlockRule::class, 'block_id');
    }

    public function content(): HasOne
    {
        return $this->hasOne(BlockContent::class, 'block_id');
    }
}
