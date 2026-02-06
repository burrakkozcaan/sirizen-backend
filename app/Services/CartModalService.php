<?php

namespace App\Services;

use App\Models\CartModalLayout;
use App\Models\Product;
use App\Models\ProductSeller;

class CartModalService
{
    protected BadgeService $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /**
     * Cart Modal için tam veriyi hazırla
     */
    public function getModalData(Product $product): array
    {
        $categoryGroup = $product->categoryGroup;
        $layout = $categoryGroup 
            ? CartModalLayout::getDefaultForCategoryGroup($categoryGroup->id)
            : null;

        // Varyantları kategori grubuna göre düzenle
        $variants = $this->formatVariants($product);

        // Satıcı bilgileri (elektronik için)
        $sellers = $this->getSellers($product);

        // Kampanya bilgileri
        $campaigns = $this->getCampaigns($product);

        // Stok uyarısı
        $stockWarning = $this->getStockWarning($product, $layout?->rules);

        return [
            'product' => [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'discount_percentage' => $product->discount_percentage,
                'currency' => $product->currency ?? 'TRY',
                'image' => $product->images->first()?->url,
                'stock' => $product->stock,
            ],
            'layout' => $layout?->getBlocks() ?? $this->getDefaultBlocks(),
            'rules' => $layout?->rules ?? [],
            'variants' => $variants,
            'sellers' => $sellers,
            'campaigns' => $campaigns,
            'stock_warning' => $stockWarning,
            'badges' => $this->badgeService->formatForApi(
                $this->badgeService->getBadges($product)
            ),
        ];
    }

    /**
     * Varyantları formatla
     */
    protected function formatVariants(Product $product): array
    {
        $variants = $product->variants->where('is_active', true);

        if ($variants->isEmpty()) {
            return [
                'has_variants' => false,
                'options' => [],
            ];
        }

        // Attribute bazlı gruplama
        $attributeKeys = [];
        if ($variants->first()?->attribute_values) {
            $attributeKeys = array_keys($variants->first()->attribute_values);
        }

        $options = [];
        foreach ($attributeKeys as $key) {
            $values = $variants->pluck("attribute_values.{$key}")->unique()->filter()->values();
            
            // Her değer için stok durumunu hesapla
            $valuesWithStock = $values->map(function ($value) use ($variants, $key) {
                $matchingVariants = $variants->filter(
                    fn($v) => ($v->attribute_values[$key] ?? null) === $value
                );
                
                return [
                    'value' => $value,
                    'available' => $matchingVariants->sum('stock') > 0,
                    'stock' => $matchingVariants->sum('stock'),
                ];
            });

            $options[] = [
                'key' => $key,
                'label' => $this->translateAttribute($key),
                'values' => $valuesWithStock,
            ];
        }

        // Kombinasyon validasyonu
        $combinations = [];
        foreach ($variants as $variant) {
            $combinations[] = [
                'attributes' => $variant->attribute_values,
                'price' => $variant->discount_price ?? $variant->price,
                'stock' => $variant->stock,
                'available' => $variant->stock > 0,
            ];
        }

        return [
            'has_variants' => true,
            'options' => $options,
            'combinations' => $combinations,
            'total_stock' => $variants->sum('stock'),
        ];
    }

    /**
     * Satıcı bilgilerini getir
     */
    protected function getSellers(Product $product): array
    {
        $sellers = ProductSeller::with('vendor')
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('price')
            ->get();

        return $sellers->map(fn($seller) => [
            'id' => $seller->id,
            'vendor_name' => $seller->vendor?->name,
            'vendor_slug' => $seller->vendor?->slug,
            'price' => $seller->price,
            'discount_price' => $seller->discount_price,
            'stock' => $seller->stock,
            'shipping_time' => $seller->shipping_time,
            'free_shipping' => $seller->free_shipping,
            'rating' => $seller->vendor?->rating,
        ])->toArray();
    }

    /**
     * Kampanya bilgilerini getir
     */
    protected function getCampaigns(Product $product): array
    {
        return $product->campaigns
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->map(fn($campaign) => [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'type' => $campaign->type,
                'discount_rate' => $campaign->pivot->discount_rate ?? null,
                'badge_text' => $campaign->badge_text,
            ])
            ->toArray();
    }

    /**
     * Stok uyarısı
     */
    protected function getStockWarning(Product $product, ?array $rules): ?array
    {
        $threshold = $rules['show_stock_warning_threshold'] ?? 5;
        
        if ($product->stock > $threshold) {
            return null;
        }

        if ($product->stock === 0) {
            return [
                'type' => 'out_of_stock',
                'message' => 'Stokta yok',
                'color' => 'red',
            ];
        }

        if ($product->stock <= $threshold) {
            return [
                'type' => 'low_stock',
                'message' => "Son {$product->stock} adet!",
                'color' => 'orange',
            ];
        }

        return null;
    }

    /**
     * Varsayılan bloklar
     */
    protected function getDefaultBlocks(): array
    {
        return [
            ['block' => 'variant_selector', 'order' => 1],
            ['block' => 'price', 'order' => 2],
            ['block' => 'add_to_cart', 'order' => 3],
        ];
    }

    /**
     * Attribute key'leri çevir
     */
    protected function translateAttribute(string $key): string
    {
        $translations = [
            'size' => 'Beden',
            'color' => 'Renk',
            'volume' => 'Hacim',
            'material' => 'Materyal',
            'storage' => 'Depolama',
            'ram' => 'RAM',
            'processor' => 'İşlemci',
            'screen' => 'Ekran',
        ];

        return $translations[$key] ?? $key;
    }
}
