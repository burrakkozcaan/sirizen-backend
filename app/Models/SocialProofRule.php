<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialProofRule extends Model
{
    /** @use HasFactory<\Database\Factories\SocialProofRuleFactory> */
    use HasFactory;

    protected $fillable = [
        'category_group_id',
        'type',
        'display_format',
        'threshold_type',
        'threshold_value',
        'refresh_interval',
        'position',
        'color',
        'icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'threshold_value' => 'integer',
            'refresh_interval' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function categoryGroup(): BelongsTo
    {
        return $this->belongsTo(CategoryGroup::class);
    }

    /**
     * Ürün için sosyal kanıt mesajını formatla
     */
    public function formatMessage(Product $product): ?string
    {
        $count = match ($this->type) {
            'cart_count' => $product->cart_count ?? rand(100, 5000), // Gerçek veri veya simülasyon
            'view_count' => $product->view_count ?? rand(1000, 50000),
            'sold_count' => $product->sold_count ?? rand(50, 1000),
            'review_count' => $product->reviews_count,
            default => 0,
        };

        // Eşik kontrolü
        if ($this->threshold_type === 'fixed' && $count < $this->threshold_value) {
            return null;
        }

        // Formatla (Türkçe sayı formatı)
        $formattedCount = $this->formatNumber($count);

        return str_replace('{count}', $formattedCount, $this->display_format);
    }

    /**
     * Sayıyı formatla (1000 -> 1B, 1000000 -> 1M)
     */
    private function formatNumber(int $number): string
    {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        }
        if ($number >= 1000) {
            return round($number / 1000, 1) . 'B';
        }

        return (string) $number;
    }
}
