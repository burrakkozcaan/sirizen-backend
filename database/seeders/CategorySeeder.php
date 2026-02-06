<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Ana kategoriler - Unsplash resimleri
            [
                'id' => 1,
                'name' => 'Kadın',
                'slug' => 'kadin',
                'icon' => '👗',
                'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=400&h=400&fit=crop',
                'order' => 1,
                'category_group_id' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Erkek',
                'slug' => 'erkek',
                'icon' => '👔',
                'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop',
                'order' => 2,
                'category_group_id' => 1,
            ],
            [
                'id' => 3,
                'name' => 'Anne & Çocuk',
                'slug' => 'anne-cocuk',
                'icon' => '👶',
                'image' => 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=400&h=400&fit=crop',
                'order' => 3,
                'category_group_id' => 7,
            ],
            [
                'id' => 4,
                'name' => 'Ev & Yaşam',
                'slug' => 'ev-yasam',
                'icon' => '🏠',
                'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=400&fit=crop',
                'order' => 4,
                'category_group_id' => 4,
            ],
            [
                'id' => 5,
                'name' => 'Süpermarket',
                'slug' => 'supermarket',
                'icon' => '🛒',
                'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&h=400&fit=crop',
                'order' => 5,
                'category_group_id' => 8,
            ],
            [
                'id' => 6,
                'name' => 'Kozmetik',
                'slug' => 'kozmetik',
                'icon' => '💄',
                'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=400&fit=crop',
                'order' => 6,
                'category_group_id' => 3,
            ],
            [
                'id' => 7,
                'name' => 'Ayakkabı & Çanta',
                'slug' => 'ayakkabi-canta',
                'icon' => '👠',
                'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400&h=400&fit=crop',
                'order' => 7,
                'category_group_id' => 6,
            ],
            [
                'id' => 8,
                'name' => 'Elektronik',
                'slug' => 'elektronik',
                'icon' => '📱',
                'image' => 'https://images.unsplash.com/photo-1468495244123-6c6c332eeece?w=400&h=400&fit=crop',
                'order' => 8,
                'category_group_id' => 2,
            ],
            [
                'id' => 9,
                'name' => 'Spor & Outdoor',
                'slug' => 'spor-outdoor',
                'icon' => '⚽',
                'image' => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=400&h=400&fit=crop',
                'order' => 9,
                'category_group_id' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['id' => $category['id']],
                $category
            );
        }

        // Sub-categories with Unsplash images
        $subCategories = [
            // Kadın alt kategorileri
            ['id' => 11, 'parent_id' => 1, 'name' => 'Giyim', 'slug' => 'kadin-giyim', 'order' => 1, 'category_group_id' => 1, 'image' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=200&h=250&fit=crop'],
            ['id' => 12, 'parent_id' => 1, 'name' => 'Ayakkabı', 'slug' => 'kadin-ayakkabi', 'order' => 2, 'category_group_id' => 6, 'image' => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=200&h=250&fit=crop'],
            ['id' => 13, 'parent_id' => 1, 'name' => 'Çanta', 'slug' => 'kadin-canta', 'order' => 3, 'category_group_id' => 6, 'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=200&h=250&fit=crop'],
            ['id' => 14, 'parent_id' => 1, 'name' => 'Aksesuar', 'slug' => 'kadin-aksesuar', 'order' => 4, 'category_group_id' => 1, 'image' => 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=200&h=250&fit=crop'],
            ['id' => 15, 'parent_id' => 1, 'name' => 'Saat', 'slug' => 'kadin-saat', 'order' => 5, 'category_group_id' => 2, 'image' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=200&h=250&fit=crop'],

            // Erkek alt kategorileri
            ['id' => 21, 'parent_id' => 2, 'name' => 'Giyim', 'slug' => 'erkek-giyim', 'order' => 1, 'category_group_id' => 1, 'image' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=200&h=250&fit=crop'],
            ['id' => 22, 'parent_id' => 2, 'name' => 'Ayakkabı', 'slug' => 'erkek-ayakkabi', 'order' => 2, 'category_group_id' => 6, 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=200&h=250&fit=crop'],
            ['id' => 23, 'parent_id' => 2, 'name' => 'Aksesuar', 'slug' => 'erkek-aksesuar', 'order' => 3, 'category_group_id' => 1, 'image' => 'https://images.unsplash.com/photo-1594534475808-b18fc33b045e?w=200&h=250&fit=crop'],
            ['id' => 24, 'parent_id' => 2, 'name' => 'Saat', 'slug' => 'erkek-saat', 'order' => 4, 'category_group_id' => 2, 'image' => 'https://images.unsplash.com/photo-1539874754764-5a96559165b0?w=200&h=250&fit=crop'],

            // Anne & Çocuk alt kategorileri
            ['id' => 31, 'parent_id' => 3, 'name' => 'Bebek Giyim', 'slug' => 'bebek-giyim', 'order' => 1, 'category_group_id' => 7, 'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=200&h=250&fit=crop'],
            ['id' => 32, 'parent_id' => 3, 'name' => 'Çocuk Giyim', 'slug' => 'cocuk-giyim', 'order' => 2, 'category_group_id' => 7, 'image' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?w=200&h=250&fit=crop'],
            ['id' => 33, 'parent_id' => 3, 'name' => 'Oyuncak', 'slug' => 'oyuncak', 'order' => 3, 'category_group_id' => 7, 'image' => 'https://images.unsplash.com/photo-1558060370-d644479cb6f7?w=200&h=250&fit=crop'],
            ['id' => 34, 'parent_id' => 3, 'name' => 'Bebek Bakım', 'slug' => 'bebek-bakim', 'order' => 4, 'category_group_id' => 7, 'image' => 'https://images.unsplash.com/photo-1519689680058-324335c77eba?w=200&h=250&fit=crop'],

            // Ev & Yaşam alt kategorileri
            ['id' => 41, 'parent_id' => 4, 'name' => 'Mobilya', 'slug' => 'mobilya', 'order' => 1, 'category_group_id' => 4, 'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=200&h=250&fit=crop'],
            ['id' => 42, 'parent_id' => 4, 'name' => 'Ev Tekstili', 'slug' => 'ev-tekstili', 'order' => 2, 'category_group_id' => 4, 'image' => 'https://images.unsplash.com/photo-1616627561839-074385245ff6?w=200&h=250&fit=crop'],
            ['id' => 43, 'parent_id' => 4, 'name' => 'Dekorasyon', 'slug' => 'dekorasyon', 'order' => 3, 'category_group_id' => 4, 'image' => 'https://images.unsplash.com/photo-1513519245088-0e12902e35ca?w=200&h=250&fit=crop'],
            ['id' => 44, 'parent_id' => 4, 'name' => 'Mutfak', 'slug' => 'mutfak', 'order' => 4, 'category_group_id' => 4, 'image' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=200&h=250&fit=crop'],
            ['id' => 45, 'parent_id' => 4, 'name' => 'Banyo', 'slug' => 'banyo', 'order' => 5, 'category_group_id' => 4, 'image' => 'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?w=200&h=250&fit=crop'],

            // Süpermarket alt kategorileri
            ['id' => 51, 'parent_id' => 5, 'name' => 'Gıda', 'slug' => 'gida', 'order' => 1, 'category_group_id' => 8, 'image' => 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?w=200&h=250&fit=crop'],
            ['id' => 52, 'parent_id' => 5, 'name' => 'İçecek', 'slug' => 'icecek', 'order' => 2, 'category_group_id' => 8, 'image' => 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=200&h=250&fit=crop'],
            ['id' => 53, 'parent_id' => 5, 'name' => 'Temizlik', 'slug' => 'temizlik', 'order' => 3, 'category_group_id' => 8, 'image' => 'https://images.unsplash.com/photo-1563453392212-326f5e854473?w=200&h=250&fit=crop'],
            ['id' => 54, 'parent_id' => 5, 'name' => 'Kişisel Bakım', 'slug' => 'kisisel-bakim', 'order' => 4, 'category_group_id' => 8, 'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=200&h=250&fit=crop'],
            ['id' => 55, 'parent_id' => 5, 'name' => 'Pet Shop', 'slug' => 'pet-shop', 'order' => 5, 'category_group_id' => 8, 'image' => 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=200&h=250&fit=crop'],

            // Kozmetik alt kategorileri
            ['id' => 61, 'parent_id' => 6, 'name' => 'Makyaj', 'slug' => 'makyaj', 'order' => 1, 'category_group_id' => 3, 'image' => 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?w=200&h=250&fit=crop'],
            ['id' => 62, 'parent_id' => 6, 'name' => 'Cilt Bakımı', 'slug' => 'cilt-bakimi', 'order' => 2, 'category_group_id' => 3, 'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=200&h=250&fit=crop'],
            ['id' => 63, 'parent_id' => 6, 'name' => 'Parfüm', 'slug' => 'parfum', 'order' => 3, 'category_group_id' => 3, 'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=200&h=250&fit=crop'],
            ['id' => 64, 'parent_id' => 6, 'name' => 'Saç Bakımı', 'slug' => 'sac-bakimi', 'order' => 4, 'category_group_id' => 3, 'image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=200&h=250&fit=crop'],

            // Ayakkabı & Çanta alt kategorileri
            ['id' => 71, 'parent_id' => 7, 'name' => 'Kadın Ayakkabı', 'slug' => 'kadin-ayakkabi-ana', 'order' => 1, 'category_group_id' => 6, 'image' => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=200&h=250&fit=crop'],
            ['id' => 72, 'parent_id' => 7, 'name' => 'Erkek Ayakkabı', 'slug' => 'erkek-ayakkabi-ana', 'order' => 2, 'category_group_id' => 6, 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=200&h=250&fit=crop'],
            ['id' => 73, 'parent_id' => 7, 'name' => 'Çanta', 'slug' => 'canta', 'order' => 3, 'category_group_id' => 6, 'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=200&h=250&fit=crop'],
            ['id' => 74, 'parent_id' => 7, 'name' => 'Cüzdan', 'slug' => 'cuzdan', 'order' => 4, 'category_group_id' => 6, 'image' => 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=200&h=250&fit=crop'],
            ['id' => 75, 'parent_id' => 7, 'name' => 'Valiz', 'slug' => 'valiz', 'order' => 5, 'category_group_id' => 6, 'image' => 'https://images.unsplash.com/photo-1565026057447-bc90a3dceb87?w=200&h=250&fit=crop'],

            // Elektronik alt kategorileri
            ['id' => 81, 'parent_id' => 8, 'name' => 'Telefon', 'slug' => 'telefon', 'order' => 1, 'category_group_id' => 2, 'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=200&h=250&fit=crop'],
            ['id' => 82, 'parent_id' => 8, 'name' => 'Bilgisayar', 'slug' => 'bilgisayar', 'order' => 2, 'category_group_id' => 2, 'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=200&h=250&fit=crop'],
            ['id' => 83, 'parent_id' => 8, 'name' => 'TV & Ses', 'slug' => 'tv-ses', 'order' => 3, 'category_group_id' => 2, 'image' => 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?w=200&h=250&fit=crop'],
            ['id' => 84, 'parent_id' => 8, 'name' => 'Aksesuar', 'slug' => 'elektronik-aksesuar', 'order' => 4, 'category_group_id' => 2, 'image' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=200&h=250&fit=crop'],
            ['id' => 85, 'parent_id' => 8, 'name' => 'Küçük Ev Aletleri', 'slug' => 'kucuk-ev-aletleri', 'order' => 5, 'category_group_id' => 2, 'image' => 'https://images.unsplash.com/photo-1570222094114-d054a817e56b?w=200&h=250&fit=crop'],

            // Spor & Outdoor alt kategorileri
            ['id' => 91, 'parent_id' => 9, 'name' => 'Spor Giyim', 'slug' => 'spor-giyim', 'order' => 1, 'category_group_id' => 5, 'image' => 'https://images.unsplash.com/photo-1518459031867-a89b944bffe4?w=200&h=250&fit=crop'],
            ['id' => 92, 'parent_id' => 9, 'name' => 'Spor Ayakkabı', 'slug' => 'spor-ayakkabi', 'order' => 2, 'category_group_id' => 5, 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=200&h=250&fit=crop'],
            ['id' => 93, 'parent_id' => 9, 'name' => 'Outdoor', 'slug' => 'outdoor', 'order' => 3, 'category_group_id' => 5, 'image' => 'https://images.unsplash.com/photo-1445307806294-bff7f67ff225?w=200&h=250&fit=crop'],
            ['id' => 94, 'parent_id' => 9, 'name' => 'Fitness', 'slug' => 'fitness', 'order' => 4, 'category_group_id' => 5, 'image' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=200&h=250&fit=crop'],
            ['id' => 95, 'parent_id' => 9, 'name' => 'Bisiklet', 'slug' => 'bisiklet', 'order' => 5, 'category_group_id' => 5, 'image' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=200&h=250&fit=crop'],
        ];

        foreach ($subCategories as $subCategory) {
            Category::updateOrCreate(
                ['id' => $subCategory['id']],
                $subCategory
            );
        }
    }
}
