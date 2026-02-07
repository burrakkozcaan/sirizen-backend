<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryGroup;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Pre-load category groups by key for dynamic lookups
        $groupIds = CategoryGroup::pluck('id', 'key');

        $categories = [
            // Ana kategoriler - Unsplash resimleri
            [
                'name' => 'Kadın',
                'slug' => 'kadin',
                'icon' => '👗',
                'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=400&h=400&fit=crop',
                'order' => 1,
                'category_group_id' => $groupIds['giyim'],
            ],
            [
                'name' => 'Erkek',
                'slug' => 'erkek',
                'icon' => '👔',
                'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop',
                'order' => 2,
                'category_group_id' => $groupIds['giyim'],
            ],
            [
                'name' => 'Anne & Çocuk',
                'slug' => 'anne-cocuk',
                'icon' => '👶',
                'image' => 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=400&h=400&fit=crop',
                'order' => 3,
                'category_group_id' => $groupIds['anne-cocuk'],
            ],
            [
                'name' => 'Ev & Yaşam',
                'slug' => 'ev-yasam',
                'icon' => '🏠',
                'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=400&fit=crop',
                'order' => 4,
                'category_group_id' => $groupIds['ev-yasam'],
            ],
            [
                'name' => 'Süpermarket',
                'slug' => 'supermarket',
                'icon' => '🛒',
                'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&h=400&fit=crop',
                'order' => 5,
                'category_group_id' => $groupIds['supermarket'],
            ],
            [
                'name' => 'Kozmetik',
                'slug' => 'kozmetik',
                'icon' => '💄',
                'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=400&fit=crop',
                'order' => 6,
                'category_group_id' => $groupIds['kozmetik'],
            ],
            [
                'name' => 'Ayakkabı & Çanta',
                'slug' => 'ayakkabi-canta',
                'icon' => '👠',
                'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400&h=400&fit=crop',
                'order' => 7,
                'category_group_id' => $groupIds['ayakkabi-canta'],
            ],
            [
                'name' => 'Elektronik',
                'slug' => 'elektronik',
                'icon' => '📱',
                'image' => 'https://images.unsplash.com/photo-1468495244123-6c6c332eeece?w=400&h=400&fit=crop',
                'order' => 8,
                'category_group_id' => $groupIds['elektronik'],
            ],
            [
                'name' => 'Spor & Outdoor',
                'slug' => 'spor-outdoor',
                'icon' => '⚽',
                'image' => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=400&h=400&fit=crop',
                'order' => 9,
                'category_group_id' => $groupIds['spor'],
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        // Look up parent category IDs by slug
        $parentIds = Category::whereNull('parent_id')
            ->pluck('id', 'slug');

        // Sub-categories with Unsplash images
        $subCategories = [
            // Kadın alt kategorileri
            ['parent_slug' => 'kadin', 'name' => 'Giyim', 'slug' => 'kadin-giyim', 'order' => 1, 'category_group_key' => 'giyim', 'image' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=200&h=250&fit=crop'],
            ['parent_slug' => 'kadin', 'name' => 'Ayakkabı', 'slug' => 'kadin-ayakkabi', 'order' => 2, 'category_group_key' => 'ayakkabi-canta', 'image' => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=200&h=250&fit=crop'],
            ['parent_slug' => 'kadin', 'name' => 'Çanta', 'slug' => 'kadin-canta', 'order' => 3, 'category_group_key' => 'ayakkabi-canta', 'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=200&h=250&fit=crop'],
            ['parent_slug' => 'kadin', 'name' => 'Aksesuar', 'slug' => 'kadin-aksesuar', 'order' => 4, 'category_group_key' => 'giyim', 'image' => 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=200&h=250&fit=crop'],
            ['parent_slug' => 'kadin', 'name' => 'Saat', 'slug' => 'kadin-saat', 'order' => 5, 'category_group_key' => 'elektronik', 'image' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=200&h=250&fit=crop'],

            // Erkek alt kategorileri
            ['parent_slug' => 'erkek', 'name' => 'Giyim', 'slug' => 'erkek-giyim', 'order' => 1, 'category_group_key' => 'giyim', 'image' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=200&h=250&fit=crop'],
            ['parent_slug' => 'erkek', 'name' => 'Ayakkabı', 'slug' => 'erkek-ayakkabi', 'order' => 2, 'category_group_key' => 'ayakkabi-canta', 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=200&h=250&fit=crop'],
            ['parent_slug' => 'erkek', 'name' => 'Aksesuar', 'slug' => 'erkek-aksesuar', 'order' => 3, 'category_group_key' => 'giyim', 'image' => 'https://images.unsplash.com/photo-1594534475808-b18fc33b045e?w=200&h=250&fit=crop'],
            ['parent_slug' => 'erkek', 'name' => 'Saat', 'slug' => 'erkek-saat', 'order' => 4, 'category_group_key' => 'elektronik', 'image' => 'https://images.unsplash.com/photo-1539874754764-5a96559165b0?w=200&h=250&fit=crop'],

            // Anne & Çocuk alt kategorileri
            ['parent_slug' => 'anne-cocuk', 'name' => 'Bebek Giyim', 'slug' => 'bebek-giyim', 'order' => 1, 'category_group_key' => 'anne-cocuk', 'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=200&h=250&fit=crop'],
            ['parent_slug' => 'anne-cocuk', 'name' => 'Çocuk Giyim', 'slug' => 'cocuk-giyim', 'order' => 2, 'category_group_key' => 'anne-cocuk', 'image' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?w=200&h=250&fit=crop'],
            ['parent_slug' => 'anne-cocuk', 'name' => 'Oyuncak', 'slug' => 'oyuncak', 'order' => 3, 'category_group_key' => 'anne-cocuk', 'image' => 'https://images.unsplash.com/photo-1558060370-d644479cb6f7?w=200&h=250&fit=crop'],
            ['parent_slug' => 'anne-cocuk', 'name' => 'Bebek Bakım', 'slug' => 'bebek-bakim', 'order' => 4, 'category_group_key' => 'anne-cocuk', 'image' => 'https://images.unsplash.com/photo-1519689680058-324335c77eba?w=200&h=250&fit=crop'],

            // Ev & Yaşam alt kategorileri
            ['parent_slug' => 'ev-yasam', 'name' => 'Mobilya', 'slug' => 'mobilya', 'order' => 1, 'category_group_key' => 'ev-yasam', 'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=200&h=250&fit=crop'],
            ['parent_slug' => 'ev-yasam', 'name' => 'Ev Tekstili', 'slug' => 'ev-tekstili', 'order' => 2, 'category_group_key' => 'ev-yasam', 'image' => 'https://images.unsplash.com/photo-1616627561839-074385245ff6?w=200&h=250&fit=crop'],
            ['parent_slug' => 'ev-yasam', 'name' => 'Dekorasyon', 'slug' => 'dekorasyon', 'order' => 3, 'category_group_key' => 'ev-yasam', 'image' => 'https://images.unsplash.com/photo-1513519245088-0e12902e35ca?w=200&h=250&fit=crop'],
            ['parent_slug' => 'ev-yasam', 'name' => 'Mutfak', 'slug' => 'mutfak', 'order' => 4, 'category_group_key' => 'ev-yasam', 'image' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=200&h=250&fit=crop'],
            ['parent_slug' => 'ev-yasam', 'name' => 'Banyo', 'slug' => 'banyo', 'order' => 5, 'category_group_key' => 'ev-yasam', 'image' => 'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?w=200&h=250&fit=crop'],

            // Süpermarket alt kategorileri
            ['parent_slug' => 'supermarket', 'name' => 'Gıda', 'slug' => 'gida', 'order' => 1, 'category_group_key' => 'supermarket', 'image' => 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?w=200&h=250&fit=crop'],
            ['parent_slug' => 'supermarket', 'name' => 'İçecek', 'slug' => 'icecek', 'order' => 2, 'category_group_key' => 'supermarket', 'image' => 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=200&h=250&fit=crop'],
            ['parent_slug' => 'supermarket', 'name' => 'Temizlik', 'slug' => 'temizlik', 'order' => 3, 'category_group_key' => 'supermarket', 'image' => 'https://images.unsplash.com/photo-1563453392212-326f5e854473?w=200&h=250&fit=crop'],
            ['parent_slug' => 'supermarket', 'name' => 'Kişisel Bakım', 'slug' => 'kisisel-bakim', 'order' => 4, 'category_group_key' => 'supermarket', 'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=200&h=250&fit=crop'],
            ['parent_slug' => 'supermarket', 'name' => 'Pet Shop', 'slug' => 'pet-shop', 'order' => 5, 'category_group_key' => 'supermarket', 'image' => 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=200&h=250&fit=crop'],

            // Kozmetik alt kategorileri
            ['parent_slug' => 'kozmetik', 'name' => 'Makyaj', 'slug' => 'makyaj', 'order' => 1, 'category_group_key' => 'kozmetik', 'image' => 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?w=200&h=250&fit=crop'],
            ['parent_slug' => 'kozmetik', 'name' => 'Cilt Bakımı', 'slug' => 'cilt-bakimi', 'order' => 2, 'category_group_key' => 'kozmetik', 'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=200&h=250&fit=crop'],
            ['parent_slug' => 'kozmetik', 'name' => 'Parfüm', 'slug' => 'parfum', 'order' => 3, 'category_group_key' => 'kozmetik', 'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=200&h=250&fit=crop'],
            ['parent_slug' => 'kozmetik', 'name' => 'Saç Bakımı', 'slug' => 'sac-bakimi', 'order' => 4, 'category_group_key' => 'kozmetik', 'image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=200&h=250&fit=crop'],

            // Ayakkabı & Çanta alt kategorileri
            ['parent_slug' => 'ayakkabi-canta', 'name' => 'Kadın Ayakkabı', 'slug' => 'kadin-ayakkabi-ana', 'order' => 1, 'category_group_key' => 'ayakkabi-canta', 'image' => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=200&h=250&fit=crop'],
            ['parent_slug' => 'ayakkabi-canta', 'name' => 'Erkek Ayakkabı', 'slug' => 'erkek-ayakkabi-ana', 'order' => 2, 'category_group_key' => 'ayakkabi-canta', 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=200&h=250&fit=crop'],
            ['parent_slug' => 'ayakkabi-canta', 'name' => 'Çanta', 'slug' => 'canta', 'order' => 3, 'category_group_key' => 'ayakkabi-canta', 'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=200&h=250&fit=crop'],
            ['parent_slug' => 'ayakkabi-canta', 'name' => 'Cüzdan', 'slug' => 'cuzdan', 'order' => 4, 'category_group_key' => 'ayakkabi-canta', 'image' => 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=200&h=250&fit=crop'],
            ['parent_slug' => 'ayakkabi-canta', 'name' => 'Valiz', 'slug' => 'valiz', 'order' => 5, 'category_group_key' => 'ayakkabi-canta', 'image' => 'https://images.unsplash.com/photo-1565026057447-bc90a3dceb87?w=200&h=250&fit=crop'],

            // Elektronik alt kategorileri
            ['parent_slug' => 'elektronik', 'name' => 'Telefon', 'slug' => 'telefon', 'order' => 1, 'category_group_key' => 'elektronik', 'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=200&h=250&fit=crop'],
            ['parent_slug' => 'elektronik', 'name' => 'Bilgisayar', 'slug' => 'bilgisayar', 'order' => 2, 'category_group_key' => 'elektronik', 'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=200&h=250&fit=crop'],
            ['parent_slug' => 'elektronik', 'name' => 'TV & Ses', 'slug' => 'tv-ses', 'order' => 3, 'category_group_key' => 'elektronik', 'image' => 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?w=200&h=250&fit=crop'],
            ['parent_slug' => 'elektronik', 'name' => 'Aksesuar', 'slug' => 'elektronik-aksesuar', 'order' => 4, 'category_group_key' => 'elektronik', 'image' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=200&h=250&fit=crop'],
            ['parent_slug' => 'elektronik', 'name' => 'Küçük Ev Aletleri', 'slug' => 'kucuk-ev-aletleri', 'order' => 5, 'category_group_key' => 'elektronik', 'image' => 'https://images.unsplash.com/photo-1570222094114-d054a817e56b?w=200&h=250&fit=crop'],

            // Spor & Outdoor alt kategorileri
            ['parent_slug' => 'spor-outdoor', 'name' => 'Spor Giyim', 'slug' => 'spor-giyim', 'order' => 1, 'category_group_key' => 'spor', 'image' => 'https://images.unsplash.com/photo-1518459031867-a89b944bffe4?w=200&h=250&fit=crop'],
            ['parent_slug' => 'spor-outdoor', 'name' => 'Spor Ayakkabı', 'slug' => 'spor-ayakkabi', 'order' => 2, 'category_group_key' => 'spor', 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=200&h=250&fit=crop'],
            ['parent_slug' => 'spor-outdoor', 'name' => 'Outdoor', 'slug' => 'outdoor', 'order' => 3, 'category_group_key' => 'spor', 'image' => 'https://images.unsplash.com/photo-1445307806294-bff7f67ff225?w=200&h=250&fit=crop'],
            ['parent_slug' => 'spor-outdoor', 'name' => 'Fitness', 'slug' => 'fitness', 'order' => 4, 'category_group_key' => 'spor', 'image' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=200&h=250&fit=crop'],
            ['parent_slug' => 'spor-outdoor', 'name' => 'Bisiklet', 'slug' => 'bisiklet', 'order' => 5, 'category_group_key' => 'spor', 'image' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=200&h=250&fit=crop'],
        ];

        foreach ($subCategories as $subCategory) {
            $parentSlug = $subCategory['parent_slug'];
            $groupKey = $subCategory['category_group_key'];
            unset($subCategory['parent_slug'], $subCategory['category_group_key']);

            $subCategory['parent_id'] = $parentIds[$parentSlug];
            $subCategory['category_group_id'] = $groupIds[$groupKey];

            Category::updateOrCreate(
                ['slug' => $subCategory['slug']],
                $subCategory
            );
        }
    }
}
