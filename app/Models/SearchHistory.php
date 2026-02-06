<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchHistory extends Model
{
    /** @use HasFactory<\Database\Factories\SearchHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'query',
        'results_count',
        'searched_at',
    ];

    protected function casts(): array
    {
        return [
            'results_count' => 'integer',
            'searched_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
