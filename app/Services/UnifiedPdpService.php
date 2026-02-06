<?php

namespace App\Services;

use App\Models\Product;
use App\Models\CategoryGroup;
use Illuminate\Support\Facades\Cache;

/**
 * UNIFIED PDP SERVICE
 *
 * Trendyol-style PDP Engine
 * PDP + Cart = Tek API, Tek Şema, Farklı Context
 *
 * Endpoint: GET /api/pdp/{slug}?context=page|cart|modal|quickview
 *
 * Kural: Frontend ASLA karar vermez. Backend block listesi ne diyorsa o render edilir.
 */
class UnifiedPdpService
{
    protected BadgeService $badgeService;
    protected ?array $config = null;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
        $this->config = config('pdp');
    }

    /**
     * Unified PDP Response oluştur
     *
     * @param Product $product
     * @param string $context page|cart|modal|quickview
     * @param array $options variant_id, seller_id, selected_attributes
     */
    public function build(Product $product, string $context = 'page', array $options = []): array
    {
        $categoryGroup = $this->getCategoryGroup($product);

        return [
            'meta' => $this->buildMeta($product, $context, $categoryGroup),
            'product' => $this->buildProduct($product),
            'pricing' => $this->buildPricing($product, $options),
            'variants' => $this->buildVariants($product, $categoryGroup),
            'vendors' => $this->buildVendors($product, $categoryGroup),
            'badges' => $this->buildBadges($product),
            'campaigns' => $this->buildCampaigns($product),
            'blocks' => $this->buildBlocks($context, $categoryGroup, $product),
            'rules' => $this->buildRules($categoryGroup),
            'social_proof' => $this->buildSocialProof($product, $categoryGroup),
        ];
    }

    /**
     * Meta bilgisi
     */
    protected function buildMeta(Product $product, string $context, string $categoryGroup): array
    {
        $now = now();
        $ttl = $this->config['cache']['pdp_ttl'] ?? 60;

        return [
            'context' => $context,
            'category_group' => $categoryGroup,
            'cached_at' => $now->toIso8601String(),
            'expires_at' => $now->addSeconds($ttl)->toIso8601String(),
        ];
    }

    /**
     * Core product data
     */
    protected function buildProduct(Product $product): array
    {
        return [
            'id' => $product->id,
            'title' => $product->title,
            'slug' => $product->slug,
            'description' => $product->description,
            'brand' => $product->brand ? [
                'id' => $product->brand->id,
                'name' => $product->brand->name,
                'slug' => $product->brand->slug,
                'logo' => $product->brand->logo,
            ] : null,
            'category' => [
                'id' => $product->category_id,
                'name' => $product->category?->name,
                'slug' => $product->category?->slug,
                'group' => $this->getCategoryGroup($product),
            ],
            'images' => $product->images->map(fn($img, $index) => [
                'id' => $img->id,
                'url' => $img->url,
                'alt' => $img->alt ?? $product->title,
                'order' => $index,
            ])->values()->toArray(),
            'attributes' => $this->buildAttributes($product),
        ];
    }

    /**
     * Pricing data (dynamic - varyant seçimine göre değişebilir)
     */
    protected function buildPricing(Product $product, array $options = []): array
    {
        $variant = null;

        // Varyant seçilmişse onun fiyatını kullan
        if (!empty($options['variant_id'])) {
            $variant = $product->variants->find($options['variant_id']);
        }

        $price = $variant?->price ?? $product->price;
        $salePrice = $variant?->discount_price ?? $product->discount_price;
        $stock = $variant?->stock ?? $product->stock ?? 0;

        return [
            'price' => $price,
            'sale_price' => $salePrice,
            'original_price' => $salePrice ? $price : null,
            'discount_percentage' => $this->calculateDiscountPercentage($price, $salePrice),
            'currency' => $product->currency ?? 'TRY',
            'stock' => $stock,
            'is_in_stock' => $stock > 0,
            'stock_status' => $this->getStockStatus($stock),
            'low_stock_threshold' => $this->config['category_rules'][$this->getCategoryGroup($product)]['show_stock_warning_threshold'] ?? 5,
        ];
    }

    /**
     * Variants config (Kategori bazlı filtreleme!)
     */
    protected function buildVariants(Product $product, string $categoryGroup): array
    {
        if ($product->variants->isEmpty()) {
            return [
                'enabled' => false,
                'attributes' => [],
                'combinations' => [],
                'selection_required' => false,
            ];
        }

        $allowedAttributes = $this->config['category_attributes'][$categoryGroup]['allowed_variant_attributes']
            ?? $this->config['category_attributes']['default']['allowed_variant_attributes'];

        // Varyantlardan attribute'ları çıkar ve filtrele
        $attributeGroups = [];
        foreach ($product->variants as $variant) {
            // attribute_values JSON veya color/size kolonlarından oku
            $attrs = $variant->attribute_values ?? [];

            // Eğer attribute_values boşsa, color ve size kolonlarını kullan
            if (empty($attrs)) {
                if ($variant->color) {
                    $attrs['renk'] = $variant->color;
                }
                if ($variant->size) {
                    $attrs['beden'] = $variant->size;
                }
                // value kolonunda ton bilgisi olabilir (kozmetik için)
                if ($variant->value && !$variant->color && !$variant->size) {
                    $attrs['ton'] = $variant->value;
                }
            }

            foreach ($attrs as $key => $value) {
                // ❌ Kategori izin vermiyorsa → SKIP
                if (!in_array($key, $allowedAttributes)) {
                    continue;
                }

                if (!isset($attributeGroups[$key])) {
                    $attributeGroups[$key] = [
                        'key' => $key,
                        'label' => $this->getAttributeLabel($key),
                        'type' => $this->getAttributeType($key),
                        'values' => [],
                    ];
                }

                $valueKey = $value;
                if (!isset($attributeGroups[$key]['values'][$valueKey])) {
                    $attributeGroups[$key]['values'][$valueKey] = [
                        'value' => $value,
                        'label' => $value,
                        'available' => $variant->stock > 0,
                        'stock' => $variant->stock,
                        'color_hex' => $key === 'renk' ? $this->getColorHex($value) : null,
                    ];
                }
            }
        }

        // Values array'e çevir
        foreach ($attributeGroups as $key => $group) {
            $attributeGroups[$key]['values'] = array_values($group['values']);
        }

        $rules = $this->config['category_rules'][$categoryGroup] ?? $this->config['category_rules']['default'];

        return [
            'enabled' => true,
            'attributes' => array_values($attributeGroups),
            'combinations' => $product->variants->map(function($v) use ($allowedAttributes) {
                // attribute_values JSON veya color/size kolonlarından oku
                $attrs = $v->attribute_values ?? [];
                if (empty($attrs)) {
                    if ($v->color) {
                        $attrs['renk'] = $v->color;
                    }
                    if ($v->size) {
                        $attrs['beden'] = $v->size;
                    }
                    if ($v->value && !$v->color && !$v->size) {
                        $attrs['ton'] = $v->value;
                    }
                }

                return [
                    'id' => $v->id,
                    'sku' => $v->sku,
                    'price' => $v->price,
                    'sale_price' => $v->discount_price ?? $v->sale_price,
                    'stock' => $v->stock,
                    'is_default' => $v->is_default ?? false,
                    'attributes' => collect($attrs)
                        ->filter(fn($val, $key) => in_array($key, $allowedAttributes))
                        ->toArray(),
                    'image' => $v->image?->url ?? null,
                ];
            })->toArray(),
            'selection_required' => $rules['disable_add_until_variant_selected'] ?? false,
        ];
    }

    /**
     * Vendors (multi-seller için)
     */
    protected function buildVendors(Product $product, string $categoryGroup): array
    {
        $rules = $this->config['category_rules'][$categoryGroup] ?? $this->config['category_rules']['default'];

        // Multi-seller izinli değilse sadece ana vendor
        if (!($rules['allow_multi_seller'] ?? false)) {
            $vendor = $product->vendor;
            if (!$vendor) {
                return [];
            }

            return [[
                'id' => $vendor->id,
                'name' => $vendor->name,
                'slug' => $vendor->slug,
                'logo' => $vendor->logo,
                'rating' => (float) ($vendor->rating ?? 0),
                'review_count' => $vendor->reviews_count ?? 0,
                'is_official' => $vendor->is_official ?? false,
                'shipping' => [
                    'estimated_days' => $vendor->shipping_days ?? 3,
                    'same_day_cutoff' => $vendor->same_day_cutoff,
                    'free_shipping_threshold' => $vendor->free_shipping_threshold,
                ],
                'price' => $product->price,
                'stock' => $product->stock,
            ]];
        }

        // Multi-seller: Tüm satıcıları getir
        $sellers = $product->productSellers ?? collect();

        return $sellers->map(fn($seller) => [
            'id' => $seller->vendor_id,
            'name' => $seller->vendor?->name,
            'slug' => $seller->vendor?->slug,
            'logo' => $seller->vendor?->logo,
            'rating' => (float) ($seller->vendor?->rating ?? 0),
            'review_count' => $seller->vendor?->reviews_count ?? 0,
            'is_official' => $seller->vendor?->is_official ?? false,
            'shipping' => [
                'estimated_days' => $seller->shipping_days ?? 3,
                'same_day_cutoff' => $seller->same_day_cutoff,
                'free_shipping_threshold' => $seller->free_shipping_threshold,
            ],
            'price' => $seller->price,
            'stock' => $seller->stock,
        ])->toArray();
    }

    /**
     * Badges
     */
    protected function buildBadges(Product $product): array
    {
        $badges = $this->badgeService->getBadges($product);

        return $this->badgeService->formatForApi($badges);
    }

    /**
     * Campaigns
     */
    protected function buildCampaigns(Product $product): array
    {
        // TODO: CampaignService entegrasyonu
        $campaigns = $product->campaigns ?? collect();

        return $campaigns->map(fn($c) => [
            'id' => $c->id,
            'title' => $c->title,
            'type' => $c->type ?? 'discount',
            'discount_type' => $c->discount_type,
            'discount_value' => $c->discount_value,
            'badge_text' => $c->badge_text,
            'min_order' => $c->min_order,
            'valid_until' => $c->end_date?->toIso8601String(),
        ])->toArray();
    }

    /**
     * Blocks (Context + Kategori bazlı)
     */
    protected function buildBlocks(string $context, string $categoryGroup, Product $product): array
    {
        $allowedBlocks = $this->config['block_visibility'][$context]
            ?? $this->config['block_visibility']['page'];

        $rules = $this->config['category_rules'][$categoryGroup] ?? $this->config['category_rules']['default'];

        $blocks = [];
        $order = 1;

        foreach ($allowedBlocks as $blockKey) {
            // Kategori kurallarına göre filtrele
            if ($blockKey === 'size_guide' && !($rules['show_size_guide'] ?? false)) {
                continue;
            }
            if ($blockKey === 'installments' && !($rules['show_installments'] ?? true)) {
                continue;
            }
            if ($blockKey === 'quantity_selector' && !($rules['show_quantity_selector'] ?? true)) {
                continue;
            }
            if ($blockKey === 'seller_selector' && !($rules['allow_multi_seller'] ?? false)) {
                continue;
            }

            // Varyant yoksa variant_selector gösterme
            if ($blockKey === 'variant_selector' && $product->variants->isEmpty()) {
                continue;
            }

            $blocks[] = [
                'block' => $blockKey,
                'position' => $this->getBlockPosition($blockKey, $context),
                'order' => $order++,
                'visible' => true,
                'props' => $this->getBlockProps($blockKey, $product, $rules),
            ];
        }

        return $blocks;
    }

    /**
     * Rules (Kategori bazlı davranış kuralları)
     */
    protected function buildRules(string $categoryGroup): array
    {
        $rules = $this->config['category_rules'][$categoryGroup]
            ?? $this->config['category_rules']['default'];

        return [
            'disable_add_until_variant_selected' => $rules['disable_add_until_variant_selected'] ?? false,
            'show_out_of_stock_variants' => true,
            'show_stock_warning_threshold' => $rules['show_stock_warning_threshold'] ?? 5,
            'allow_multi_seller' => $rules['allow_multi_seller'] ?? false,
            'show_all_sellers' => $rules['allow_multi_seller'] ?? false,
            'show_quantity_selector' => $rules['show_quantity_selector'] ?? true,
            'max_quantity' => $rules['max_quantity'] ?? 10,
            'show_size_guide' => $rules['show_size_guide'] ?? false,
            'show_installments' => $rules['show_installments'] ?? true,
            'show_social_proof' => true,
        ];
    }

    /**
     * Social Proof (Kategori bazlı)
     */
    protected function buildSocialProof(Product $product, string $categoryGroup): ?array
    {
        $config = $this->config['social_proof'][$categoryGroup]
            ?? $this->config['social_proof']['default'];

        $data = [
            'review_count' => $product->reviews_count ?? 0,
            'average_rating' => (float) ($product->rating ?? 0),
            'messages' => [],
        ];

        // Config'e göre ekle
        if ($config['show_view_count'] ?? false) {
            $data['view_count'] = $product->view_count ?? rand(10, 100); // Fake for now
        }

        if ($config['show_cart_count'] ?? false) {
            $data['cart_count'] = rand(1, 15); // TODO: Real cart count from Redis
        }

        if ($config['show_sold_count'] ?? false) {
            $data['sold_count'] = $product->sold_count ?? rand(50, 500);
        }

        // Dinamik mesajlar oluştur
        $data['messages'] = $this->buildSocialProofMessages($product, $data, $categoryGroup);

        return $data;
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    protected function getCategoryGroup(Product $product): string
    {
        $group = $product->categoryGroup?->key ?? $product->category?->categoryGroup?->key;

        return $group ?? 'default';
    }

    protected function calculateDiscountPercentage(?float $price, ?float $salePrice): ?int
    {
        if (!$price || !$salePrice || $salePrice >= $price) {
            return null;
        }

        return (int) round((($price - $salePrice) / $price) * 100);
    }

    protected function getStockStatus(int $stock): string
    {
        if ($stock <= 0) {
            return 'out_of_stock';
        }
        if ($stock <= 5) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    protected function getAttributeLabel(string $key): string
    {
        $labels = [
            'beden' => 'Beden',
            'renk' => 'Renk',
            'boy' => 'Boy',
            'kapasite' => 'Kapasite',
            'ram' => 'RAM',
            'depolama' => 'Depolama',
            'hacim' => 'Hacim',
            'ton' => 'Ton',
            'boyut' => 'Boyut',
            'malzeme' => 'Malzeme',
            'agirlik' => 'Ağırlık',
            'adet' => 'Adet',
        ];

        return $labels[$key] ?? ucfirst($key);
    }

    protected function getAttributeType(string $key): string
    {
        if ($key === 'renk') {
            return 'color';
        }
        if ($key === 'beden') {
            return 'size';
        }

        return 'select';
    }

    protected function getColorHex(string $colorName): ?string
    {
        $colors = [
            'siyah' => '#000000',
            'beyaz' => '#FFFFFF',
            'kırmızı' => '#FF0000',
            'mavi' => '#0000FF',
            'yeşil' => '#008000',
            'sarı' => '#FFFF00',
            'pembe' => '#FFC0CB',
            'mor' => '#800080',
            'turuncu' => '#FFA500',
            'gri' => '#808080',
            'kahverengi' => '#A52A2A',
            'lacivert' => '#000080',
            'bej' => '#F5F5DC',
        ];

        return $colors[strtolower($colorName)] ?? null;
    }

    protected function getBlockPosition(string $block, string $context): string
    {
        if ($context === 'modal') {
            $modalPositions = [
                'gallery' => 'modal_header',
                'title' => 'modal_header',
                'price' => 'modal_body',
                'badges' => 'modal_body',
                'variant_selector' => 'modal_body',
                'seller_selector' => 'modal_body',
                'campaigns' => 'modal_body',
                'quantity_selector' => 'modal_footer',
                'add_to_cart' => 'modal_footer',
            ];

            return $modalPositions[$block] ?? 'modal_body';
        }

        $bottomBlocks = ['description', 'attributes', 'reviews_summary', 'qa_summary', 'related_products', 'similar_products', 'bought_together'];

        if (in_array($block, $bottomBlocks)) {
            return 'bottom';
        }

        $stickyBlocks = ['add_to_cart', 'add_to_favorites'];

        if (in_array($block, $stickyBlocks)) {
            return 'sticky';
        }

        return 'main';
    }

    protected function getBlockProps(string $block, Product $product, array $rules): array
    {
        $props = [];

        if ($block === 'quantity_selector') {
            $props['max'] = $rules['max_quantity'] ?? 10;
        }

        if ($block === 'variant_selector') {
            $props['required'] = $rules['disable_add_until_variant_selected'] ?? false;
        }

        return $props;
    }

    protected function buildAttributes(Product $product): array
    {
        return $product->attributeValues
            ->map(fn($attrValue) => [
                'key' => $attrValue->attribute?->key,
                'label' => $attrValue->attribute?->label,
                'value' => $attrValue->value,
                'display_value' => $attrValue->getFormattedValue(),
            ])
            ->filter(fn($attr) => $attr['key'] !== null)
            ->values()
            ->toArray();
    }

    protected function buildSocialProofMessages(Product $product, array $data, string $categoryGroup): array
    {
        $messages = [];

        // Düşük stok uyarısı
        $stock = $product->stock ?? 0;
        $threshold = $this->config['category_rules'][$categoryGroup]['show_stock_warning_threshold'] ?? 5;

        if ($stock > 0 && $stock <= $threshold) {
            $messages[] = [
                'type' => 'scarcity',
                'text' => "Son {$stock} ürün!",
                'icon' => 'flame',
                'priority' => 1,
            ];
        }

        // Popülerlik
        if (($data['view_count'] ?? 0) > 50) {
            $messages[] = [
                'type' => 'popularity',
                'text' => "{$data['view_count']} kişi şu an bakıyor",
                'icon' => 'eye',
                'priority' => 2,
            ];
        }

        // Güven
        if (($data['sold_count'] ?? 0) > 100) {
            $count = number_format($data['sold_count']);
            $messages[] = [
                'type' => 'trust',
                'text' => "{$count}+ satış",
                'icon' => 'check',
                'priority' => 3,
            ];
        }

        return $messages;
    }

    /**
     * Attribute validation helper
     * Vendor'dan gelen attribute'ı validate et
     */
    public function isAttributeAllowed(string $categoryGroup, string $attributeKey, string $type = 'variant'): bool
    {
        $schema = $this->config['category_attributes'][$categoryGroup]
            ?? $this->config['category_attributes']['default'];

        $allowedKey = $type === 'variant' ? 'allowed_variant_attributes' : 'allowed_highlight_attributes';

        return in_array($attributeKey, $schema[$allowedKey] ?? []);
    }

    /**
     * Filter disallowed attributes from vendor input
     */
    public function filterVendorAttributes(string $categoryGroup, array $attributes, string $type = 'variant'): array
    {
        return array_filter(
            $attributes,
            fn($value, $key) => $this->isAttributeAllowed($categoryGroup, $key, $type),
            ARRAY_FILTER_USE_BOTH
        );
    }
}
