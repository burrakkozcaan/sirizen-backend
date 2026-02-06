<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'amount',
        'payout_method',
        'status',
        'period_start',
        'period_end',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'period_start' => 'date',
            'period_end' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
