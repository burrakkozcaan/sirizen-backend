<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentEvent extends Model
{
    /** @use HasFactory<\Database\Factories\ShipmentEventFactory> */
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'status',
        'location',
        'description',
        'occurred_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
