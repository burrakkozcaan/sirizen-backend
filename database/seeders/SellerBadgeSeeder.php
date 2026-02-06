<?php

namespace Database\Seeders;

use App\Models\SellerBadge;
use App\Models\SellerBadgeAssignment;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class SellerBadgeSeeder extends Seeder
{
    public function run(): void
    {
        // Satıcı rozetlerini oluştur
        $badges = [
            [
                'name' => 'Hızlı Kargo',
                'slug' => 'fast-shipping',
                'icon' => '🚀',
                'color' => '#10b981',
                'description' => 'Bu satıcı siparişleri çok hızlı kargolar',
                'is_active' => true,
            ],
            [
                'name' => 'Ücretsiz İade',
                'slug' => 'free-returns',
                'icon' => '🔄',
                'color' => '#3b82f6',
                'description' => 'Bu satıcıdan ücretsiz iade yapabilirsiniz',
                'is_active' => true,
            ],
            [
                'name' => 'Yüksek Puan',
                'slug' => 'high-rating',
                'icon' => '⭐',
                'color' => '#f59e0b',
                'description' => 'Müşteri memnuniyeti çok yüksek',
                'is_active' => true,
            ],
            [
                'name' => 'Güvenilir Satıcı',
                'slug' => 'trusted-seller',
                'icon' => '✓',
                'color' => '#8b5cf6',
                'description' => 'Platform tarafından onaylanmış güvenilir satıcı',
                'is_active' => true,
            ],
            [
                'name' => 'Çok Satan',
                'slug' => 'best-seller',
                'icon' => '🏆',
                'color' => '#ef4444',
                'description' => 'En çok satış yapan satıcılardan biri',
                'is_active' => true,
            ],
            [
                'name' => 'Yeni Satıcı',
                'slug' => 'new-seller',
                'icon' => '🌟',
                'color' => '#06b6d4',
                'description' => 'Platformda yeni satış yapmaya başladı',
                'is_active' => true,
            ],
        ];

        foreach ($badges as $badgeData) {
            SellerBadge::create($badgeData);
        }

        // Satıcılara rastgele rozetler ata
        $vendors = Vendor::all();
        $allBadges = SellerBadge::all();

        if ($vendors->isEmpty() || $allBadges->isEmpty()) {
            return;
        }

        foreach ($vendors as $vendor) {
            // Her satıcıya 1-3 rozet ata
            $badgeCount = rand(1, 3);
            $selectedBadges = $allBadges->random(min($badgeCount, $allBadges->count()));

            foreach ($selectedBadges as $badge) {
                SellerBadgeAssignment::create([
                    'vendor_id' => $vendor->id,
                    'badge_id' => $badge->id,
                    'assigned_at' => now()->subDays(rand(1, 60)),
                    'expires_at' => rand(0, 1) ? now()->addMonths(rand(3, 12)) : null,
                ]);
            }
        }
    }
}
