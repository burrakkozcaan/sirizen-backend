<?php

namespace Database\Seeders;

use App\Models\BadgeDefinition;
use Illuminate\Database\Seeder;

class BadgeDefinitionSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'key' => 'bestseller',
                'label' => 'Çok Satan',
                'icon' => 'fire',
                'color' => 'red',
                'bg_color' => '#fee2e2',
                'text_color' => '#991b1b',
                'priority' => 10,
                'is_active' => true,
            ],
            [
                'key' => 'new',
                'label' => 'Yeni',
                'icon' => 'sparkles',
                'color' => 'green',
                'bg_color' => '#dcfce7',
                'text_color' => '#166534',
                'priority' => 9,
                'is_active' => true,
            ],
            [
                'key' => 'discount',
                'label' => 'İndirimli',
                'icon' => 'tag',
                'color' => 'orange',
                'bg_color' => '#fff7ed',
                'text_color' => '#9a3412',
                'priority' => 8,
                'is_active' => true,
            ],
            [
                'key' => 'high_rating',
                'label' => 'Yüksek Puanlı',
                'icon' => 'star',
                'color' => 'yellow',
                'bg_color' => '#fef9c3',
                'text_color' => '#854d0e',
                'priority' => 7,
                'is_active' => true,
            ],
            [
                'key' => 'popular',
                'label' => 'Popüler',
                'icon' => 'trending-up',
                'color' => 'purple',
                'bg_color' => '#f3e8ff',
                'text_color' => '#6b21a8',
                'priority' => 6,
                'is_active' => true,
            ],
            [
                'key' => 'free_shipping',
                'label' => 'Ücretsiz Kargo',
                'icon' => 'truck',
                'color' => 'blue',
                'bg_color' => '#dbeafe',
                'text_color' => '#1e40af',
                'priority' => 5,
                'is_active' => true,
            ],
            [
                'key' => 'fast_delivery',
                'label' => 'Hızlı Teslimat',
                'icon' => 'bolt',
                'color' => 'cyan',
                'bg_color' => '#cffafe',
                'text_color' => '#164e63',
                'priority' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($badges as $badge) {
            BadgeDefinition::firstOrCreate(
                ['key' => $badge['key']],
                $badge
            );
        }

        $this->command->info('BadgeDefinition seed tamamlandı: ' . BadgeDefinition::count() . ' rozet tanımı oluşturuldu.');
    }
}
