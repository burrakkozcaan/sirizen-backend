<?php

namespace Database\Seeders;

use App\Models\CategoryGroup;
use App\Models\SocialProofRule;
use Illuminate\Database\Seeder;

class SocialProofRuleSeeder extends Seeder
{
    public function run(): void
    {
        $categoryGroups = CategoryGroup::all();

        if ($categoryGroups->isEmpty()) {
            $this->command->warn('CategoryGroups bulunamadı. Önce CategoryGroupSeeder çalıştırın.');
            return;
        }

        foreach ($categoryGroups as $categoryGroup) {
            // Sepete Eklenme Sayısı (Trendyol: "3.2K kişinin sepetinde")
            SocialProofRule::create([
                'category_group_id' => $categoryGroup->id,
                'type' => 'cart_count',
                'display_format' => '{count} kişinin sepetinde',
                'threshold_type' => 'fixed',
                'threshold_value' => 50, // En az 50 kişi
                'refresh_interval' => 3600, // 1 saatte bir güncelle
                'position' => 'top',
                'color' => 'orange',
                'icon' => 'shopping-cart',
                'is_active' => true,
            ]);

            // Görüntülenme Sayısı (Trendyol: "Son 24 saatte 1.2B görüntülendi")
            SocialProofRule::create([
                'category_group_id' => $categoryGroup->id,
                'type' => 'view_count',
                'display_format' => 'Son 24 saatte {count} görüntülendi',
                'threshold_type' => 'fixed',
                'threshold_value' => 100, // En az 100 görüntülenme
                'refresh_interval' => 3600,
                'position' => 'top',
                'color' => 'blue',
                'icon' => 'eye',
                'is_active' => true,
            ]);

            // Satış Sayısı (Trendyol: "Son 1 ayda 2.5B satıldı")
            SocialProofRule::create([
                'category_group_id' => $categoryGroup->id,
                'type' => 'sold_count',
                'display_format' => 'Son 1 ayda {count} satıldı',
                'threshold_type' => 'fixed',
                'threshold_value' => 10, // En az 10 satış
                'refresh_interval' => 86400, // 24 saatte bir güncelle
                'position' => 'middle',
                'color' => 'green',
                'icon' => 'check-circle',
                'is_active' => true,
            ]);

            // Değerlendirme Sayısı (Trendyol: "1.2B değerlendirme")
            SocialProofRule::create([
                'category_group_id' => $categoryGroup->id,
                'type' => 'review_count',
                'display_format' => '{count} değerlendirme',
                'threshold_type' => 'fixed',
                'threshold_value' => 5, // En az 5 değerlendirme
                'refresh_interval' => 3600,
                'position' => 'bottom',
                'color' => 'purple',
                'icon' => 'star',
                'is_active' => true,
            ]);
        }

        $this->command->info('SocialProofRule seed tamamlandı: ' . SocialProofRule::count() . ' kural oluşturuldu.');
    }
}
