<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\FilterConfig;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FilterService
{
    /**
     * Kategori için filtreleri getir (Trendyol-style)
     */
    public function getFiltersForCategory(Category $category): array
    {
        $categoryGroup = $this->getCategoryGroup($category);

        if (!$categoryGroup) {
            return $this->getDefaultFilters($category);
        }

        $cacheKey = "filters:category_group:{$categoryGroup->id}";

        return Cache::remember($cacheKey, 300, function () use ($categoryGroup, $category) {
            $filterConfigs = FilterConfig::with('attribute')
                ->where('category_group_id', $categoryGroup->id)
                ->where('is_active', true)
                ->orderBy('order')
                ->get();

            $filters = [];

            foreach ($filterConfigs as $config) {
                $filter = $this->buildFilter($config, $category);
                if ($filter) {
                    $filters[] = $filter;
                }
            }

            // Eğer hiç config yoksa default filtreler
            if (empty($filters)) {
                return $this->getDefaultFilters($category);
            }

            return $filters;
        });
    }

    /**
     * Tek bir filter config'den filter oluştur
     */
    protected function buildFilter(FilterConfig $config, Category $category): ?array
    {
        return match ($config->filter_type) {
            'attribute' => $this->buildAttributeFilter($config, $category),
            'price' => $this->buildPriceFilter($config, $category),
            'brand' => $this->buildBrandFilter($config, $category),
            'rating' => $this->buildRatingFilter($config),
            'seller' => $this->buildSellerFilter($config, $category),
            'shipping' => $this->buildShippingFilter($config),
            'campaign' => $this->buildCampaignFilter($config),
            default => null,
        };
    }

    /**
     * Attribute filtresi oluştur
     */
    protected function buildAttributeFilter(FilterConfig $config, Category $category): ?array
    {
        if (!$config->attribute) {
            return null;
        }

        $attribute = $config->attribute;
        $categoryIds = $this->getCategoryIds($category);

        // Bu attribute için mevcut değerleri getir (ürün sayısıyla)
        $options = DB::table('product_attribute_values')
            ->join('products', 'product_attribute_values.product_id', '=', 'products.id')
            ->where('product_attribute_values.attribute_id', $attribute->id)
            ->whereIn('products.category_id', $categoryIds)
            ->where('products.is_active', true)
            ->select('product_attribute_values.value')
            ->selectRaw('COUNT(DISTINCT products.id) as count')
            ->groupBy('product_attribute_values.value')
            ->orderByRaw('count DESC')
            ->get();

        if ($options->isEmpty()) {
            return null;
        }

        return [
            'key' => $attribute->key,
            'label' => $config->display_label ?: $attribute->label,
            'type' => $config->filter_component,
            'is_collapsed' => $config->is_collapsed,
            'show_count' => $config->show_count,
            'options' => $options->map(fn($opt) => [
                'value' => $opt->value,
                'label' => $opt->value,
                'count' => $config->show_count ? $opt->count : null,
            ])->toArray(),
        ];
    }

    /**
     * Fiyat filtresi oluştur
     */
    protected function buildPriceFilter(FilterConfig $config, Category $category): array
    {
        $categoryIds = $this->getCategoryIds($category);

        $priceRange = Product::whereIn('category_id', $categoryIds)
            ->where('is_active', true)
            ->selectRaw('MIN(COALESCE(discount_price, price)) as min_price')
            ->selectRaw('MAX(COALESCE(discount_price, price)) as max_price')
            ->first();

        $configData = $config->config ?? [];

        return [
            'key' => 'price',
            'label' => $config->display_label ?: 'Fiyat',
            'type' => 'range',
            'is_collapsed' => $config->is_collapsed,
            'min' => $configData['min'] ?? floor($priceRange->min_price ?? 0),
            'max' => $configData['max'] ?? ceil($priceRange->max_price ?? 10000),
            'step' => $configData['step'] ?? 10,
            'unit' => 'TL',
        ];
    }

    /**
     * Marka filtresi oluştur
     */
    protected function buildBrandFilter(FilterConfig $config, Category $category): ?array
    {
        $categoryIds = $this->getCategoryIds($category);

        $brands = DB::table('products')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->whereIn('products.category_id', $categoryIds)
            ->where('products.is_active', true)
            ->select('brands.id', 'brands.name', 'brands.slug')
            ->selectRaw('COUNT(products.id) as count')
            ->groupBy('brands.id', 'brands.name', 'brands.slug')
            ->orderByRaw('count DESC')
            ->limit(50)
            ->get();

        if ($brands->isEmpty()) {
            return null;
        }

        return [
            'key' => 'brand',
            'label' => $config->display_label ?: 'Marka',
            'type' => $config->filter_component,
            'is_collapsed' => $config->is_collapsed,
            'show_count' => $config->show_count,
            'options' => $brands->map(fn($brand) => [
                'value' => $brand->slug,
                'label' => $brand->name,
                'count' => $config->show_count ? $brand->count : null,
            ])->toArray(),
        ];
    }

    /**
     * Rating filtresi oluştur
     */
    protected function buildRatingFilter(FilterConfig $config): array
    {
        return [
            'key' => 'rating',
            'label' => $config->display_label ?: 'Değerlendirme',
            'type' => 'rating',
            'is_collapsed' => $config->is_collapsed,
            'options' => [
                ['value' => '4', 'label' => '4 yıldız ve üzeri'],
                ['value' => '3', 'label' => '3 yıldız ve üzeri'],
                ['value' => '2', 'label' => '2 yıldız ve üzeri'],
                ['value' => '1', 'label' => '1 yıldız ve üzeri'],
            ],
        ];
    }

    /**
     * Satıcı filtresi oluştur
     */
    protected function buildSellerFilter(FilterConfig $config, Category $category): ?array
    {
        $categoryIds = $this->getCategoryIds($category);

        $sellers = DB::table('products')
            ->join('vendors', 'products.vendor_id', '=', 'vendors.id')
            ->whereIn('products.category_id', $categoryIds)
            ->where('products.is_active', true)
            ->select('vendors.id', 'vendors.name', 'vendors.slug')
            ->selectRaw('COUNT(products.id) as count')
            ->groupBy('vendors.id', 'vendors.name', 'vendors.slug')
            ->orderByRaw('count DESC')
            ->limit(30)
            ->get();

        if ($sellers->isEmpty()) {
            return null;
        }

        return [
            'key' => 'seller',
            'label' => $config->display_label ?: 'Satıcı',
            'type' => $config->filter_component,
            'is_collapsed' => $config->is_collapsed,
            'show_count' => $config->show_count,
            'options' => $sellers->map(fn($seller) => [
                'value' => $seller->slug,
                'label' => $seller->name,
                'count' => $config->show_count ? $seller->count : null,
            ])->toArray(),
        ];
    }

    /**
     * Kargo filtresi oluştur
     */
    protected function buildShippingFilter(FilterConfig $config): array
    {
        return [
            'key' => 'shipping',
            'label' => $config->display_label ?: 'Kargo',
            'type' => 'checkbox',
            'is_collapsed' => $config->is_collapsed,
            'options' => [
                ['value' => 'free', 'label' => 'Ücretsiz Kargo'],
                ['value' => 'fast', 'label' => 'Hızlı Teslimat'],
            ],
        ];
    }

    /**
     * Kampanya filtresi oluştur
     */
    protected function buildCampaignFilter(FilterConfig $config): array
    {
        return [
            'key' => 'campaign',
            'label' => $config->display_label ?: 'Kampanya',
            'type' => 'checkbox',
            'is_collapsed' => $config->is_collapsed,
            'options' => [
                ['value' => 'discount', 'label' => 'İndirimli Ürünler'],
                ['value' => 'flash', 'label' => 'Flaş Ürünler'],
            ],
        ];
    }

    /**
     * Default filtreler (config yoksa)
     */
    protected function getDefaultFilters(Category $category): array
    {
        $categoryIds = $this->getCategoryIds($category);

        return [
            $this->buildDefaultPriceFilter($categoryIds),
            $this->buildDefaultBrandFilter($categoryIds),
            $this->buildDefaultRatingFilter(),
            $this->buildDefaultShippingFilter(),
        ];
    }

    protected function buildDefaultPriceFilter(array $categoryIds): array
    {
        $priceRange = Product::whereIn('category_id', $categoryIds)
            ->where('is_active', true)
            ->selectRaw('MIN(COALESCE(discount_price, price)) as min_price')
            ->selectRaw('MAX(COALESCE(discount_price, price)) as max_price')
            ->first();

        return [
            'key' => 'price',
            'label' => 'Fiyat',
            'type' => 'range',
            'is_collapsed' => false,
            'min' => floor($priceRange->min_price ?? 0),
            'max' => ceil($priceRange->max_price ?? 10000),
            'step' => 10,
            'unit' => 'TL',
        ];
    }

    protected function buildDefaultBrandFilter(array $categoryIds): ?array
    {
        $brands = DB::table('products')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->whereIn('products.category_id', $categoryIds)
            ->where('products.is_active', true)
            ->select('brands.id', 'brands.name', 'brands.slug')
            ->selectRaw('COUNT(products.id) as count')
            ->groupBy('brands.id', 'brands.name', 'brands.slug')
            ->orderByRaw('count DESC')
            ->limit(50)
            ->get();

        if ($brands->isEmpty()) {
            return null;
        }

        return [
            'key' => 'brand',
            'label' => 'Marka',
            'type' => 'checkbox',
            'is_collapsed' => false,
            'show_count' => true,
            'options' => $brands->map(fn($brand) => [
                'value' => $brand->slug,
                'label' => $brand->name,
                'count' => $brand->count,
            ])->toArray(),
        ];
    }

    protected function buildDefaultRatingFilter(): array
    {
        return [
            'key' => 'rating',
            'label' => 'Değerlendirme',
            'type' => 'rating',
            'is_collapsed' => false,
            'options' => [
                ['value' => '4', 'label' => '4 yıldız ve üzeri'],
                ['value' => '3', 'label' => '3 yıldız ve üzeri'],
            ],
        ];
    }

    protected function buildDefaultShippingFilter(): array
    {
        return [
            'key' => 'shipping',
            'label' => 'Kargo',
            'type' => 'checkbox',
            'is_collapsed' => false,
            'options' => [
                ['value' => 'free', 'label' => 'Ücretsiz Kargo'],
                ['value' => 'fast', 'label' => 'Hızlı Teslimat'],
            ],
        ];
    }

    /**
     * Filtreleri ürün sorgusuna uygula
     */
    public function applyFilters(Builder $query, array $filters, Category $category): Builder
    {
        $categoryGroup = $this->getCategoryGroup($category);

        foreach ($filters as $key => $value) {
            if (empty($value)) {
                continue;
            }

            $query = match ($key) {
                'price' => $this->applyPriceFilter($query, $value),
                'brand' => $this->applyBrandFilter($query, $value),
                'rating' => $this->applyRatingFilter($query, $value),
                'seller' => $this->applySellerFilter($query, $value),
                'shipping' => $this->applyShippingFilter($query, $value),
                'campaign' => $this->applyCampaignFilter($query, $value),
                default => $this->applyAttributeFilter($query, $key, $value, $categoryGroup),
            };
        }

        return $query;
    }

    protected function applyPriceFilter(Builder $query, mixed $value): Builder
    {
        if (is_string($value) && str_contains($value, '-')) {
            [$min, $max] = explode('-', $value);
            $query->whereRaw('COALESCE(discount_price, price) >= ?', [(float) $min])
                  ->whereRaw('COALESCE(discount_price, price) <= ?', [(float) $max]);
        } elseif (is_array($value)) {
            if (isset($value['min'])) {
                $query->whereRaw('COALESCE(discount_price, price) >= ?', [(float) $value['min']]);
            }
            if (isset($value['max'])) {
                $query->whereRaw('COALESCE(discount_price, price) <= ?', [(float) $value['max']]);
            }
        }

        return $query;
    }

    protected function applyBrandFilter(Builder $query, mixed $value): Builder
    {
        $brands = is_array($value) ? $value : [$value];

        return $query->whereHas('brand', function ($q) use ($brands) {
            $q->where(function ($subQuery) use ($brands) {
                foreach ($brands as $brand) {
                    $subQuery->orWhere('slug', $brand)
                            ->orWhere('name', $brand)
                            ->orWhere('name', 'LIKE', '%' . $brand . '%');
                }
            });
        });
    }

    protected function applyRatingFilter(Builder $query, mixed $value): Builder
    {
        $rating = is_array($value) ? max($value) : $value;

        return $query->where('rating', '>=', (float) $rating);
    }

    protected function applySellerFilter(Builder $query, mixed $value): Builder
    {
        $sellers = is_array($value) ? $value : [$value];

        return $query->whereHas('vendor', function ($q) use ($sellers) {
            $q->whereIn('slug', $sellers);
        });
    }

    protected function applyShippingFilter(Builder $query, mixed $value): Builder
    {
        $options = is_array($value) ? $value : [$value];

        if (in_array('free', $options)) {
            $query->whereHas('productSellers', function ($q) {
                $q->where('free_shipping', true);
            });
        }

        if (in_array('fast', $options)) {
            $query->where('shipping_time', '<=', 1);
        }

        return $query;
    }

    protected function applyCampaignFilter(Builder $query, mixed $value): Builder
    {
        $options = is_array($value) ? $value : [$value];

        if (in_array('discount', $options)) {
            $query->whereNotNull('discount_price')
                  ->whereColumn('discount_price', '<', 'price');
        }

        if (in_array('flash', $options)) {
            $query->whereHas('campaigns', function ($q) {
                $q->where('type', 'flash')
                  ->where('is_active', true)
                  ->where('start_date', '<=', now())
                  ->where('end_date', '>=', now());
            });
        }

        return $query;
    }

    protected function applyAttributeFilter(Builder $query, string $key, mixed $value, ?CategoryGroup $categoryGroup): Builder
    {
        $values = is_array($value) ? $value : [$value];

        // Attribute key'i bul
        $attribute = Attribute::where('key', $key)
            ->where('is_filterable', true)
            ->where('is_active', true)
            ->first();

        if (!$attribute) {
            return $query;
        }

        return $query->whereHas('attributeValues', function ($q) use ($attribute, $values) {
            $q->where('attribute_id', $attribute->id)
              ->whereIn('value', $values);
        });
    }

    /**
     * Kategori için category group'u bul
     */
    protected function getCategoryGroup(Category $category): ?CategoryGroup
    {
        // Önce kategorinin kendi group'una bak
        if ($category->category_group_id) {
            return $category->categoryGroup;
        }

        // Parent'a bak
        if ($category->parent_id && $category->parent) {
            return $this->getCategoryGroup($category->parent);
        }

        return null;
    }

    /**
     * Kategori ve alt kategorilerin ID'lerini getir
     */
    protected function getCategoryIds(Category $category): array
    {
        $ids = [$category->id];

        if ($category->children->isNotEmpty()) {
            foreach ($category->children as $child) {
                $ids = array_merge($ids, $this->getCategoryIds($child));
            }
        }

        return $ids;
    }

    /**
     * Filter cache'ini temizle
     */
    public function clearCache(?int $categoryGroupId = null): void
    {
        if ($categoryGroupId) {
            Cache::forget("filters:category_group:{$categoryGroupId}");
        } else {
            // Tüm filter cache'lerini temizle
            $groups = CategoryGroup::pluck('id');
            foreach ($groups as $id) {
                Cache::forget("filters:category_group:{$id}");
            }
        }
    }
}
