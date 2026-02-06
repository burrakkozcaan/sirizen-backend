<?php

namespace Database\Seeders;

use App\Models\BadgeDefinition;
use App\Models\BadgeRule;
use App\Models\CategoryGroup;
use Illuminate\Database\Seeder;

class BadgeRuleSeeder extends Seeder
{
    public function run(): void
    {
        $categoryGroups = CategoryGroup::all();
        $badgeDefinitions = BadgeDefinition::all();

        if ($categoryGroups->isEmpty() || $badgeDefinitions->isEmpty()) {
            $this->command->warn('CategoryGroups veya BadgeDefinitions bulunamadı. Önce bunları seed edin.');
            return;
        }

        // Çok Satan Rozeti Kuralları
        $bestsellerBadge = $badgeDefinitions->firstWhere('key', 'bestseller');
        if ($bestsellerBadge) {
            foreach ($categoryGroups as $categoryGroup) {
                BadgeRule::create([
                    'badge_definition_id' => $bestsellerBadge->id,
                    'category_group_id' => $categoryGroup->id,
                    'condition_type' => 'is_bestseller',
                    'condition_config' => [
                        'field' => 'is_bestseller',
                        'operator' => '=',
                        'value' => true,
                    ],
                    'priority' => 10,
                    'is_active' => true,
                ]);
            }
        }

        // Yeni Ürün Rozeti Kuralları
        $newBadge = $badgeDefinitions->firstWhere('key', 'new');
        if ($newBadge) {
            foreach ($categoryGroups as $categoryGroup) {
                BadgeRule::create([
                    'badge_definition_id' => $newBadge->id,
                    'category_group_id' => $categoryGroup->id,
                    'condition_type' => 'is_new',
                    'condition_config' => [
                        'field' => 'is_new',
                        'operator' => '=',
                        'value' => true,
                    ],
                    'priority' => 9,
                    'is_active' => true,
                ]);
            }
        }

        // İndirimli Ürün Rozeti Kuralları
        $discountBadge = $badgeDefinitions->firstWhere('key', 'discount');
        if ($discountBadge) {
            foreach ($categoryGroups as $categoryGroup) {
                BadgeRule::create([
                    'badge_definition_id' => $discountBadge->id,
                    'category_group_id' => $categoryGroup->id,
                    'condition_type' => 'price_discount',
                    'condition_config' => [
                        'field' => 'discount_percentage',
                        'operator' => '>=',
                        'value' => 20, // %20 ve üzeri indirim
                    ],
                    'priority' => 8,
                    'is_active' => true,
                ]);
            }
        }

        // Yüksek Puanlı Ürün Rozeti
        $highRatingBadge = $badgeDefinitions->firstWhere('key', 'high_rating');
        if ($highRatingBadge) {
            foreach ($categoryGroups as $categoryGroup) {
                BadgeRule::create([
                    'badge_definition_id' => $highRatingBadge->id,
                    'category_group_id' => $categoryGroup->id,
                    'condition_type' => 'rating',
                    'condition_config' => [
                        'field' => 'rating',
                        'operator' => '>=',
                        'value' => 4.5,
                    ],
                    'priority' => 7,
                    'is_active' => true,
                ]);
            }
        }

        // Çok Değerlendirilen Ürün Rozeti
        $popularBadge = $badgeDefinitions->firstWhere('key', 'popular');
        if ($popularBadge) {
            foreach ($categoryGroups as $categoryGroup) {
                BadgeRule::create([
                    'badge_definition_id' => $popularBadge->id,
                    'category_group_id' => $categoryGroup->id,
                    'condition_type' => 'review_count',
                    'condition_config' => [
                        'field' => 'reviews_count',
                        'operator' => '>=',
                        'value' => 100,
                    ],
                    'priority' => 6,
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('BadgeRule seed tamamlandı: ' . BadgeRule::count() . ' kural oluşturuldu.');
    }
}
