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
                ['key' => $group['key']],
                $group
            );
        }
    }
}
