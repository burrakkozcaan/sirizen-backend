<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorPenalty extends Model
{
    /** @use HasFactory<\Database\Factories\VendorPenaltyFactory> */
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'reason',
        'penalty_points',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'penalty_points' => 'integer',
            'expires_at' => 'datetime',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
