<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'title',
        'subtitle',
        'cta',
        'badge',
        'discount_text',
        'start_date',
        'end_date',
        'layout_type',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * Scope for active collections
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for currently valid collections (date range)
     */
    public function scopeCurrentlyValid($query)
    {
        $now = now()->toDateString();
        return $query->where(function ($q) use ($now) {
            $q->whereNull('start_date')
                ->orWhere('start_date', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('end_date')
                ->orWhere('end_date', '>=', $now);
        });
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CollectionItem::class)->orderBy('order');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'collection_items')
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /**
     * Get formatted date range for display
     */
    public function getDateRangeAttribute(): ?string
    {
        if (!$this->start_date && !$this->end_date) {
            return null;
        }

        $start = $this->start_date?->translatedFormat('d F');
        $end = $this->end_date?->translatedFormat('d F');

        if ($start && $end) {
            return "{$start} - {$end}";
        }

        return $start ?? $end;
    }
}
