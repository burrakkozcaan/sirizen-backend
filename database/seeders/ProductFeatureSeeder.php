<?php

namespace Database\Seeders;

use App\Models\ProductFeature;
use Illuminate\Database\Seeder;

class ProductFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            [
                'product_id' => 1,
                'title' => 'Ücretsiz Kargo',
                'icon' => 'heroicon-m-truck',
            ],
            [
                'product_id' => 1,
                'title' => '2 Yıl Garanti',
                'icon' => 'heroicon-m-shield-check',
            ],
            [
                'product_id' => 2,
                'title' => 'Hızlı Teslimat',
                'icon' => 'heroicon-m-bolt',
            ],
            [
                'product_id' => 3,
                'title' => 'Ücretsiz İade',
                'icon' => 'heroicon-m-arrow-path',
            ],
        ];

        foreach ($features as $feature) {
            ProductFeature::updateOrCreate(
                ['product_id' => $feature['product_id'], 'title' => $feature['title']],
                $feature
            );
        }
    }
}
