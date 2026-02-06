<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispute extends Model
{
    /** @use HasFactory<\Database\Factories\DisputeFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'vendor_id',
        'type',
        'subject',
        'description',
        'status',
        'assigned_to',
        'resolution_notes',
        'resolved_by',
        'resolution_type',
        'evidence_files',
        'assigned_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'evidence_files' => 'array',
            'assigned_at' => 'datetime',
            'resolved_at' => 'datetime',
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

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
