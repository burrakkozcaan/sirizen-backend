<?php

namespace App\Models;

use App\PaymentProvider;
use App\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'currency',
        'commission_amount',
        'vendor_amount',
        'platform_amount',
        'payment_provider',
        'gateway',
        'method',
        'payment_method',
        'payment_type',
        'status',
        'split_status',
        'transaction_id',
        'checkout_token',
        'callback_status',
        'callback_received_at',
        'gateway_response',
        'error_message',
        'installment',
        'installment_count',
        'refund_id',
        'refunded_amount',
        'refunded_at',
        'paid_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'vendor_amount' => 'decimal:2',
            'platform_amount' => 'decimal:2',
            'refunded_amount' => 'decimal:2',
            'gateway_response' => 'array',
            'metadata' => 'array',
            'installment' => 'integer',
            'installment_count' => 'integer',
            'paid_at' => 'datetime',
            'callback_received_at' => 'datetime',
            'refunded_at' => 'datetime',
            'payment_provider' => PaymentProvider::class,
            'status' => PaymentStatus::class,
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    /**
     * Ödeme başarılı mı?
     */
    public function isSuccessful(): bool
    {
        return $this->status === PaymentStatus::Completed;
    }

    /**
     * İade edilmiş mi?
     */
    public function isRefunded(): bool
    {
        return in_array($this->status, [PaymentStatus::Refunded, PaymentStatus::PartiallyRefunded], true);
    }

    /**
     * Split tutarlarının doğru olup olmadığını kontrol et.
     * commission_amount + vendor_amount = amount olmalı (tolerans: 1 kuruş)
     */
    public function isSplitValid(): bool
    {
        if (! $this->commission_amount || ! $this->vendor_amount) {
            return true; // Henüz split yapılmamış, geçerli
        }

        $splitTotal = round((float) $this->commission_amount + (float) $this->vendor_amount, 2);
        $amount     = round((float) $this->amount, 2);

        return abs($splitTotal - $amount) <= 0.01;
    }
}
