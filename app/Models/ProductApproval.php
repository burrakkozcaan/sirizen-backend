<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'vendor_id',
        'status',
        'rejection_reason',
        'admin_notes',
        'changes_requested',
        'reviewed_by',
        'submitted_at',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'changes_requested' => 'array',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

