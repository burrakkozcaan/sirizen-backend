<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartModalLayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_group_id',
        'name',
        'layout_config',
        'rules',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'layout_config' => 'array',
            'rules' => 'array',
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
     * Varsayılan layout'u getir veya oluştur
     */
    public static function getDefaultForCategoryGroup(int $categoryGroupId): ?self
    {
        return self::where('category_group_id', $categoryGroupId)
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Tüm kategori grupları için varsayılan layout'ları seed et
     */
    public static function seedDefaults(): void
    {
        $defaults = [
            // Giyim
            'giyim' => [
                'name' => 'Giyim Cart Modal',
                'blocks' => [
                    ['block' => 'variant_selector', 'order' => 1, 'props' => ['type' => 'size']],
                    ['block' => 'variant_selector', 'order' => 2, 'props' => ['type' => 'color']],
                    ['block' => 'stock_warning', 'order' => 3],
                    ['block' => 'price', 'order' => 4],
                    ['block' => 'delivery_info', 'order' => 5],
                    ['block' => 'add_to_cart', 'order' => 6],
                ],
                'rules' => [
                    'disable_add_until_variant_selected' => true,
                    'show_stock_warning_threshold' => 5,
                ],
            ],
            // Kozmetik
            'kozmetik' => [
                'name' => 'Kozmetik Cart Modal',
                'blocks' => [
                    ['block' => 'variant_selector', 'order' => 1, 'props' => ['type' => 'volume']],
                    ['block' => 'campaign_info', 'order' => 2],
                    ['block' => 'price', 'order' => 3],
                    ['block' => 'add_to_cart', 'order' => 4],
                ],
                'rules' => [
                    'show_campaign_info' => true,
                ],
            ],
            // Elektronik
            'elektronik' => [
                'name' => 'Elektronik Cart Modal',
                'blocks' => [
                    ['block' => 'seller_selector', 'order' => 1],
                    ['block' => 'variant_selector', 'order' => 2, 'props' => ['type' => 'storage']],
                    ['block' => 'warranty_info', 'order' => 3],
                    ['block' => 'price', 'order' => 4],
                    ['block' => 'add_to_cart', 'order' => 5],
                ],
                'rules' => [
                    'show_multiple_sellers' => true,
                    'show_warranty_info' => true,
                ],
            ],
        ];

        foreach ($defaults as $key => $config) {
            $categoryGroup = CategoryGroup::where('key', $key)->first();
            if (!$categoryGroup) continue;

            self::updateOrCreate(
                [
                    'category_group_id' => $categoryGroup->id,
                    'is_default' => true,
                ],
                [
                    'name' => $config['name'],
                    'layout_config' => ['blocks' => $config['blocks']],
                    'rules' => $config['rules'],
                    'is_active' => true,
                ]
            );
        }
    }
}
