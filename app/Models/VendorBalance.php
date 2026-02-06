<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'balance',
        'available_balance',
        'pending_balance',
        'total_earnings',
        'total_withdrawn',
        'currency',
        'last_settlement_at',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'available_balance' => 'decimal:2',
            'pending_balance' => 'decimal:2',
            'total_earnings' => 'decimal:2',
            'total_withdrawn' => 'decimal:2',
            'last_settlement_at' => 'datetime',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
