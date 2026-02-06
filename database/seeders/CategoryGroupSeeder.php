<?php

namespace Database\Seeders;

use App\Models\CategoryGroup;
use Illuminate\Database\Seeder;

class CategoryGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'id' => 1,
                'key' => 'giyim',
                'name' => 'Giyim',
                'icon' => 'shirt',
                'color' => '#FF6B6B',
                'metadata' => [
                    'has_size' => true,
                    'has_color' => true,
                    'has_gender' => true,
                ],
                'is_active' => true,
            ],
            [
                'id' => 2,
                'key' => 'elektronik',
                'name' => 'Elektronik',
                'icon' => 'smartphone',
                'color' => '#4ECDC4',
                'metadata' => [
                    'has_warranty' => true,
                    'has_specs' => true,
                ],
                'is_active' => true,
            ],
            [
                'id' => 3,
                'key' => 'kozmetik',
                'name' => 'Kozmetik',
                'icon' => 'sparkles',
                'color' => '#FF9F9F',
                'metadata' => [
                    'has_skin_type' => true,
                    'has_ingredients' => true,
                ],
                'is_active' => true,
            ],
            [
                'id' => 4,
                'key' => 'ev-yasam',
                'name' => 'Ev & Yaşam',
                'icon' => 'home',
                'color' => '#95D5B2',
                'metadata' => [
                    'has_dimensions' => true,
                    'has_material' => true,
                ],
                'is_active' => true,
            ],
            [
                'id' => 5,
                'key' => 'spor',
                'name' => 'Spor & Outdoor',
                'icon' => 'dumbbell',
                'color' => '#FFB347',
                'metadata' => [
                    'has_size' => true,
                    'has_sport_type' => true,
                ],
                'is_active' => true,
            ],
            [
                'id' => 6,
                'key' => 'ayakkabi-canta',
                'name' => 'Ayakkabı & Çanta',
                'icon' => 'shopping-bag',
                'color' => '#DDA0DD',
                'metadata' => [
                    'has_size' => true,
                    'has_material' => true,
                ],
                'is_active' => true,
            ],
            [
                'id' => 7,
                'key' => 'anne-cocuk',
                'name' => 'Anne & Çocuk',
                'icon' => 'baby',
                'color' => '#87CEEB',
                'metadata' => [
                    'has_age_group' => true,
                    'has_size' => true,
                ],
                'is_active' => true,
            ],
            [
                'id' => 8,
                'key' => 'supermarket',
                'name' => 'Süpermarket',
                'icon' => 'shopping-cart',
                'color' => '#98D8C8',
                'metadata' => [
                    'has_expiry' => true,
                    'has_nutrition' => true,
                ],
                'is_active' => true,
            ],
        ];

        foreach ($groups as $group) {
            CategoryGroup::updateOrCreate(
                ['id' => $group['id']],
                $group
            );
        }
    }
}
