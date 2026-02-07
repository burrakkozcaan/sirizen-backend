<?php

namespace Database\Seeders;

use App\Models\VendorTier;
use Illuminate\Database\Seeder;

class VendorTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            [
                'name' => 'Bronz Satıcı',
                'min_total_orders' => 0,
                'min_rating' => 3.0,
                'max_cancel_rate' => 10.0,
                'max_return_rate' => 15.0,
                'commission_rate' => 0.0, // Komisyon indirimi yok
                'priority_boost' => 0,
                'badge_icon' => '🥉',
                'max_products' => 100,
                'description' => 'Yeni satıcılar için başlangıç seviyesi',
            ],
            [
                'name' => 'Gümüş Satıcı',
                'min_total_orders' => 100,
                'min_rating' => 4.0,
                'max_cancel_rate' => 5.0,
                'max_return_rate' => 10.0,
                'commission_rate' => 1.0, // %1 komisyon indirimi
                'priority_boost' => 5,
                'badge_icon' => '🥈',
                'max_products' => 500,
                'description' => '100+ sipariş ve %4+ puan ile gümüş seviye',
            ],
            [
                'name' => 'Altın Satıcı',
                'min_total_orders' => 500,
                'min_rating' => 4.5,
                'max_cancel_rate' => 3.0,
                'max_return_rate' => 5.0,
                'commission_rate' => 2.0, // %2 komisyon indirimi
                'priority_boost' => 10,
                'badge_icon' => '🥇',
                'max_products' => 2000,
                'description' => '500+ sipariş ve %4.5+ puan ile altın seviye',
            ],
            [
                'name' => 'Elmas Satıcı',
                'min_total_orders' => 2000,
                'min_rating' => 4.8,
                'max_cancel_rate' => 1.0,
                'max_return_rate' => 3.0,
                'commission_rate' => 3.0, // %3 komisyon indirimi
                'priority_boost' => 20,
                'badge_icon' => '💎',
                'max_products' => null, // Sınırsız
                'description' => '2000+ sipariş ve %4.8+ puan ile elmas seviye',
            ],
        ];

        foreach ($tiers as $tier) {
            VendorTier::updateOrCreate(
                ['name' => $tier['name']],
                $tier
            );
        }
    }
}
