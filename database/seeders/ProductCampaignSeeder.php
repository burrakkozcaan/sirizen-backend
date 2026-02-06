<?php

namespace Database\Seeders;

use App\Models\ProductCampaign;
use Illuminate\Database\Seeder;

class ProductCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $links = [
            ['product_id' => 1, 'campaign_id' => 1],
            ['product_id' => 2, 'campaign_id' => 1],
            ['product_id' => 2, 'campaign_id' => 2],
            ['product_id' => 3, 'campaign_id' => 2],
            ['product_id' => 3, 'campaign_id' => 3],
            ['product_id' => 4, 'campaign_id' => 4],
        ];

        foreach ($links as $link) {
            ProductCampaign::create($link);
        }
    }
}
