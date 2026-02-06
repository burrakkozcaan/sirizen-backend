<?php

namespace App\Services;

use App\Models\AttributeHighlight;
use App\Models\PdpBlock;
use App\Models\PdpLayout;
use App\Models\Product;
use App\Models\SocialProofRule;
use Illuminate\Support\Collection;

class PDPService
{
    protected BadgeService $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /**
     * Ürün için tam PDP verisini hazırla
     */
    public function getProductData(Product $product): array
    {
        $categoryGroup = $product->categoryGroup;
        $layout = $categoryGroup?->defaultPdpLayout();

        return [
            'product' => $this->formatProduct($product),
            'layout' => $layout?->getBlocks() ?? $this->getDefaultLayout(),
            'badges' => $this->badgeService->formatForApi(
                $this->badgeService->getBadges($product)
            ),
            'highlights' => $this->getHighlightAttributes($product),
            'social_proof' => $this->getSocialProof($product),
            'filters' => $categoryGroup ? $this->getAvailableFilters($categoryGroup->id) : [],
        ];
    }

    /**
     * Ürünü API formatına dönüştür
     */
    protected function formatProduct(Product $product): array
    {
        return [
            'id' => $product->id,
            'title' => $product->title,
            'slug' => $product->slug,
            'price' => $product->price,
            'discount_price' => $product->discount_price,
            'discount_percentage' => $product->discount_percentage,
            'currency' => $product->currency ?? 'TRY',
            'rating' => $product->rating,
            'reviews_count' => $product->reviews_count,
            'stock' => $product->stock,
            'is_new' => $product->is_new,
            'is_bestseller' => $product->is_bestseller,
            'fast_delivery' => $product->fast_delivery,
            'images' => $product->images->map(fn ($img) => [
                'url' => $img->url,
                'alt' => $img->alt,
            ]),
            'variants' => $this->formatVariants($product),
            'attributes' => $this->formatAttributes($product),
            'description' => $product->description,
            'brand' => $product->brand?->name,
            'category' => [
                'id' => $product->category_id,
                'name' => $product->category?->name,
                'slug' => $product->category?->slug,
            ],
        ];
    }

    /**
     * Varyantları formatla
     */
    protected function formatVariants(Product $product): array
    {
        if ($product->variants->isEmpty()) {
            return [];
        }

        return $product->variants->map(fn ($variant) => [
            'id' => $variant->id,
            'title' => $variant->title,
            'price' => $variant->price,
            'discount_price' => $variant->discount_price,
            'stock' => $variant->stock,
            'attributes' => $variant->attribute_values ?? [],
            'image' => $variant->image?->url,
        ])->toArray();
    }

    /**
     * Özellikleri formatla
     */
    protected function formatAttributes(Product $product): array
    {
        return $product->attributeValues
            ->map(fn ($attrValue) => [
                'key' => $attrValue->attribute?->key,
                'label' => $attrValue->attribute?->label,
                'value' => $attrValue->value,
                'display_value' => $attrValue->getFormattedValue(),
            ])
            ->filter(fn ($attr) => $attr['key'] !== null)
            ->values()
            ->toArray();
    }

    /**
     * Öne çıkan özellikleri getir (sarı kutular)
     */
    public function getHighlightAttributes(Product $product): array
    {
        if (! $product->category_group_id) {
            return [];
        }

        $highlights = AttributeHighlight::with(['attribute', 'attribute.productValues' => function ($query) use ($product) {
            $query->where('product_id', $product->id);
        }])
            ->where('category_group_id', $product->category_group_id)
            ->where('show_in_pdp', true)
            ->orderBy('priority')
            ->get();

        return $highlights->filter(function ($highlight) use ($product) {
            // Üründe bu attribute var mı kontrol et
            $hasValue = $product->attributeValues
                ->where('attribute_id', $highlight->attribute_id)
                ->isNotEmpty();

            return $hasValue;
        })->map(function ($highlight) use ($product) {
            $attrValue = $product->attributeValues
                ->where('attribute_id', $highlight->attribute_id)
                ->first();

            return [
                'key' => $highlight->attribute?->key,
                'label' => $highlight->display_label ?? $highlight->attribute?->label,
                'value' => $attrValue?->value,
                'display_value' => $attrValue?->getFormattedValue(),
                'icon' => $highlight->icon,
                'color' => $highlight->color,
            ];
        })->values()->toArray();
    }

    /**
     * Sosyal kanıt verisini getir
     */
    public function getSocialProof(Product $product): ?array
    {
        if (! $product->category_group_id) {
            return null;
        }

        $rule = SocialProofRule::where('category_group_id', $product->category_group_id)
            ->where('is_active', true)
            ->first();

        if (! $rule) {
            return null;
        }

        $message = $rule->formatMessage($product);

        if (! $message) {
            return null;
        }

        return [
            'type' => $rule->type,
            'message' => $message,
            'position' => $rule->position,
            'color' => $rule->color,
            'icon' => $rule->icon,
            'refresh_interval' => $rule->refresh_interval,
        ];
    }

    /**
     * Kategori grubu için filtreleri getir
     */
    public function getAvailableFilters(int $categoryGroupId): array
    {
        // Filtre konfigürasyonlarını getir
        $configs = \App\Models\FilterConfig::with('attribute')
            ->where('category_group_id', $categoryGroupId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return $configs->map(function ($config) {
            return [
                'key' => $config->getFilterKey(),
                'label' => $config->display_label,
                'type' => $config->filter_component,
                'options' => $config->getOptions(),
                'is_collapsed' => $config->is_collapsed,
                'show_count' => $config->show_count,
                'config' => $config->config,
            ];
        })->toArray();
    }

    /**
     * Varsayılan PDP layout'u
     */
    public function getDefaultLayout(): array
    {
        return [
            ['block' => 'gallery', 'position' => 'main', 'order' => 1],
            ['block' => 'title', 'position' => 'main', 'order' => 2],
            ['block' => 'rating', 'position' => 'main', 'order' => 3],
            ['block' => 'badges', 'position' => 'main', 'order' => 4],
            ['block' => 'social_proof', 'position' => 'main', 'order' => 5],
            ['block' => 'price', 'position' => 'main', 'order' => 6],
            ['block' => 'variant_selector', 'position' => 'main', 'order' => 7],
            ['block' => 'attributes_highlight', 'position' => 'main', 'order' => 8],
            ['block' => 'delivery_info', 'position' => 'main', 'order' => 9],
            ['block' => 'campaigns', 'position' => 'main', 'order' => 10],
            ['block' => 'add_to_cart', 'position' => 'main', 'order' => 11],
            ['block' => 'description', 'position' => 'bottom', 'order' => 12],
            ['block' => 'attributes_detail', 'position' => 'bottom', 'order' => 13],
            ['block' => 'reviews', 'position' => 'bottom', 'order' => 14],
            ['block' => 'related_products', 'position' => 'bottom', 'order' => 15],
        ];
    }

    /**
     * PDP bloklarını sistemde tanımla
     */
    public function seedDefaultBlocks(): void
    {
        $blocks = PdpBlock::getDefaultBlocks();

        foreach ($blocks as $block) {
            PdpBlock::updateOrCreate(
                ['key' => $block['key']],
                $block + ['is_active' => true]
            );
        }
    }
}
