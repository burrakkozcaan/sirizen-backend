<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAlert extends Model
{
    /** @use HasFactory<\Database\Factories\StockAlertFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'is_active',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'notified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
