<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\Product;
use App\Models\QuickLink;
use Illuminate\Database\Seeder;

class QuickLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fashionCategory = Category::query()->where('slug', 'kadin')->value('slug')
            ?? Category::factory()->create()->slug;
        $electronicsCategory = Category::query()->where('slug', 'elektronik')->value('slug')
            ?? Category::factory()->create()->slug;
        $shoesCategory = Category::query()->where('slug', 'ayakkabi-canta')->value('slug')
            ?? Category::factory()->create()->slug;
        $motherBabyCategory = Category::query()->where('slug', 'anne-cocuk')->value('slug')
            ?? Category::factory()->create()->slug;

        $primaryCampaign = Campaign::query()->where('slug', 'kis-indirimi')->value('slug')
            ?? Campaign::factory()->create()->slug;
        $secondaryCampaign = Campaign::query()->where('slug', 'ayakkabi-festivali')->value('slug')
            ?? Campaign::factory()->create()->slug;

        $featuredProduct = Product::query()->select(['id', 'slug'])->first()
            ?? Product::factory()->create();

        $links = [
            [
                'key' => 'price_drops',
                'label' => 'price_drops',
                'icon' => 'trending_down',
                'link_type' => 'campaign',
                'path' => "/campaign/{$primaryCampaign}",
                'category_slug' => null,
                'campaign_slug' => $primaryCampaign,
                'product_id' => null,
                'color' => 'info',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'key' => 'super_deals',
                'label' => 'super_deals',
                'icon' => 'zap',
                'link_type' => 'campaign',
                'path' => "/campaign/{$secondaryCampaign}",
                'category_slug' => null,
                'campaign_slug' => $secondaryCampaign,
                'product_id' => null,
                'color' => 'warning',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'key' => 'electronics',
                'label' => 'electronics',
                'icon' => 'tv',
                'link_type' => 'category',
                'path' => "/category/{$electronicsCategory}",
                'category_slug' => $electronicsCategory,
                'campaign_slug' => null,
                'product_id' => null,
                'color' => 'info',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'key' => 'fashion',
                'label' => 'fashion',
                'icon' => 'shirt',
                'link_type' => 'category',
                'path' => "/category/{$fashionCategory}",
                'category_slug' => $fashionCategory,
                'campaign_slug' => null,
                'product_id' => null,
                'color' => 'primary',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'key' => 'shoes_bags',
                'label' => 'shoes_bags',
                'icon' => 'shopping_bag',
                'link_type' => 'category',
                'path' => "/category/{$shoesCategory}",
                'category_slug' => $shoesCategory,
                'campaign_slug' => null,
                'product_id' => null,
                'color' => 'purple',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'key' => 'mother_baby',
                'label' => 'mother_baby',
                'icon' => 'baby',
                'link_type' => 'category',
                'path' => "/category/{$motherBabyCategory}",
                'category_slug' => $motherBabyCategory,
                'campaign_slug' => null,
                'product_id' => null,
                'color' => 'success',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'key' => 'featured_product',
                'label' => 'custom',
                'icon' => 'zap',
                'link_type' => 'product',
                'path' => "/product/{$featuredProduct->slug}",
                'category_slug' => null,
                'campaign_slug' => null,
                'product_id' => $featuredProduct->id,
                'color' => 'danger',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'key' => 'points',
                'label' => 'points',
                'icon' => 'gift',
                'link_type' => 'custom',
                'path' => '/points',
                'category_slug' => null,
                'campaign_slug' => null,
                'product_id' => null,
                'color' => 'primary',
                'order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($links as $link) {
            QuickLink::updateOrCreate(
                ['key' => $link['key']],
                $link
            );
        }
    }
}
