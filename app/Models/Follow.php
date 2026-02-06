<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Follow extends Model
{
    protected $fillable = [
        'user_id',
        'target_type',
        'target_id',
        'reward_type',
        'reward_value',
    ];

    protected function casts(): array
    {
        return [
            'reward_value' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(FollowReward::class);
    }
}
