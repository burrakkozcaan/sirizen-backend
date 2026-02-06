<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    /** @use HasFactory<\Database\Factories\CommissionFactory> */
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'vendor_id',
        'order_item_id',
        'gross_amount',
        'commission_rate',
        'commission_amount',
        'net_amount',
        'currency',
        'refunded_amount',
        'status',
        'settled_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'refunded_amount' => 'decimal:2',
            'settled_at' => 'datetime',
        ];
    }

    /**
     * Komisyon kesinleşti mi?
     */
    public function isSettled(): bool
    {
        return $this->settled_at !== null;
    }


    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
