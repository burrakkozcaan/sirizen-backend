<?php

namespace Database\Seeders;

use App\Models\AttributeSet;
use Illuminate\Database\Seeder;

class AttributeSetSeeder extends Seeder
{
    public function run(): void
    {
        $sets = [
            // Giyim (category_group_id: 1)
            [
                'id' => 1,
                'key' => 'kadin-giyim',
                'name' => 'Kadın Giyim',
                'category_group_id' => 1,
                'description' => 'Kadın giyim ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'key' => 'erkek-giyim',
                'name' => 'Erkek Giyim',
                'category_group_id' => 1,
                'description' => 'Erkek giyim ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'id' => 3,
                'key' => 'dis-giyim',
                'name' => 'Dış Giyim',
                'category_group_id' => 1,
                'description' => 'Mont, kaban, ceket için özellik seti',
                'is_active' => true,
            ],

            // Elektronik (category_group_id: 2)
            [
                'id' => 4,
                'key' => 'telefon',
                'name' => 'Cep Telefonu',
                'category_group_id' => 2,
                'description' => 'Cep telefonu ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'id' => 5,
                'key' => 'bilgisayar',
                'name' => 'Bilgisayar',
                'category_group_id' => 2,
                'description' => 'Bilgisayar ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'id' => 6,
                'key' => 'tv-ses',
                'name' => 'TV & Ses Sistemleri',
                'category_group_id' => 2,
                'description' => 'TV ve ses sistemleri için özellik seti',
                'is_active' => true,
            ],

            // Kozmetik (category_group_id: 3)
            [
                'id' => 7,
                'key' => 'makyaj',
                'name' => 'Makyaj Ürünleri',
                'category_group_id' => 3,
                'description' => 'Makyaj ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'id' => 8,
                'key' => 'cilt-bakimi',
                'name' => 'Cilt Bakım',
                'category_group_id' => 3,
                'description' => 'Cilt bakım ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'id' => 9,
                'key' => 'parfum',
                'name' => 'Parfüm',
                'category_group_id' => 3,
                'description' => 'Parfüm ürünleri için özellik seti',
                'is_active' => true,
            ],

            // Ev & Yaşam (category_group_id: 4)
            [
                'id' => 10,
                'key' => 'mobilya',
                'name' => 'Mobilya',
                'category_group_id' => 4,
                'description' => 'Mobilya ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'id' => 11,
                'key' => 'ev-tekstili',
                'name' => 'Ev Tekstili',
                'category_group_id' => 4,
                'description' => 'Ev tekstili ürünleri için özellik seti',
                'is_active' => true,
            ],

            // Spor (category_group_id: 5)
            [
                'id' => 12,
                'key' => 'spor-giyim',
                'name' => 'Spor Giyim',
                'category_group_id' => 5,
                'description' => 'Spor giyim ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'id' => 13,
                'key' => 'spor-ekipman',
                'name' => 'Spor Ekipmanları',
                'category_group_id' => 5,
                'description' => 'Spor ekipmanları için özellik seti',
                'is_active' => true,
            ],

            // Ayakkabı & Çanta (category_group_id: 6)
            [
                'id' => 14,
                'key' => 'ayakkabi',
                'name' => 'Ayakkabı',
                'category_group_id' => 6,
                'description' => 'Ayakkabı ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'id' => 15,
                'key' => 'canta',
                'name' => 'Çanta',
                'category_group_id' => 6,
                'description' => 'Çanta ürünleri için özellik seti',
                'is_active' => true,
            ],

            // Anne & Çocuk (category_group_id: 7)
            [
                'id' => 16,
                'key' => 'bebek-giyim',
                'name' => 'Bebek Giyim',
                'category_group_id' => 7,
                'description' => 'Bebek giyim ürünleri için özellik seti',
                'is_active' => true,
            ],
            [
                'id' => 17,
                'key' => 'oyuncak',
                'name' => 'Oyuncak',
                'category_group_id' => 7,
                'description' => 'Oyuncak ürünleri için özellik seti',
                'is_active' => true,
            ],

            // Süpermarket (category_group_id: 8)
            [
                'id' => 18,
                'key' => 'gida',
                'name' => 'Gıda Ürünleri',
                'category_group_id' => 8,
                'description' => 'Gıda ürünleri için özellik seti',
                'is_active' => true,
            ],
        ];

        foreach ($sets as $set) {
            AttributeSet::updateOrCreate(
                ['id' => $set['id']],
                $set
            );
        }
    }
}
