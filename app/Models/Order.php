<?php

namespace App\Models;

use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

#[ObservedBy(OrderObserver::class)]
class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_price',
        'status',
        'payment_method',
        'payment_reference',
        'payment_provider',
        'payment_status',
        'paid_at',
        'address_id',
        'reordered_from_order_id',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function shipments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Shipment::class,
            OrderItem::class,
            'order_id',
            'order_item_id',
        );
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function commissions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Commission::class,
            OrderItem::class,
            'order_id', // Foreign key on order_items table
            'order_item_id', // Foreign key on commissions table
            'id', // Local key on orders table
            'id' // Local key on order_items table
        );
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'id', 'order_id');
    }

    /**
     * Alias for total_price for backward compatibility
     */
    public function getTotalAttribute(): float
    {
        return (float) $this->total_price;
    }

    /**
     * Alias for total_price for backward compatibility
     */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->total_price;
    }
}
