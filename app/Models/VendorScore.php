<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorScore extends Model
{
    /** @use HasFactory<\Database\Factories\VendorScoreFactory> */
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'total_score',
        'delivery_score',
        'rating_score',
        'stock_score',
        'support_score',
    ];

    protected function casts(): array
    {
        return [
            'total_score' => 'decimal:2',
            'delivery_score' => 'decimal:2',
            'rating_score' => 'decimal:2',
            'stock_score' => 'decimal:2',
            'support_score' => 'decimal:2',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
