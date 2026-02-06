<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $fillable = [
        'position',
        'section_type',
        'config_json',
        'is_active',
        'start_date',
        'end_date',
        'visible_for',
    ];

    protected function casts(): array
    {
        return [
            'config_json' => 'array',
            'visible_for' => 'array',
            'is_active' => 'boolean',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    /**
     * Scope for active sections
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for currently valid sections (date range)
     */
    public function scopeCurrentlyValid($query)
    {
        $now = now();
        return $query->where(function ($q) use ($now) {
            $q->whereNull('start_date')
                ->orWhere('start_date', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('end_date')
                ->orWhere('end_date', '>=', $now);
        });
    }

    /**
     * Scope ordered by position
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    /**
     * Check if section is visible for given user segments
     */
    public function isVisibleFor(array $userSegments = []): bool
    {
        // If no visibility restrictions, show to everyone
        if (empty($this->visible_for)) {
            return true;
        }

        // Check if any user segment matches
        return !empty(array_intersect($this->visible_for, $userSegments));
    }
}
