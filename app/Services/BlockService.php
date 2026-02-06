<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBlock;
use Illuminate\Support\Facades\Cache;

class BlockService
{
    /**
     * Get blocks for a product (with rule engine).
     */
    public function getBlocksForProduct(int $productId): array
    {
        $cacheKey = "product-blocks:{$productId}";
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($productId) {
            $product = Product::with(['category', 'brand', 'variants'])->find($productId);
            
            if (!$product) {
                return [];
            }

            // Get all active blocks
            $blocks = ProductBlock::with(['content', 'rules'])
                ->where('is_active', true)
                ->where(function ($query) use ($productId) {
                    // Product-specific blocks
                    $query->where('product_id', $productId)
                        // OR rule-based blocks
                        ->orWhereNull('product_id');
                })
                ->orderBy('priority', 'desc')
                ->orderBy('id')
                ->get();

            $matchedBlocks = [];

            foreach ($blocks as $block) {
                // If product_id is set, it's product-specific
                if ($block->product_id && $block->product_id === $productId) {
                    $matchedBlocks[] = $this->formatBlock($block);
                    continue;
                }

                // Rule-based: Check if all rules match
                if ($block->rules->isEmpty()) {
                    // No rules = show for all products
                    $matchedBlocks[] = $this->formatBlock($block);
                    continue;
                }

                $allRulesMatch = true;
                foreach ($block->rules as $rule) {
                    if (!$this->evaluateRule($rule, $product)) {
                        $allRulesMatch = false;
                        break;
                    }
                }

                if ($allRulesMatch) {
                    $matchedBlocks[] = $this->formatBlock($block);
                }
            }

            // Group by position
            $grouped = [];
            foreach ($matchedBlocks as $block) {
                $grouped[$block['position']][] = $block;
            }

            return $grouped;
        });
    }

    /**
     * Evaluate a rule against a product.
     */
    private function evaluateRule($rule, Product $product): bool
    {
        $value = json_decode($rule->value, true) ?? $rule->value;

        return match ($rule->rule_type) {
            'category' => $this->evaluateCategoryRule($rule->operator, $value, $product),
            'brand' => $this->evaluateBrandRule($rule->operator, $value, $product),
            'price' => $this->evaluatePriceRule($rule->operator, $value, $product),
            'stock' => $this->evaluateStockRule($rule->operator, $value, $product),
            'seller' => $this->evaluateSellerRule($rule->operator, $value, $product),
            default => false,
        };
    }

    private function evaluateCategoryRule(string $operator, $value, Product $product): bool
    {
        $categorySlug = $product->category->slug ?? '';
        
        return match ($operator) {
            '=' => $categorySlug === $value,
            'in' => in_array($categorySlug, (array) $value),
            'contains' => str_contains($categorySlug, $value),
            default => false,
        };
    }

    private function evaluateBrandRule(string $operator, $value, Product $product): bool
    {
        $brandSlug = $product->brand->slug ?? '';
        
        return match ($operator) {
            '=' => $brandSlug === $value,
            'in' => in_array($brandSlug, (array) $value),
            default => false,
        };
    }

    private function evaluatePriceRule(string $operator, $value, Product $product): bool
    {
        $price = (float) ($product->variants->first()?->price ?? $product->price ?? 0);
        $value = (float) $value;
        
        return match ($operator) {
            '>' => $price > $value,
            '>=' => $price >= $value,
            '<' => $price < $value,
            '<=' => $price <= $value,
            '=' => $price === $value,
            default => false,
        };
    }

    private function evaluateStockRule(string $operator, $value, Product $product): bool
    {
        $stock = (int) ($product->variants->first()?->stock ?? $product->stock ?? 0);
        $value = (int) $value;
        
        return match ($operator) {
            '>' => $stock > $value,
            '>=' => $stock >= $value,
            '<' => $stock < $value,
            '<=' => $stock <= $value,
            '=' => $stock === $value,
            default => false,
        };
    }

    private function evaluateSellerRule(string $operator, $value, Product $product): bool
    {
        // TODO: Implement seller-specific rules (e.g., same_day_shipping)
        return false;
    }

    /**
     * Format block for API response.
     */
    private function formatBlock(ProductBlock $block): array
    {
        $content = $block->content;
        
        return [
            'id' => $block->id,
            'type' => $block->block_type,
            'position' => $block->position,
            'priority' => $block->priority,
            'title' => $content?->title,
            'description' => $content?->description,
            'icon' => $content?->icon,
            'image' => $content?->image,
            'color' => $content?->color ?? 'primary',
            'cta_text' => $content?->cta_text,
            'cta_link' => $content?->cta_link,
        ];
    }
}

