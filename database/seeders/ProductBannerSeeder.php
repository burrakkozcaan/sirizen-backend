<?php

namespace Database\Seeders;

use App\Models\ProductBanner;
use Illuminate\Database\Seeder;

class ProductBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'product_id' => 1,
                'title' => 'Kış İndirimi',
                'image' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=1200',
                'position' => 'top_left',
                'is_active' => true,
            ],
            [
                'product_id' => 2,
                'title' => 'Hızlı Teslimat',
                'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=1200',
                'position' => 'top_right',
                'is_active' => true,
            ],
            [
                'product_id' => 3,
                'title' => 'Sepette %20',
                'image' => 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=1200',
                'position' => 'under_gallery',
                'is_active' => true,
            ],
        ];

        foreach ($banners as $banner) {
            ProductBanner::updateOrCreate(
                ['product_id' => $banner['product_id'], 'position' => $banner['position']],
                $banner
            );
        }
    }
}
