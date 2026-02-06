<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    /** @use HasFactory<\Database\Factories\ShipmentFactory> */
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'shipping_company_id',
        'cargo_integration_id',
        'order_id',
        'address_id',
        'vendor_id',
        'tracking_number',
        'carrier',
        'status',
        'tracking_url',
        'label_url',
        'barcode_url',
        'cargo_reference_id',
        'current_location',
        'current_latitude',
        'current_longitude',
        'progress_percent',
        'notify_on_status_change',
        'estimated_delivery',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'last_tracking_update',
        'weight',
        'desi',
        'piece_count',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'estimated_delivery' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'last_tracking_update' => 'datetime',
            'progress_percent' => 'integer',
            'notify_on_status_change' => 'boolean',
            'current_latitude' => 'decimal:8',
            'current_longitude' => 'decimal:8',
            'weight' => 'decimal:2',
            'desi' => 'decimal:2',
            'piece_count' => 'integer',
        ];
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function shippingCompany(): BelongsTo
    {
        return $this->belongsTo(ShippingCompany::class);
    }

    public function cargoIntegration(): BelongsTo
    {
        return $this->belongsTo(CargoIntegration::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ShipmentEvent::class);
    }

    /**
     * Teslim edildi mi?
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    /**
     * Aktif mi? (iptal edilmemiş, teslim edilmemiş)
     */
    public function isActive(): bool
    {
        return ! in_array($this->status, ['delivered', 'cancelled', 'returned', 'failed'], true);
    }

    /**
     * Takip URL'i
     */
    public function getTrackingUrlAttribute(): ?string
    {
        if ($this->attributes['tracking_url'] ?? null) {
            return $this->attributes['tracking_url'];
        }

        if (! $this->tracking_number || ! $this->shippingCompany) {
            return null;
        }

        // Provider enum'undan URL oluştur
        $providerCode = $this->shippingCompany->code ?? null;
        if ($providerCode && enum_exists(\App\CargoProvider::class)) {
            $provider = \App\CargoProvider::tryFrom($providerCode);
            if ($provider) {
                return $provider->trackingUrl($this->tracking_number);
            }
        }

        return null;
    }
}
