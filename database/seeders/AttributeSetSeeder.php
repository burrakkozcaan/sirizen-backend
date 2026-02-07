<?php

namespace Database\Seeders;

use App\Models\AttributeSet;
use App\Models\CategoryGroup;
use Illuminate\Database\Seeder;

class AttributeSetSeeder extends Seeder
{
    public function run(): void
    {
        // Pre-load category group IDs by key for dynamic lookups
        $groupIds = CategoryGroup::pluck('id', 'key');

        $sets = [
            // Giyim
            [
                'key' => 'kadin-giyim',
                'name' => 'Kadın Giyim',
                'category_group_id' => $groupIds['giyim'],
                'description' => 'Kadın giyim ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'key' => 'erkek-giyim',
                'name' => 'Erkek Giyim',
                'category_group_id' => $groupIds['giyim'],
                'description' => 'Erkek giyim ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'key' => 'dis-giyim',
                'name' => 'Dış Giyim',
                'category_group_id' => $groupIds['giyim'],
                'description' => 'Mont, kaban, ceket için özellik seti',
                'is_active' => true,
            ],

            // Elektronik
            [
                'key' => 'telefon',
                'name' => 'Cep Telefonu',
                'category_group_id' => $groupIds['elektronik'],
                'description' => 'Cep telefonu ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'key' => 'bilgisayar',
                'name' => 'Bilgisayar',
                'category_group_id' => $groupIds['elektronik'],
                'description' => 'Bilgisayar ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'key' => 'tv-ses',
                'name' => 'TV & Ses Sistemleri',
                'category_group_id' => $groupIds['elektronik'],
                'description' => 'TV ve ses sistemleri için özellik seti',
                'is_active' => true,
            ],

            // Kozmetik
            [
                'key' => 'makyaj',
                'name' => 'Makyaj Ürünleri',
                'category_group_id' => $groupIds['kozmetik'],
                'description' => 'Makyaj ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'key' => 'cilt-bakimi',
                'name' => 'Cilt Bakım',
                'category_group_id' => $groupIds['kozmetik'],
                'description' => 'Cilt bakım ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'key' => 'parfum',
                'name' => 'Parfüm',
                'category_group_id' => $groupIds['kozmetik'],
                'description' => 'Parfüm ürünleri için özellik seti',
                'is_active' => true,
            ],

            // Ev & Yaşam
            [
                'key' => 'mobilya',
                'name' => 'Mobilya',
                'category_group_id' => $groupIds['ev-yasam'],
                'description' => 'Mobilya ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'key' => 'ev-tekstili',
                'name' => 'Ev Tekstili',
                'category_group_id' => $groupIds['ev-yasam'],
                'description' => 'Ev tekstili ürünleri için özellik seti',
                'is_active' => true,
            ],

            // Spor
            [
                'key' => 'spor-giyim',
                'name' => 'Spor Giyim',
                'category_group_id' => $groupIds['spor'],
                'description' => 'Spor giyim ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'key' => 'spor-ekipman',
                'name' => 'Spor Ekipmanları',
                'category_group_id' => $groupIds['spor'],
                'description' => 'Spor ekipmanları için özellik seti',
                'is_active' => true,
            ],

            // Ayakkabı & Çanta
            [
                'key' => 'ayakkabi',
                'name' => 'Ayakkabı',
                'category_group_id' => $groupIds['ayakkabi-canta'],
                'description' => 'Ayakkabı ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'key' => 'canta',
                'name' => 'Çanta',
                'category_group_id' => $groupIds['ayakkabi-canta'],
                'description' => 'Çanta ürünleri için özellik seti',
                'is_active' => true,
            ],

            // Anne & Çocuk
            [
                'key' => 'bebek-giyim',
                'name' => 'Bebek Giyim',
                'category_group_id' => $groupIds['anne-cocuk'],
                'description' => 'Bebek giyim ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'key' => 'oyuncak',
                'name' => 'Oyuncak',
                'category_group_id' => $groupIds['anne-cocuk'],
                'description' => 'Oyuncak ürünleri için özellik seti',
                'is_active' => true,
            ],

            // Süpermarket
            [
                'key' => 'gida',
                'name' => 'Gıda Ürünleri',
                'category_group_id' => $groupIds['supermarket'],
                'description' => 'Gıda ürünleri için özellik seti',
                'is_active' => true,
            ],
        ];

        foreach ($sets as $set) {
            AttributeSet::updateOrCreate(
                ['key' => $set['key']],
                $set
            );
        }
    }
}
