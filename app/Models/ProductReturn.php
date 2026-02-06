<?php

namespace App\Models;

use App\ReturnReason;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductReturn extends Model
{
    /** @use HasFactory<\Database\Factories\ProductReturnFactory> */
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'reason',
        'reason_description',
        'status',
        'user_id',
        'vendor_id',
        'refund_amount',
        'tracking_number',
        'carrier',
        'condition_status',
        'received_at',
        'requested_at',
        'approved_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'reason' => ReturnReason::class,
            'received_at' => 'datetime',
            'requested_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
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

    public function returnImages(): HasMany
    {
        return $this->hasMany(ReturnImage::class);
    }
}
