<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdpLayout extends Model
{
    /** @use HasFactory<\Database\Factories\PdpLayoutFactory> */
    use HasFactory;

    protected $fillable = [
        'category_group_id',
        'name',
        'layout_config',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'layout_config' => 'array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function categoryGroup(): BelongsTo
    {
        return $this->belongsTo(CategoryGroup::class);
    }

    /**
     * Layout config'ten blok listesini getir
     */
    public function getBlocks(): array
    {
        return $this->layout_config['blocks'] ?? [];
    }

    /**
     * Belirli bir pozisyondaki blokları getir
     */
    public function getBlocksByPosition(string $position): array
    {
        return collect($this->getBlocks())
            ->filter(fn ($block) => ($block['position'] ?? 'main') === $position)
            ->sortBy('order')
            ->values()
            ->toArray();
    }
}
