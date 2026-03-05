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
            // ─── KADIN GİYİM ───────────────────────────────────────────────
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
                'is_bestseller' => true,
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
                'brand_slug' => 'zara',
                'vendor_slug' => 'modastore',
                'category_slug' => 'kadin-giyim',
                'title' => 'Kadın Saten Midi Elbise',
                'slug' => 'kadin-saten-midi-elbise',
                'description' => 'Şık tasarımlı saten kumaş kadın midi elbise. Özel davetler ve günlük kombinler için ideal.',
                'rating' => 4.6,
                'reviews_count' => 872,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 549.99,
                'discount_price' => 449.99,
                'original_price' => 699.99,
                'stock' => 60,
                'currency' => 'TRY',
                'shipping_time' => 2,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1566479179817-c0a7d0e01e97?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'ZARA-ELB-001-XS', 'size' => 'XS', 'color' => null, 'stock' => 12, 'price' => 449.99, 'weight' => 0.30],
                    ['sku' => 'ZARA-ELB-001-S', 'size' => 'S', 'color' => null, 'stock' => 20, 'price' => 449.99, 'weight' => 0.32],
                    ['sku' => 'ZARA-ELB-001-M', 'size' => 'M', 'color' => null, 'stock' => 18, 'price' => 449.99, 'weight' => 0.34],
                    ['sku' => 'ZARA-ELB-001-L', 'size' => 'L', 'color' => null, 'stock' => 10, 'price' => 449.99, 'weight' => 0.36],
                ],
            ],

            // ─── ERKEK GİYİM ───────────────────────────────────────────────
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
                'is_bestseller' => true,
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
                'brand_slug' => 'defacto',
                'vendor_slug' => 'modastore',
                'category_slug' => 'erkek-giyim',
                'title' => 'Erkek Regular Fit Oxford Gömlek',
                'slug' => 'erkek-regular-fit-oxford-gomlek',
                'description' => 'Pamuk karışımlı Oxford kumaştan üretilmiş regular fit erkek gömlek. İş ve günlük yaşam için uygun.',
                'rating' => 4.4,
                'reviews_count' => 540,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 299.99,
                'discount_price' => 249.99,
                'original_price' => 399.99,
                'stock' => 80,
                'currency' => 'TRY',
                'shipping_time' => 2,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'DFT-GML-001-S', 'size' => 'S', 'color' => null, 'stock' => 20, 'price' => 249.99, 'weight' => 0.25],
                    ['sku' => 'DFT-GML-001-M', 'size' => 'M', 'color' => null, 'stock' => 30, 'price' => 249.99, 'weight' => 0.27],
                    ['sku' => 'DFT-GML-001-L', 'size' => 'L', 'color' => null, 'stock' => 20, 'price' => 249.99, 'weight' => 0.29],
                    ['sku' => 'DFT-GML-001-XL', 'size' => 'XL', 'color' => null, 'stock' => 10, 'price' => 249.99, 'weight' => 0.31],
                ],
            ],

            // ─── SPOR AYAKKABI ─────────────────────────────────────────────
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
                'is_bestseller' => true,
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
                'brand_slug' => 'adidas',
                'vendor_slug' => 'sportmax',
                'category_slug' => 'spor-ayakkabi',
                'title' => 'Adidas Ultraboost 22 Koşu Ayakkabısı',
                'slug' => 'adidas-ultraboost-22-kosu-ayakkabisi',
                'description' => 'Adidas Ultraboost 22, BOOST teknolojisi ile olağanüstü enerji geri dönüşü sağlar. Uzun koşular için ideal.',
                'rating' => 4.8,
                'reviews_count' => 1680,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 3299.99,
                'discount_price' => 2799.99,
                'original_price' => 3999.99,
                'stock' => 45,
                'currency' => 'TRY',
                'shipping_time' => 1,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'ADS-UB22-001-40', 'size' => '40', 'color' => null, 'stock' => 8, 'price' => 2799.99, 'weight' => 0.65],
                    ['sku' => 'ADS-UB22-001-41', 'size' => '41', 'color' => null, 'stock' => 12, 'price' => 2799.99, 'weight' => 0.67],
                    ['sku' => 'ADS-UB22-001-42', 'size' => '42', 'color' => null, 'stock' => 15, 'price' => 2799.99, 'weight' => 0.69],
                    ['sku' => 'ADS-UB22-001-43', 'size' => '43', 'color' => null, 'stock' => 10, 'price' => 2799.99, 'weight' => 0.71],
                ],
            ],

            // ─── KADIN ÇANTA ───────────────────────────────────────────────
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
                'is_bestseller' => true,
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
                    ['sku' => 'ZARA-BAG-001', 'size' => null, 'color' => 'Siyah', 'stock' => 20, 'price' => 899.99, 'weight' => 0.55],
                    ['sku' => 'ZARA-BAG-001-BRW', 'size' => null, 'color' => 'Kahverengi', 'stock' => 15, 'price' => 899.99, 'weight' => 0.55],
                ],
            ],
            [
                ...$defaultSafetyInfo,
                'brand_slug' => 'mango',
                'vendor_slug' => 'modastore',
                'category_slug' => 'kadin-canta',
                'title' => 'Kadın Tote Bag Büyük Çanta',
                'slug' => 'kadin-tote-bag-buyuk-canta',
                'description' => 'Ferah ve şık Tote bag. A4 evrak, laptop ve günlük ihtiyaçlarınız için geniş alan.',
                'rating' => 4.3,
                'reviews_count' => 320,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 649.99,
                'discount_price' => 549.99,
                'original_price' => 799.99,
                'stock' => 40,
                'currency' => 'TRY',
                'shipping_time' => 2,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1591561954557-26941169b49e?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'MNG-TOTE-001-BEJ', 'size' => null, 'color' => 'Bej', 'stock' => 20, 'price' => 549.99, 'weight' => 0.60],
                    ['sku' => 'MNG-TOTE-001-BLK', 'size' => null, 'color' => 'Siyah', 'stock' => 20, 'price' => 549.99, 'weight' => 0.60],
                ],
            ],

            // ─── MAKYAJ ────────────────────────────────────────────────────
            [
                ...$defaultSafetyInfo,
                'short_description' => 'Uzun süre kalıcı, canlı renkler sunar.',
                'brand_slug' => 'flormar',
                'vendor_slug' => 'beautybox',
                'category_slug' => 'makyaj',
                'title' => 'Flormar Ruj Mat Kadife Serisi',
                'slug' => 'flormar-ruj-mat-kadife',
                'description' => 'Flormar Mat Kadife Ruj, 12 saat kalıcılık ve mat bitiş sunar. Nemlendirici formulü ile dudakları besler.',
                'rating' => 4.6,
                'reviews_count' => 1890,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 89.99,
                'discount_price' => 69.99,
                'original_price' => 119.99,
                'stock' => 200,
                'currency' => 'TRY',
                'shipping_time' => 2,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1586495777744-4e6232bf2a40?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'FLR-RUJ-001-RED', 'size' => null, 'color' => 'Kırmızı', 'stock' => 60, 'price' => 69.99, 'weight' => 0.05],
                    ['sku' => 'FLR-RUJ-001-PNK', 'size' => null, 'color' => 'Pembe', 'stock' => 70, 'price' => 69.99, 'weight' => 0.05],
                    ['sku' => 'FLR-RUJ-001-BRG', 'size' => null, 'color' => 'Bordo', 'stock' => 70, 'price' => 69.99, 'weight' => 0.05],
                ],
            ],
            [
                ...$defaultSafetyInfo,
                'short_description' => 'Pürüzsüz uygulama, uzun kalıcılık.',
                'brand_slug' => 'golden-rose',
                'vendor_slug' => 'beautybox',
                'category_slug' => 'makyaj',
                'title' => 'Golden Rose Fondöten Mat Kaplama',
                'slug' => 'golden-rose-fondoten-mat-kaplama',
                'description' => 'Tam örtücü mat fondöten. Gözenekleri kapatır, 24 saat kalıcı mat görünüm sunar. SPF 15 içerir.',
                'rating' => 4.5,
                'reviews_count' => 1240,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 129.99,
                'discount_price' => 109.99,
                'original_price' => 159.99,
                'stock' => 150,
                'currency' => 'TRY',
                'shipping_time' => 2,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1522338242992-e1a54906a8da?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1619451334792-150fd785ee74?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'GR-FND-001-N01', 'size' => null, 'color' => 'Natural Ivory', 'stock' => 40, 'price' => 109.99, 'weight' => 0.08],
                    ['sku' => 'GR-FND-001-N02', 'size' => null, 'color' => 'Sand Beige', 'stock' => 60, 'price' => 109.99, 'weight' => 0.08],
                    ['sku' => 'GR-FND-001-N03', 'size' => null, 'color' => 'Warm Honey', 'stock' => 50, 'price' => 109.99, 'weight' => 0.08],
                ],
            ],

            // ─── CİLT BAKIMI ───────────────────────────────────────────────
            [
                ...$defaultSafetyInfo,
                'short_description' => 'Derin nemlendirme ve aydınlatma etkisi.',
                'brand_slug' => 'nivea',
                'vendor_slug' => 'beautybox',
                'category_slug' => 'cilt-bakimi',
                'title' => 'Nivea Q10 Gece Kremi Yoğun Nemlendirici',
                'slug' => 'nivea-q10-gece-kremi',
                'description' => 'Nivea Q10 Gece Kremi, uyku sırasında cildi derinlemesine nemlendirir ve yeniler. Kırışıklık karşıtı formulü ile cilt elastikiyetini artırır.',
                'rating' => 4.7,
                'reviews_count' => 3200,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 189.99,
                'discount_price' => 159.99,
                'original_price' => 229.99,
                'stock' => 120,
                'currency' => 'TRY',
                'shipping_time' => 1,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'NVA-Q10-GC-50ML', 'size' => '50ml', 'color' => null, 'stock' => 120, 'price' => 159.99, 'weight' => 0.12],
                ],
            ],

            // ─── PARFÜM ────────────────────────────────────────────────────
            [
                ...$defaultSafetyInfo,
                'short_description' => 'Uzun süre kalıcı, çekici koku.',
                'brand_slug' => 'mac',
                'vendor_slug' => 'beautybox',
                'category_slug' => 'parfum',
                'title' => 'Flormar Black Panter EDP 100ml',
                'slug' => 'flormar-black-panter-edp-100ml',
                'description' => 'Sert ve odunsu nota ile güçlü iz bırakan erkek parfümü. Kalıcılığı ile gün boyu taze hissettirır.',
                'rating' => 4.4,
                'reviews_count' => 780,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 349.99,
                'discount_price' => 299.99,
                'original_price' => 449.99,
                'stock' => 80,
                'currency' => 'TRY',
                'shipping_time' => 2,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1541643600914-78b084683702?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1592945403407-9caf930b54d1?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'FLR-PFM-001-100ML', 'size' => '100ml', 'color' => null, 'stock' => 80, 'price' => 299.99, 'weight' => 0.35],
                ],
            ],

            // ─── TELEFON ───────────────────────────────────────────────────
            [
                ...$defaultSafetyInfo,
                'short_description' => 'Güçlü işlemci, uzun pil ömrü.',
                'brand_slug' => 'samsung',
                'vendor_slug' => 'techzone',
                'category_slug' => 'telefon',
                'title' => 'Samsung Galaxy A54 5G 128GB Akıllı Telefon',
                'slug' => 'samsung-galaxy-a54-5g-128gb',
                'description' => 'Samsung Galaxy A54 5G, 6.4" Super AMOLED ekran, 50MP üçlü kamera sistemi ve 5000mAh pil ile üstün deneyim sunar.',
                'rating' => 4.6,
                'reviews_count' => 2890,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 12999.99,
                'discount_price' => 11499.99,
                'original_price' => 14999.99,
                'stock' => 50,
                'currency' => 'TRY',
                'shipping_time' => 1,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1610945264803-c22b62d2a7b3?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'SSG-A54-128-BLK', 'size' => null, 'color' => 'Siyah', 'stock' => 20, 'price' => 11499.99, 'weight' => 0.20],
                    ['sku' => 'SSG-A54-128-WHT', 'size' => null, 'color' => 'Beyaz', 'stock' => 15, 'price' => 11499.99, 'weight' => 0.20],
                    ['sku' => 'SSG-A54-128-VIO', 'size' => null, 'color' => 'Mor', 'stock' => 15, 'price' => 11499.99, 'weight' => 0.20],
                ],
            ],
            [
                ...$defaultSafetyInfo,
                'short_description' => 'Uygun fiyat, yüksek performans.',
                'brand_slug' => 'xiaomi',
                'vendor_slug' => 'techzone',
                'category_slug' => 'telefon',
                'title' => 'Xiaomi Redmi Note 12 Pro 256GB',
                'slug' => 'xiaomi-redmi-note-12-pro-256gb',
                'description' => 'Xiaomi Redmi Note 12 Pro, 200MP kamera, 120W hızlı şarj ve 6.67" AMOLED ekranı ile bütçe dostu premium telefon.',
                'rating' => 4.5,
                'reviews_count' => 1950,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 9499.99,
                'discount_price' => 8499.99,
                'original_price' => 10999.99,
                'stock' => 60,
                'currency' => 'TRY',
                'shipping_time' => 1,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1565849904461-04a58ad377e0?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'XMI-RN12P-256-BLK', 'size' => null, 'color' => 'Siyah', 'stock' => 25, 'price' => 8499.99, 'weight' => 0.19],
                    ['sku' => 'XMI-RN12P-256-BLU', 'size' => null, 'color' => 'Mavi', 'stock' => 20, 'price' => 8499.99, 'weight' => 0.19],
                    ['sku' => 'XMI-RN12P-256-GRY', 'size' => null, 'color' => 'Gri', 'stock' => 15, 'price' => 8499.99, 'weight' => 0.19],
                ],
            ],

            // ─── BİLGİSAYAR ────────────────────────────────────────────────
            [
                ...$defaultSafetyInfo,
                'short_description' => 'İnce tasarım, güçlü performans.',
                'brand_slug' => 'apple',
                'vendor_slug' => 'techzone',
                'category_slug' => 'bilgisayar',
                'title' => 'Apple MacBook Air M2 13" 256GB Dizüstü Bilgisayar',
                'slug' => 'apple-macbook-air-m2-13-256gb',
                'description' => 'Apple M2 çip, 8GB bellek ve 256GB SSD ile olağanüstü performans. 18 saate kadar pil ömrü ve Liquid Retina ekran.',
                'rating' => 4.9,
                'reviews_count' => 4120,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 45999.99,
                'discount_price' => 42999.99,
                'original_price' => 49999.99,
                'stock' => 25,
                'currency' => 'TRY',
                'shipping_time' => 2,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1611186871525-5b78b38e5b0d?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'APL-MBA-M2-SLVR', 'size' => null, 'color' => 'Uzay Grisi', 'stock' => 10, 'price' => 42999.99, 'weight' => 1.24],
                    ['sku' => 'APL-MBA-M2-GLD', 'size' => null, 'color' => 'Yıldız Işığı', 'stock' => 15, 'price' => 42999.99, 'weight' => 1.24],
                ],
            ],

            // ─── TV & SES ───────────────────────────────────────────────────
            [
                ...$defaultSafetyInfo,
                'short_description' => 'Kristal netliğinde görüntü, derin ses.',
                'brand_slug' => 'sony',
                'vendor_slug' => 'techzone',
                'category_slug' => 'tv-ses',
                'title' => 'Sony 55" 4K OLED Akıllı TV',
                'slug' => 'sony-55-4k-oled-akilli-tv',
                'description' => 'Sony BRAVIA XR OLED, XR Cognitive Processor ve Acoustic Surface Audio+ teknolojisi ile sinema kalitesinde görüntü ve ses.',
                'rating' => 4.8,
                'reviews_count' => 890,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 54999.99,
                'discount_price' => 49999.99,
                'original_price' => 64999.99,
                'stock' => 15,
                'currency' => 'TRY',
                'shipping_time' => 3,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1571415060716-baff5f717a19?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'SNY-TV-OLED55', 'size' => '55"', 'color' => null, 'stock' => 15, 'price' => 49999.99, 'weight' => 18.5],
                ],
            ],

            // ─── BEBEK GİYİM ────────────────────────────────────────────────
            [
                ...$defaultSafetyInfo,
                'short_description' => '%100 pamuklu, bebek cildine uygun.',
                'brand_slug' => 'lc-waikiki',
                'vendor_slug' => 'lc-waikiki',
                'category_slug' => 'bebek-giyim',
                'title' => 'Bebek Pamuklu Tulum Seti 3\'lü',
                'slug' => 'bebek-pamuklu-tulum-seti-3lu',
                'description' => '%100 organik pamuktan üretilen bebek tulum seti. 3 farklı desenle 3 adet tulum içerir. 0-3 ay ile 3-6 ay bedenlerde.',
                'rating' => 4.8,
                'reviews_count' => 1560,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 299.99,
                'discount_price' => 249.99,
                'original_price' => 399.99,
                'stock' => 100,
                'currency' => 'TRY',
                'shipping_time' => 2,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1619037961390-f2047d89bc55?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'LCW-BBK-TLM-0-3', 'size' => '0-3 Ay', 'color' => null, 'stock' => 50, 'price' => 249.99, 'weight' => 0.25],
                    ['sku' => 'LCW-BBK-TLM-3-6', 'size' => '3-6 Ay', 'color' => null, 'stock' => 50, 'price' => 249.99, 'weight' => 0.28],
                ],
            ],

            // ─── OYUNCAK ────────────────────────────────────────────────────
            [
                ...$defaultSafetyInfo,
                'short_description' => 'Eğlenceli ve eğitici oyun seti.',
                'brand_slug' => 'lc-waikiki',
                'vendor_slug' => 'homestyle',
                'category_slug' => 'oyuncak',
                'title' => 'Lego City Polis Merkezi Seti 743 Parça',
                'slug' => 'lego-city-polis-merkezi-743-parca',
                'description' => 'Lego City Polis Merkezi 743 parçalık yapı seti. 6 minifigür ve aksesuar içerir. 6+ yaş için uygundur.',
                'rating' => 4.9,
                'reviews_count' => 2340,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 799.99,
                'discount_price' => 649.99,
                'original_price' => 999.99,
                'stock' => 45,
                'currency' => 'TRY',
                'shipping_time' => 2,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'LGO-CTY-POL-743', 'size' => null, 'color' => null, 'stock' => 45, 'price' => 649.99, 'weight' => 1.20],
                ],
            ],

            // ─── MUTFAK ─────────────────────────────────────────────────────
            [
                ...$defaultSafetyInfo,
                'short_description' => 'Sağlıklı pişirme, kolay temizlik.',
                'brand_slug' => 'karaca',
                'vendor_slug' => 'homestyle',
                'category_slug' => 'mutfak',
                'title' => 'Karaca Biogranit Tava Seti 3\'lü',
                'slug' => 'karaca-biogranit-tava-seti-3lu',
                'description' => 'Karaca Biogranit kaplı tava seti. Yapışmaz ve sağlıklı Biogranit kaplama ile kolay pişirme. 20cm, 24cm ve 28cm tavalar içerir.',
                'rating' => 4.7,
                'reviews_count' => 3120,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 899.99,
                'discount_price' => 749.99,
                'original_price' => 1199.99,
                'stock' => 65,
                'currency' => 'TRY',
                'shipping_time' => 2,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'KRC-GRAN-3LU-BLK', 'size' => null, 'color' => 'Siyah', 'stock' => 40, 'price' => 749.99, 'weight' => 2.80],
                    ['sku' => 'KRC-GRAN-3LU-RED', 'size' => null, 'color' => 'Kırmızı', 'stock' => 25, 'price' => 749.99, 'weight' => 2.80],
                ],
            ],

            // ─── EV TEKSTİLİ ────────────────────────────────────────────────
            [
                ...$defaultSafetyInfo,
                'short_description' => 'Yumuşak dokulu, uzun ömürlü.',
                'brand_slug' => 'madame-coco',
                'vendor_slug' => 'homestyle',
                'category_slug' => 'ev-tekstili',
                'title' => 'Madame Coco Çift Kişilik Pike Takımı',
                'slug' => 'madame-coco-cift-kisilik-pike-takimi',
                'description' => '%100 saf pamuktan üretilen pike takımı. Yıkamaya dayanıklı, cilde nazik dokusuyla her mevsim kullanıma uygundur.',
                'rating' => 4.6,
                'reviews_count' => 890,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 599.99,
                'discount_price' => 499.99,
                'original_price' => 799.99,
                'stock' => 70,
                'currency' => 'TRY',
                'shipping_time' => 2,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1540518614846-7eded433c457?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'MDC-PIK-DBL-WHT', 'size' => null, 'color' => 'Beyaz', 'stock' => 30, 'price' => 499.99, 'weight' => 1.80],
                    ['sku' => 'MDC-PIK-DBL-GRY', 'size' => null, 'color' => 'Gri', 'stock' => 20, 'price' => 499.99, 'weight' => 1.80],
                    ['sku' => 'MDC-PIK-DBL-BEJ', 'size' => null, 'color' => 'Bej', 'stock' => 20, 'price' => 499.99, 'weight' => 1.80],
                ],
            ],

            // ─── SPOR GİYİM ─────────────────────────────────────────────────
            [
                ...$defaultSafetyInfo,
                'short_description' => 'Nefes alan kumaş, hareketli tasarım.',
                'brand_slug' => 'nike',
                'vendor_slug' => 'sportmax',
                'category_slug' => 'spor-giyim',
                'title' => 'Nike Dri-FIT Erkek Koşu Tişörtü',
                'slug' => 'nike-dri-fit-erkek-kosu-tisortu',
                'description' => 'Nike Dri-FIT teknolojisi ile ter absorpsiyonu sağlar. Hafif ve nefes alan kumaşı ile yoğun antrenmanlar için ideal.',
                'rating' => 4.7,
                'reviews_count' => 1430,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 449.99,
                'discount_price' => 379.99,
                'original_price' => 599.99,
                'stock' => 90,
                'currency' => 'TRY',
                'shipping_time' => 1,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1576766126191-26d41e1b8ec2?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1588701177396-4f2b02e1d534?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'NIKE-DRF-M-S', 'size' => 'S', 'color' => null, 'stock' => 25, 'price' => 379.99, 'weight' => 0.18],
                    ['sku' => 'NIKE-DRF-M-M', 'size' => 'M', 'color' => null, 'stock' => 35, 'price' => 379.99, 'weight' => 0.20],
                    ['sku' => 'NIKE-DRF-M-L', 'size' => 'L', 'color' => null, 'stock' => 20, 'price' => 379.99, 'weight' => 0.22],
                    ['sku' => 'NIKE-DRF-M-XL', 'size' => 'XL', 'color' => null, 'stock' => 10, 'price' => 379.99, 'weight' => 0.24],
                ],
            ],

            // ─── FITNESS ────────────────────────────────────────────────────
            [
                ...$defaultSafetyInfo,
                'short_description' => 'Yüksek yoğunluklu antrenman için.',
                'brand_slug' => 'adidas',
                'vendor_slug' => 'sportmax',
                'category_slug' => 'fitness',
                'title' => 'Yoga Matı 6mm Kaymaz Taban Premium',
                'slug' => 'yoga-mati-6mm-kaymaz-taban-premium',
                'description' => 'TPE malzemeden üretilen 6mm kalınlıklı yoga matı. Kaymaz taban, eklem dostu yapısı ve taşıma askısı ile yoga, pilates ve fitness için ideal.',
                'rating' => 4.5,
                'reviews_count' => 2100,
                'is_active' => true,
                'is_bestseller' => true,
                'price' => 299.99,
                'discount_price' => 249.99,
                'original_price' => 399.99,
                'stock' => 80,
                'currency' => 'TRY',
                'shipping_time' => 2,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1588286840104-8957b019727f?w=600', 'is_main' => true, 'order' => 1],
                    ['url' => 'https://images.unsplash.com/photo-1601925228006-ed8c4c08e2c5?w=600', 'is_main' => false, 'order' => 2],
                ],
                'variants' => [
                    ['sku' => 'YGA-MAT-6MM-PRP', 'size' => null, 'color' => 'Mor', 'stock' => 30, 'price' => 249.99, 'weight' => 1.00],
                    ['sku' => 'YGA-MAT-6MM-BLU', 'size' => null, 'color' => 'Mavi', 'stock' => 30, 'price' => 249.99, 'weight' => 1.00],
                    ['sku' => 'YGA-MAT-6MM-GRN', 'size' => null, 'color' => 'Yeşil', 'stock' => 20, 'price' => 249.99, 'weight' => 1.00],
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
                $this->command->warn("Category not found, skipping: " . ($productData['title'] ?? ''));
                continue;
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
