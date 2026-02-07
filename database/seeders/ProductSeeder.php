<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Lookup IDs by slug to avoid hardcoded ID issues across SQLite/PG
        $categoryIds = Category::pluck('id', 'slug');
        $brandIds = Brand::pluck('id', 'slug');
        $vendorIds = Vendor::pluck('id', 'slug');

        $defaultSafetyInfo = [
            'short_description' => 'Kış sezonuna uygun dayanıklı malzemelerle üretilmiştir.',
            'additional_information' => 'Satış fiyatı satıcı tarafından belirlenir. Ürün stok bilgisi satıcıya göre değişebilir.',
            'safety_information' => 'Kullanım talimatları ve uyarılar ürün etiketinde yer almaktadır.',
            'manufacturer_name' => 'Sirizen Üretim',
            'manufacturer_address' => 'Organize Sanayi Bölgesi, İstanbul',
            'manufacturer_contact' => 'info@sirizen.com',
            'responsible_party_name' => 'Sirizen Sorumlu',
            'responsible_party_address' => 'Merkez Mah. 100. Sok. No:1, İstanbul',
            'responsible_party_contact' => '+90 212 000 00 00',
        ];

        $products = [
            [
                ...$defaultSafetyInfo,
                'brand_slug' => 'koton',
                'vendor_slug' => 'koton',
                'category_slug' => 'kadin-giyim',
                'title' => 'Kadın Oversize Basic T-Shirt',
                'slug' => 'kadin-oversize-basic-tshirt',
                'description' => 'Rahat kesim, %100 pamuklu kadın basic t-shirt. Günlük kullanım için ideal.',
                'rating' => 4.5,
                'reviews_count' => 1250,
                'is_active' => true,
                'price' => 149.99,
                'discount_price' => 129.99,
                'original_price' => 249.99,
                'stock' => 93,
                'currency' => 'TRY',
                'shipping_time' => 2,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1503341504253-dff4815485f1?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'KTN-TSHRT-001-S', 'size' => 'S', 'color' => null, 'stock' => 25, 'price' => 129.99, 'weight' => 0.15],
                    ['sku' => 'KTN-TSHRT-001-M', 'size' => 'M', 'color' => null, 'stock' => 42, 'price' => 129.99, 'weight' => 0.16],
                    ['sku' => 'KTN-TSHRT-001-L', 'size' => 'L', 'color' => null, 'stock' => 18, 'price' => 129.99, 'weight' => 0.17],
                ],
            ],
            [
                ...$defaultSafetyInfo,
                'brand_slug' => 'mavi',
                'vendor_slug' => 'koton',
                'category_slug' => 'erkek-giyim',
                'title' => 'Erkek Slim Fit Jean Pantolon',
                'slug' => 'erkek-slim-fit-jean-pantolon',
                'description' => 'Modern slim fit kesim erkek jean pantolon. Esnek kumaş yapısı ile gün boyu konfor.',
                'rating' => 4.7,
                'reviews_count' => 890,
                'is_active' => true,
                'price' => 399.99,
                'discount_price' => 349.99,
                'original_price' => 599.99,
                'stock' => 75,
                'currency' => 'TRY',
                'shipping_time' => 1,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'MAV-JEAN-001-30', 'size' => '30', 'color' => null, 'stock' => 15, 'price' => 349.99, 'weight' => 0.45],
                    ['sku' => 'MAV-JEAN-001-32', 'size' => '32', 'color' => null, 'stock' => 28, 'price' => 349.99, 'weight' => 0.47],
                    ['sku' => 'MAV-JEAN-001-34', 'size' => '34', 'color' => null, 'stock' => 22, 'price' => 349.99, 'weight' => 0.49],
                ],
            ],
            [
                ...$defaultSafetyInfo,
                'brand_slug' => 'nike',
                'vendor_slug' => 'nike',
                'category_slug' => 'spor-ayakkabi',
                'title' => 'Nike Air Max 270 Spor Ayakkabı',
                'slug' => 'nike-air-max-270-spor-ayakkabi',
                'description' => 'Nike Air Max 270, büyük Air ünitesi ile maksimum yastıklama sağlar.',
                'rating' => 4.9,
                'reviews_count' => 2150,
                'is_active' => true,
                'price' => 2799.99,
                'discount_price' => 2499.99,
                'original_price' => 3499.99,
                'stock' => 51,
                'currency' => 'TRY',
                'shipping_time' => 1,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'NIKE-AM270-001-41', 'size' => '41', 'color' => null, 'stock' => 12, 'price' => 2499.99, 'weight' => 0.67],
                    ['sku' => 'NIKE-AM270-001-42', 'size' => '42', 'color' => null, 'stock' => 15, 'price' => 2499.99, 'weight' => 0.69],
                    ['sku' => 'NIKE-AM270-001-43', 'size' => '43', 'color' => null, 'stock' => 10, 'price' => 2499.99, 'weight' => 0.71],
                ],
            ],
            [
                ...$defaultSafetyInfo,
                'brand_slug' => 'zara',
                'vendor_slug' => 'nike',
                'category_slug' => 'kadin-canta',
                'title' => 'Kadın Deri Omuz Çantası',
                'slug' => 'kadin-deri-omuz-cantasi',
                'description' => 'Hakiki deri kadın omuz çantası. Şık tasarımı ve geniş iç hacmi ile günlük kullanıma uygun.',
                'rating' => 4.4,
                'reviews_count' => 456,
                'is_active' => true,
                'price' => 899.99,
                'discount_price' => null,
                'original_price' => 899.99,
                'stock' => 35,
                'currency' => 'TRY',
                'shipping_time' => 2,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'ZARA-BAG-001', 'size' => null, 'color' => 'Black', 'stock' => 35, 'price' => 899.99, 'weight' => 0.55],
                ],
            ],
        ];

        foreach ($products as $productData) {
            $images = $productData['images'] ?? [];
            $variants = $productData['variants'] ?? [];

            // Resolve slugs to IDs
            $productData['brand_id'] = $brandIds[$productData['brand_slug']] ?? null;
            $productData['category_id'] = $categoryIds[$productData['category_slug']] ?? null;
            $productData['vendor_id'] = $vendorIds[$productData['vendor_slug']] ?? null;

            unset($productData['images'], $productData['variants'], $productData['brand_slug'], $productData['category_slug'], $productData['vendor_slug']);

            if (!$productData['category_id']) {
                continue; // Skip if category doesn't exist
            }

            $product = Product::updateOrCreate(
                ['slug' => $productData['slug']],
                $productData
            );

            foreach ($images as $imageData) {
                ProductImage::updateOrCreate(
                    ['product_id' => $product->id, 'url' => $imageData['url']],
                    ['is_main' => $imageData['is_main'], 'order' => $imageData['order']]
                );
            }

            foreach ($variants as $index => $variantData) {
                $variantPrice = $variantData['price'] ?? $product->discount_price ?? $product->price;
                $variantOriginalPrice = $product->original_price ?? $product->price;
                $variantValue = $variantData['size'] ?? $variantData['color'] ?? "Varyant " . ($index + 1);

                ProductVariant::updateOrCreate(
                    ['product_id' => $product->id, 'sku' => $variantData['sku']],
                    [
                        'color' => $variantData['color'],
                        'size' => $variantData['size'],
                        'stock' => $variantData['stock'],
                        'price' => $variantPrice,
                        'sale_price' => $product->discount_price ? $variantPrice : null,
                        'original_price' => $variantOriginalPrice > $variantPrice ? $variantOriginalPrice : null,
                        'weight' => $variantData['weight'] ?? null,
                        'value' => $variantValue,
                        'is_default' => $index === 0,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
