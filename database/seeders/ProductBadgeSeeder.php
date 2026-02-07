<?php

namespace Database\Seeders;

use App\Models\ProductBadge;
use Illuminate\Database\Seeder;

class ProductBadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'product_id' => 1,
                'label' => 'Çok Satan',
                'color' => 'danger',
                'icon' => 'heroicon-m-fire',
            ],
            [
                'product_id' => 2,
                'label' => 'Yeni',
                'color' => 'success',
                'icon' => 'heroicon-m-sparkles',
            ],
            [
                'product_id' => 3,
                'label' => 'Sepette İndirim',
                'color' => 'warning',
                'icon' => 'heroicon-m-ticket',
            ],
        ];

        foreach ($badges as $badge) {
            ProductBadge::updateOrCreate(
                ['product_id' => $badge['product_id'], 'label' => $badge['label']],
                $badge
            );
        }
    }
}
