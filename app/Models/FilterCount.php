<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilterCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'filter_key',
        'filter_value',
        'count',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'count' => 'integer',
            'calculated_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Pre-aggregated count'ları güncelle (nightly job için)
     */
    public static function recalculateForCategory(Category $category): void
    {
        $counts = [];
        $now = now();

        // Attribute bazlı filtreler için count hesapla
        $attributeFilters = FilterConfig::with('attribute')
            ->where('category_group_id', $category->category_group_id)
            ->where('filter_type', 'attribute')
            ->where('is_active', true)
            ->get();

        foreach ($attributeFilters as $filter) {
            if (!$filter->attribute) continue;

            $results = \DB::table('product_attribute_values')
                ->join('products', 'product_attribute_values.product_id', '=', 'products.id')
                ->where('product_attribute_values.attribute_id', $filter->attribute->id)
                ->where('products.category_id', $category->id)
                ->where('products.is_active', true)
                ->select('product_attribute_values.value')
                ->selectRaw('COUNT(DISTINCT products.id) as count')
                ->groupBy('product_attribute_values.value')
                ->get();

            foreach ($results as $result) {
                $counts[] = [
                    'category_id' => $category->id,
                    'filter_key' => $filter->attribute->key,
                    'filter_value' => $result->value,
                    'count' => $result->count,
                    'calculated_at' => $now,
                    'updated_at' => $now,
                    'created_at' => $now,
                ];
            }
        }

        // Marka bazlı filtre
        $brandCounts = \DB::table('products')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->where('products.category_id', $category->id)
            ->where('products.is_active', true)
            ->select('brands.slug', 'brands.name')
            ->selectRaw('COUNT(products.id) as count')
            ->groupBy('brands.id', 'brands.slug', 'brands.name')
            ->get();

        foreach ($brandCounts as $brand) {
            $counts[] = [
                'category_id' => $category->id,
                'filter_key' => 'brand',
                'filter_value' => $brand->slug,
                'count' => $brand->count,
                'calculated_at' => $now,
                'updated_at' => $now,
                'created_at' => $now,
            ];
        }

        // Batch insert/update
        if (!empty($counts)) {
            self::where('category_id', $category->id)->delete();
            self::insert($counts);
        }
    }

    /**
     * Count'ları cache'le
     */
    public static function getCachedCounts(int $categoryId, string $filterKey): array
    {
        $cacheKey = "filter_counts:{$categoryId}:{$filterKey}";
        
        return \Cache::remember($cacheKey, 3600, function () use ($categoryId, $filterKey) {
            return self::where('category_id', $categoryId)
                ->where('filter_key', $filterKey)
                ->pluck('count', 'filter_value')
                ->toArray();
        });
    }
}
