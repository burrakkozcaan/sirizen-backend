<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembershipProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'name' => 'Plus',
                'slug' => 'plus',
                'description' => 'Ücretsiz kargo, özel kampanyalar ve erken erişim ayrıcalıkları.',
                'price_monthly' => 49.90,
                'price_yearly' => 499.00,
                'benefits' => [
                    ['key' => 'free_shipping', 'value' => true],
                    ['key' => 'early_access', 'value' => true],
                    ['key' => 'exclusive_campaigns', 'value' => true],
                ],
                'badge_icon' => 'sparkles',
                'badge_color' => 'info',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Gold',
                'slug' => 'gold',
                'description' => 'Plus ayrıcalıklarına ek olarak ekstra indirim ve destek.',
                'price_monthly' => 99.90,
                'price_yearly' => 999.00,
                'benefits' => [
                    ['key' => 'free_shipping', 'value' => true],
                    ['key' => 'early_access', 'value' => true],
                    ['key' => 'extra_discount', 'value' => 5],
                    ['key' => 'priority_support', 'value' => true],
                ],
                'badge_icon' => 'award',
                'badge_color' => 'warning',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'VIP',
                'slug' => 'vip',
                'description' => 'Maksimum ayrıcalıklar ve özel fiyatlandırma.',
                'price_monthly' => 199.90,
                'price_yearly' => 1999.00,
                'benefits' => [
                    ['key' => 'free_shipping', 'value' => true],
                    ['key' => 'early_access', 'value' => true],
                    ['key' => 'extra_discount', 'value' => 10],
                    ['key' => 'priority_support', 'value' => true],
                    ['key' => 'vip_badge', 'value' => true],
                ],
                'badge_icon' => 'star',
                'badge_color' => 'danger',
                'is_active' => true,
                'order' => 3,
            ],
        ];

        foreach ($programs as $program) {
            DB::table('membership_programs')->updateOrInsert(
                ['slug' => $program['slug']],
                [
                    'name' => $program['name'],
                    'description' => $program['description'],
                    'price_monthly' => $program['price_monthly'],
                    'price_yearly' => $program['price_yearly'],
                    'benefits' => json_encode($program['benefits']),
                    'badge_icon' => $program['badge_icon'],
                    'badge_color' => $program['badge_color'],
                    'is_active' => $program['is_active'],
                    'order' => $program['order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
