<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Koton', 'slug' => 'koton', 'logo' => 'https://cdn.dsmcdn.com/ty11/seller/logo/koton/koton_1598857541927.jpg'],
            ['name' => 'LC Waikiki', 'slug' => 'lc-waikiki', 'logo' => 'https://cdn.dsmcdn.com/ty11/seller/logo/lcwaikiki/lcwaikiki_1598857541927.jpg'],
            ['name' => 'Nike', 'slug' => 'nike', 'logo' => 'https://cdn.dsmcdn.com/ty11/seller/logo/nike/nike_1598857541927.jpg'],
            ['name' => 'Zara', 'slug' => 'zara', 'logo' => 'https://cdn.dsmcdn.com/ty11/seller/logo/zara/zara_1598857541927.jpg'],
            ['name' => 'Mavi', 'slug' => 'mavi', 'logo' => 'https://cdn.dsmcdn.com/ty11/seller/logo/mavi/mavi_1598857541927.jpg'],
            ['name' => 'Adidas', 'slug' => 'adidas'],
            ['name' => 'Puma', 'slug' => 'puma'],
            ['name' => 'H&M', 'slug' => 'hm'],
            ['name' => 'Mango', 'slug' => 'mango'],
            ['name' => 'Pull&Bear', 'slug' => 'pullbear'],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['slug' => $brand['slug']],
                $brand
            );
        }
    }
}
