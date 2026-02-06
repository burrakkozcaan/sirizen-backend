<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    /** @use HasFactory<\Database\Factories\RefundFactory> */
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'user_id',
        'vendor_id',
        'reason',
        'status',
        'refund_amount',
    ];

    protected function casts(): array
    {
        return [
            'refund_amount' => 'decimal:2',
        ];
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
