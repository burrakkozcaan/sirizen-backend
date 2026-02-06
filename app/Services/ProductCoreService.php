<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductCoreService
{
    /**
     * Get product core data (rarely changes - long cache).
     * Cache: CDN 1 day, ISR 12-24 hours
     */
    public function getProductCore(int|string $identifier, bool $bySlug = false): ?array
    {
        $cacheKey = $bySlug 
            ? "product:core:slug:{$identifier}"
            : "product:core:id:{$identifier}";

            return Cache::remember($cacheKey, now()->addDay(), function () use ($identifier, $bySlug) {
            $query = Product::with([
                'brand:id,name,slug,logo',
                'category:id,name,slug,parent_id',
                'category.parent:id,name,slug',
                'images:id,product_id,url,order',
                'attributes:id,product_id,key,value',
                'guarantees:id,product_id,type,description',
                'faqs:id,product_id,question,answer,order',
                'safetyImages:id,product_id,title,image,alt,order',
                'safetyDocuments:id,product_id,title,file,order',
                'productSellers.vendor:id,name,slug',
            ])
                ->where('is_active', true);

            if ($bySlug) {
                $product = $query->where('slug', $identifier)->first();
            } else {
                $product = $query->find($identifier);
            }

            if (!$product) {
                return null;
            }

            return [
                'id' => $product->id,
                'slug' => $product->slug,
                'title' => $product->title,
                'description' => $product->description,
                'short_description' => $product->short_description,
                'additional_information' => $product->additional_information,
                'safety_information' => $product->safety_information,
                'additional_info' => $product->additional_info_array,
                'safety_info' => $product->safety_info,
                'manufacturer' => [
                    'name' => $product->manufacturer_name,
                    'address' => $product->manufacturer_address,
                    'contact' => $product->manufacturer_contact,
                ],
                'responsible_party' => [
                    'name' => $product->responsible_party_name,
                    'address' => $product->responsible_party_address,
                    'contact' => $product->responsible_party_contact,
                ],
                'brand' => [
                    'id' => $product->brand->id,
                    'name' => $product->brand->name,
                    'slug' => $product->brand->slug,
                    'logo' => $product->brand->logo,
                ],
                'category' => $this->buildCategoryTree($product->category),
                'images' => $product->images->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => $img->url,
                    'order' => $img->order,
                ])->sortBy('order')->values(),
                'attributes' => $product->attributes->map(fn ($attr) => [
                    'id' => $attr->id,
                    'key' => $attr->key,
                    'value' => $attr->value,
                ]),
                'safety_images' => $product->safetyImages->map(fn ($image) => [
                    'id' => $image->id,
                    'title' => $image->title,
                    'url' => $image->image,
                    'alt' => $image->alt,
                    'order' => $image->order,
                ])->sortBy('order')->values(),
                'safety_documents' => $product->safetyDocuments->map(fn ($document) => [
                    'id' => $document->id,
                    'title' => $document->title,
                    'url' => $document->file,
                    'order' => $document->order,
                ])->sortBy('order')->values(),
                'guarantees' => $product->guarantees->map(fn ($guarantee) => [
                    'id' => $guarantee->id,
                    'type' => $guarantee->type,
                    'description' => $guarantee->description,
                ]),
                'faqs' => $product->faqs->where('is_active', true)
                    ->sortBy('order')
                    ->map(fn ($faq) => [
                        'id' => $faq->id,
                        'question' => $faq->question,
                        'answer' => $faq->answer,
                    ])
                    ->values(),
                'vendor_id' => $product->vendor_id ?? $product->productSellers->first()?->vendor_id,
                'status' => $product->status ?? 'active',
            ];
        });
    }

    /**
     * Build category breadcrumb tree.
     */
    private function buildCategoryTree($category): array
    {
        $tree = [];
        $current = $category;

        while ($current) {
            array_unshift($tree, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
            ]);
            $current = $current->parent;
        }

        return $tree;
    }

    /**
     * Invalidate product core cache.
     */
    public function invalidate(int|string $identifier, bool $bySlug = false): void
    {
        $keys = [
            $bySlug ? "product:core:slug:{$identifier}" : "product:core:id:{$identifier}",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
