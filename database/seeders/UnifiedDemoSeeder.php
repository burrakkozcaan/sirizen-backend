<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\QuickLink;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * UNIFIED DEMO SEEDER
 *
 * Tüm kategoriler için demo ürünler oluşturur.
 * Her kategori grubuna göre uygun attribute'lar ile.
 *
 * php artisan db:seed --class=UnifiedDemoSeeder
 */
class UnifiedDemoSeeder extends Seeder
{
    protected array $categoryGroupProducts = [];

    public function run(): void
    {
        $this->command->info('🚀 Unified Demo Seeder başlıyor...');

        // 1. Vendor oluştur
        $this->seedVendors();

        // 2. Brand oluştur
        $this->seedBrands();

        // 3. QuickLinks oluştur
        $this->seedQuickLinks();

        // 4. Her kategori grubu için ürünler oluştur
        $this->seedProductsByCategory();

        $this->command->info('✅ Unified Demo Seeder tamamlandı!');
    }

    protected function seedVendors(): void
    {
        $this->command->info('📦 Vendor\'lar oluşturuluyor...');

        $vendors = [
            ['name' => 'Sirizen Official', 'slug' => 'sirizen-official', 'rating' => 9.5, 'description' => 'Sirizen resmi mağazası'],
            ['name' => 'ModaStore', 'slug' => 'modastore', 'rating' => 8.7, 'description' => 'Moda ve giyim mağazası'],
            ['name' => 'TechZone', 'slug' => 'techzone', 'rating' => 9.1, 'description' => 'Teknoloji ürünleri mağazası'],
            ['name' => 'BeautyBox', 'slug' => 'beautybox', 'rating' => 8.9, 'description' => 'Kozmetik ve güzellik mağazası'],
            ['name' => 'HomeStyle', 'slug' => 'homestyle', 'rating' => 8.5, 'description' => 'Ev ve yaşam mağazası'],
            ['name' => 'SportMax', 'slug' => 'sportmax', 'rating' => 8.8, 'description' => 'Spor ve outdoor mağazası'],
        ];

        foreach ($vendors as $vendorData) {
            // Önce user oluştur veya bul
            $user = User::updateOrCreate(
                ['email' => $vendorData['slug'] . '@sirizen.com'],
                [
                    'name' => $vendorData['name'],
                    'password' => Hash::make('password123'),
                ]
            );

            Vendor::updateOrCreate(
                ['slug' => $vendorData['slug']],
                [
                    'user_id' => $user->id,
                    'name' => $vendorData['name'],
                    'slug' => $vendorData['slug'],
                    'description' => $vendorData['description'],
                    'rating' => $vendorData['rating'],
                    'status' => 'active',
                    'total_orders' => rand(100, 5000),
                    'followers' => rand(50, 2000),
                ]
            );
        }
    }

    protected function seedBrands(): void
    {
        $this->command->info('🏷️ Brand\'ler oluşturuluyor...');

        $brands = [
            // Giyim
            ['name' => 'Mavi', 'slug' => 'mavi', 'category_group' => 'giyim'],
            ['name' => 'Koton', 'slug' => 'koton', 'category_group' => 'giyim'],
            ['name' => 'LC Waikiki', 'slug' => 'lc-waikiki', 'category_group' => 'giyim'],
            ['name' => 'DeFacto', 'slug' => 'defacto', 'category_group' => 'giyim'],
            // Elektronik
            ['name' => 'Apple', 'slug' => 'apple', 'category_group' => 'elektronik'],
            ['name' => 'Samsung', 'slug' => 'samsung', 'category_group' => 'elektronik'],
            ['name' => 'Xiaomi', 'slug' => 'xiaomi', 'category_group' => 'elektronik'],
            ['name' => 'Sony', 'slug' => 'sony', 'category_group' => 'elektronik'],
            // Kozmetik
            ['name' => 'Flormar', 'slug' => 'flormar', 'category_group' => 'kozmetik'],
            ['name' => 'Golden Rose', 'slug' => 'golden-rose', 'category_group' => 'kozmetik'],
            ['name' => 'MAC', 'slug' => 'mac', 'category_group' => 'kozmetik'],
            ['name' => 'Nivea', 'slug' => 'nivea', 'category_group' => 'kozmetik'],
            // Ev & Yaşam
            ['name' => 'Ikea', 'slug' => 'ikea', 'category_group' => 'ev-yasam'],
            ['name' => 'Karaca', 'slug' => 'karaca', 'category_group' => 'ev-yasam'],
            ['name' => 'Madame Coco', 'slug' => 'madame-coco', 'category_group' => 'ev-yasam'],
            // Spor
            ['name' => 'Nike', 'slug' => 'nike', 'category_group' => 'spor'],
            ['name' => 'Adidas', 'slug' => 'adidas', 'category_group' => 'spor'],
            ['name' => 'Puma', 'slug' => 'puma', 'category_group' => 'spor'],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['slug' => $brand['slug']],
                [
                    'name' => $brand['name'],
                    'slug' => $brand['slug'],
                ]
            );
        }
    }

    protected function seedQuickLinks(): void
    {
        $this->command->info('🔗 QuickLink\'ler oluşturuluyor...');

        $quickLinks = [
            ['label' => 'Flaş İndirimler', 'key' => 'flas-indirimler', 'icon' => 'zap', 'color' => '#FF6B6B', 'path' => '/campaign/flash-sales', 'order' => 1, 'link_type' => 'campaign'],
            ['label' => 'Çok Satanlar', 'key' => 'cok-satanlar', 'icon' => 'trending-up', 'color' => '#4ECDC4', 'path' => '/category/cok-satanlar', 'order' => 2, 'link_type' => 'category'],
            ['label' => 'Yeni Gelenler', 'key' => 'yeni-gelenler', 'icon' => 'sparkles', 'color' => '#45B7D1', 'path' => '/category/yeni-gelenler', 'order' => 3, 'link_type' => 'category'],
            ['label' => 'Ücretsiz Kargo', 'key' => 'ucretsiz-kargo', 'icon' => 'truck', 'color' => '#96CEB4', 'path' => '/category/ucretsiz-kargo', 'order' => 4, 'link_type' => 'category'],
            ['label' => 'Kuponlarım', 'key' => 'kuponlarim', 'icon' => 'ticket', 'color' => '#FFEAA7', 'path' => '/account/coupons', 'order' => 5, 'link_type' => 'custom'],
            ['label' => 'Favorilerim', 'key' => 'favorilerim', 'icon' => 'heart', 'color' => '#FF9FF3', 'path' => '/favorites', 'order' => 6, 'link_type' => 'custom'],
        ];

        foreach ($quickLinks as $link) {
            QuickLink::updateOrCreate(
                ['key' => $link['key']],
                array_merge($link, ['is_active' => true])
            );
        }
    }

    protected function seedProductsByCategory(): void
    {
        $this->command->info('📦 Ürünler oluşturuluyor...');

        $categoryGroups = CategoryGroup::with('categories')->get();

        foreach ($categoryGroups as $group) {
            $this->command->info("  → {$group->name} kategorisi işleniyor...");

            foreach ($group->categories as $category) {
                $this->createProductsForCategory($category, $group);
            }
        }
    }

    protected function createProductsForCategory(Category $category, CategoryGroup $group): void
    {
        // Her alt kategori için 5-10 ürün oluştur
        $productCount = rand(5, 10);
        $vendor = Vendor::inRandomOrder()->first();
        $brands = Brand::all();

        for ($i = 1; $i <= $productCount; $i++) {
            $productData = $this->generateProductData($category, $group, $i);
            $brand = $brands->random();

            $product = Product::updateOrCreate(
                ['slug' => $productData['slug']],
                array_merge($productData, [
                    'category_id' => $category->id,
                    'category_group_id' => $group->id,
                    'brand_id' => $brand->id,
                    'vendor_id' => $vendor->id,
                    'is_active' => true,
                ])
            );

            // Resimler ekle
            $this->addProductImages($product, $group->key);

            // Varyantlar ekle (kategori grubuna göre)
            $this->addProductVariants($product, $group->key);
        }
    }

    protected function generateProductData(Category $category, CategoryGroup $group, int $index): array
    {
        $templates = $this->getProductTemplates($group->key);
        $template = $templates[array_rand($templates)];

        $title = $template['title'] . ' ' . $index;
        $slug = Str::slug($category->slug . '-' . $title . '-' . uniqid());

        $price = rand($template['price_min'], $template['price_max']);
        $hasDiscount = rand(0, 100) > 60; // %40 şans
        $discountPrice = $hasDiscount ? round($price * (1 - rand(10, 40) / 100), 2) : null;

        return [
            'title' => $title,
            'slug' => $slug,
            'description' => $template['description'],
            'short_description' => substr($template['description'], 0, 100),
            'price' => $price,
            'original_price' => $hasDiscount ? $price : null,
            'discount_price' => $discountPrice,
            'stock' => rand(0, 100),
            'rating' => round(rand(35, 50) / 10, 1),
            'reviews_count' => rand(10, 500),
            'is_new' => rand(0, 100) > 70,
            'is_bestseller' => rand(0, 100) > 80,
            'shipping_time' => rand(1, 5) . ' gün',
            'currency' => 'TRY',
        ];
    }

    protected function getProductTemplates(string $groupKey): array
    {
        $templates = [
            'giyim' => [
                ['title' => 'Slim Fit T-Shirt', 'price_min' => 99, 'price_max' => 299, 'description' => 'Rahat kesim, %100 pamuklu t-shirt. Günlük kullanım için ideal.'],
                ['title' => 'Oversize Sweatshirt', 'price_min' => 199, 'price_max' => 499, 'description' => 'Oversize kesim sweatshirt. Yumuşak kumaş, rahat kalıp.'],
                ['title' => 'Skinny Jean', 'price_min' => 249, 'price_max' => 599, 'description' => 'Dar kesim jean pantolon. Esnek kumaş, yüksek bel.'],
                ['title' => 'Blazer Ceket', 'price_min' => 399, 'price_max' => 899, 'description' => 'Şık blazer ceket. Ofis ve özel günler için ideal.'],
                ['title' => 'Pamuklu Gömlek', 'price_min' => 199, 'price_max' => 449, 'description' => 'Klasik kesim pamuklu gömlek. Nefes alan kumaş.'],
            ],
            'elektronik' => [
                ['title' => 'Kablosuz Kulaklık', 'price_min' => 299, 'price_max' => 1499, 'description' => 'Aktif gürültü engelleme, 30 saat pil ömrü. Premium ses kalitesi.'],
                ['title' => 'Akıllı Saat', 'price_min' => 999, 'price_max' => 3999, 'description' => 'Fitness takibi, kalp ritmi ölçümü, GPS. Su geçirmez tasarım.'],
                ['title' => 'Bluetooth Hoparlör', 'price_min' => 199, 'price_max' => 999, 'description' => 'Taşınabilir hoparlör, 20 saat pil ömrü. Su geçirmez.'],
                ['title' => 'Powerbank 20000mAh', 'price_min' => 199, 'price_max' => 599, 'description' => 'Hızlı şarj destekli, çift USB çıkış. Kompakt tasarım.'],
                ['title' => 'Tablet Kılıfı', 'price_min' => 99, 'price_max' => 299, 'description' => 'Koruyucu tablet kılıfı. Stand özellikli, kalem bölmeli.'],
            ],
            'kozmetik' => [
                ['title' => 'Nemlendirici Krem', 'price_min' => 79, 'price_max' => 349, 'description' => 'Yoğun nemlendirici formül. Tüm cilt tipleri için uygun.'],
                ['title' => 'Ruj Seti', 'price_min' => 99, 'price_max' => 299, 'description' => 'Mat ruj seti, 6 farklı ton. Uzun süre kalıcı formül.'],
                ['title' => 'Göz Farı Paleti', 'price_min' => 149, 'price_max' => 449, 'description' => '12 farklı ton göz farı paleti. Simli ve mat seçenekler.'],
                ['title' => 'Saç Bakım Maskesi', 'price_min' => 69, 'price_max' => 199, 'description' => 'Yoğun bakım maskesi. Kuru ve yıpranmış saçlar için.'],
                ['title' => 'Parfüm EDT', 'price_min' => 199, 'price_max' => 799, 'description' => 'Uzun süre kalıcı parfüm. Taze ve çiçeksi notalar.'],
            ],
            'ev-yasam' => [
                ['title' => 'Dekoratif Yastık', 'price_min' => 49, 'price_max' => 199, 'description' => 'Dekoratif kırlent. Yumuşak kadife kumaş.'],
                ['title' => 'LED Masa Lambası', 'price_min' => 149, 'price_max' => 399, 'description' => 'Ayarlanabilir LED lamba. 3 farklı ışık modu.'],
                ['title' => 'Seramik Vazo', 'price_min' => 99, 'price_max' => 349, 'description' => 'El yapımı seramik vazo. Modern tasarım.'],
                ['title' => 'Mutfak Seti', 'price_min' => 199, 'price_max' => 599, 'description' => '6 parça mutfak seti. Çelik gövde, silikon sap.'],
                ['title' => 'Banyo Havlusu Set', 'price_min' => 149, 'price_max' => 399, 'description' => '4 parça havlu seti. %100 pamuk, yumuşak doku.'],
            ],
            'spor' => [
                ['title' => 'Koşu Ayakkabısı', 'price_min' => 399, 'price_max' => 1299, 'description' => 'Hafif ve esnek koşu ayakkabısı. Nefes alan mesh üst.'],
                ['title' => 'Spor Tayt', 'price_min' => 149, 'price_max' => 399, 'description' => 'Yüksek bel spor tayt. Squat proof, 4 yönlü esneklik.'],
                ['title' => 'Yoga Matı', 'price_min' => 99, 'price_max' => 299, 'description' => 'Kaymaz yoga matı. 6mm kalınlık, taşıma kayışlı.'],
                ['title' => 'Dambıl Seti', 'price_min' => 199, 'price_max' => 599, 'description' => 'Ayarlanabilir dambıl seti. 2-10 kg arası.'],
                ['title' => 'Spor Çanta', 'price_min' => 149, 'price_max' => 449, 'description' => 'Geniş spor çantası. Ayakkabı bölmeli, su geçirmez.'],
            ],
            'ayakkabi-canta' => [
                ['title' => 'Günlük Sneaker', 'price_min' => 299, 'price_max' => 799, 'description' => 'Rahat günlük sneaker. Memory foam taban.'],
                ['title' => 'Deri Çanta', 'price_min' => 399, 'price_max' => 1299, 'description' => 'Hakiki deri el çantası. Klasik tasarım.'],
                ['title' => 'Spor Ayakkabı', 'price_min' => 349, 'price_max' => 999, 'description' => 'Günlük spor ayakkabı. Esnek taban, hafif yapı.'],
                ['title' => 'Omuz Çantası', 'price_min' => 249, 'price_max' => 699, 'description' => 'Pratik omuz çantası. Ayarlanabilir askı.'],
                ['title' => 'Cüzdan', 'price_min' => 99, 'price_max' => 399, 'description' => 'Deri cüzdan. Çoklu kart bölmeli.'],
            ],
            'anne-cocuk' => [
                ['title' => 'Bebek Tulumu', 'price_min' => 99, 'price_max' => 249, 'description' => 'Yumuşak pamuklu bebek tulumu. 0-12 ay.'],
                ['title' => 'Çocuk T-Shirt', 'price_min' => 49, 'price_max' => 149, 'description' => 'Renkli baskılı çocuk t-shirt. Organik pamuk.'],
                ['title' => 'Oyuncak Seti', 'price_min' => 99, 'price_max' => 399, 'description' => 'Eğitici oyuncak seti. BPA içermez.'],
                ['title' => 'Bebek Bakım Seti', 'price_min' => 149, 'price_max' => 349, 'description' => '10 parça bebek bakım seti. Şampuan, losyon dahil.'],
                ['title' => 'Çocuk Ayakkabı', 'price_min' => 149, 'price_max' => 349, 'description' => 'Anatomik çocuk ayakkabısı. Esnek taban.'],
            ],
            'supermarket' => [
                ['title' => 'Organik Bal', 'price_min' => 79, 'price_max' => 199, 'description' => '%100 doğal organik bal. 450gr cam kavanoz.'],
                ['title' => 'Zeytinyağı', 'price_min' => 149, 'price_max' => 399, 'description' => 'Soğuk sıkım natürel sızma zeytinyağı. 1L.'],
                ['title' => 'Kahve Çekirdeği', 'price_min' => 99, 'price_max' => 299, 'description' => 'Öğütülmüş Türk kahvesi çekirdeği. 500gr.'],
                ['title' => 'Deterjan Seti', 'price_min' => 99, 'price_max' => 249, 'description' => 'Çamaşır ve bulaşık deterjanı seti.'],
                ['title' => 'Kuru Meyve Paketi', 'price_min' => 69, 'price_max' => 179, 'description' => 'Karışık kuru meyve paketi. 500gr.'],
            ],
        ];

        return $templates[$groupKey] ?? $templates['giyim'];
    }

    protected function addProductImages(Product $product, string $groupKey): void
    {
        $imageUrls = $this->getImageUrlsForGroup($groupKey);
        $selectedImages = array_rand(array_flip($imageUrls), min(4, count($imageUrls)));

        if (!is_array($selectedImages)) {
            $selectedImages = [$selectedImages];
        }

        // Mevcut resimleri sil
        $product->images()->delete();

        foreach ($selectedImages as $index => $url) {
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'order' => $index],
                [
                    'url' => $url,
                    'alt' => $product->title,
                    'is_main' => $index === 0,
                ]
            );
        }
    }

    protected function getImageUrlsForGroup(string $groupKey): array
    {
        $images = [
            'giyim' => [
                'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=600&h=800&fit=crop',
                'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=600&h=800&fit=crop',
                'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=600&h=800&fit=crop',
                'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=600&h=800&fit=crop',
                'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&h=800&fit=crop',
            ],
            'elektronik' => [
                'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb?w=600&h=600&fit=crop',
            ],
            'kozmetik' => [
                'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?w=600&h=600&fit=crop',
            ],
            'ev-yasam' => [
                'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1513519245088-0e12902e35ca?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1616627561839-074385245ff6?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=600&h=600&fit=crop',
            ],
            'spor' => [
                'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1518459031867-a89b944bffe4?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&h=600&fit=crop',
            ],
            'ayakkabi-canta' => [
                'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1627123424574-724758594e93?w=600&h=600&fit=crop',
            ],
            'anne-cocuk' => [
                'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1558060370-d644479cb6f7?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1519689680058-324335c77eba?w=600&h=600&fit=crop',
            ],
            'supermarket' => [
                'https://images.unsplash.com/photo-1542838132-92c53300491e?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1498837167922-ddd27525d352?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1563453392212-326f5e854473?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=600&h=600&fit=crop',
            ],
        ];

        return $images[$groupKey] ?? $images['giyim'];
    }

    protected function addProductVariants(Product $product, string $groupKey): void
    {
        // Mevcut varyantları sil
        $product->variants()->delete();

        $variantConfig = $this->getVariantConfigForGroup($groupKey);

        if (empty($variantConfig)) {
            return; // Kozmetik gibi kategorilerde varyant yok
        }

        // Varyant kombinasyonları oluştur
        $combinations = $this->generateVariantCombinations($variantConfig);

        foreach ($combinations as $combo) {
            $priceModifier = rand(-10, 20); // -10% ile +20% arası fiyat farkı
            $variantPrice = $product->price * (1 + $priceModifier / 100);

            ProductVariant::updateOrCreate(
                ['product_id' => $product->id, 'sku' => $product->slug . '-' . implode('-', array_values($combo))],
                [
                    'value' => implode(' / ', array_values($combo)),
                    'color' => $combo['renk'] ?? ($combo['ton'] ?? null),
                    'size' => $combo['beden'] ?? null,
                    'price' => round($variantPrice, 2),
                    'original_price' => $product->discount_price ? round($variantPrice, 2) : null,
                    'sale_price' => $product->discount_price ? round($product->discount_price * (1 + $priceModifier / 100), 2) : null,
                    'stock' => rand(0, 50),
                    'is_active' => true,
                    'is_default' => false,
                ]
            );
        }

        // İlk varyantı default yap
        $product->variants()->first()?->update(['is_default' => true]);
    }

    protected function getVariantConfigForGroup(string $groupKey): array
    {
        $configs = [
            'giyim' => [
                'beden' => ['XS', 'S', 'M', 'L', 'XL'],
                'renk' => ['Siyah', 'Beyaz', 'Mavi', 'Kırmızı'],
            ],
            'elektronik' => [
                'renk' => ['Siyah', 'Beyaz', 'Gri'],
                // Elektronik'te kapasite/ram gibi özellikler ürüne özel olabilir
            ],
            'kozmetik' => [
                // Kozmetik'te beden YOK - sadece ton/hacim olabilir ama basit tutalım
                'ton' => ['Açık', 'Orta', 'Koyu'],
            ],
            'ev-yasam' => [
                'renk' => ['Beyaz', 'Gri', 'Bej', 'Kahverengi'],
            ],
            'spor' => [
                'beden' => ['S', 'M', 'L', 'XL'],
                'renk' => ['Siyah', 'Beyaz', 'Mavi'],
            ],
            'ayakkabi-canta' => [
                'beden' => ['36', '37', '38', '39', '40', '41', '42', '43'],
                'renk' => ['Siyah', 'Beyaz', 'Kahverengi'],
            ],
            'anne-cocuk' => [
                'beden' => ['0-6 Ay', '6-12 Ay', '1-2 Yaş', '2-3 Yaş'],
                'renk' => ['Pembe', 'Mavi', 'Beyaz', 'Sarı'],
            ],
            'supermarket' => [], // Supermarket'te genelde varyant yok
        ];

        return $configs[$groupKey] ?? [];
    }

    protected function generateVariantCombinations(array $config): array
    {
        if (empty($config)) {
            return [];
        }

        $keys = array_keys($config);
        $values = array_values($config);

        // Sadece ilk 2 değeri al her attribute'dan (çok fazla kombinasyon olmasın)
        $values = array_map(fn($v) => array_slice($v, 0, 3), $values);

        $combinations = [[]];

        foreach ($values as $i => $attributeValues) {
            $newCombinations = [];
            foreach ($combinations as $combination) {
                foreach ($attributeValues as $value) {
                    $newCombination = $combination;
                    $newCombination[$keys[$i]] = $value;
                    $newCombinations[] = $newCombination;
                }
            }
            $combinations = $newCombinations;
        }

        // Maximum 9 kombinasyon döndür
        return array_slice($combinations, 0, 9);
    }
}
