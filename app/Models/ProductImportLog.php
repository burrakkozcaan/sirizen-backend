<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'user_id',
        'file_path',
        'file_name',
        'file_type',
        'total_rows',
        'success_count',
        'failed_count',
        'skipped_count',
        'status',
        'errors',
        'summary',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_rows' => 'integer',
            'success_count' => 'integer',
            'failed_count' => 'integer',
            'skipped_count' => 'integer',
            'errors' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
