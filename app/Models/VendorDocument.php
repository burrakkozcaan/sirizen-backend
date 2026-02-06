<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'document_type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'status',
        'rejection_reason',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
