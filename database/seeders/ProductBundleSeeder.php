<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductBundle;
use Illuminate\Database\Seeder;

class ProductBundleSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::where('is_active', true)
            ->with('category')
            ->limit(50) // İlk 50 aktif ürün için bundle oluştur
            ->get();

        if ($products->isEmpty()) {
            $this->command->warn('Aktif ürün bulunamadı. Önce ProductSeeder çalıştırın.');
            return;
        }

        $bundleTypes = ['quantity_discount', 'set', 'combo'];
        $createdCount = 0;

        foreach ($products as $mainProduct) {
            // Her ürün için %30 ihtimalle bundle oluştur
            if (rand(1, 100) > 30) {
                continue;
            }

            // Aynı kategorideki diğer ürünleri bul
            $relatedProducts = Product::where('category_id', $mainProduct->category_id)
                ->where('id', '!=', $mainProduct->id)
                ->where('is_active', true)
                ->limit(rand(2, 4)) // 2-4 ürünlü paket
                ->get();

            if ($relatedProducts->isEmpty()) {
                continue;
            }

            $bundleType = $bundleTypes[array_rand($bundleTypes)];
            $discountRate = match ($bundleType) {
                'quantity_discount' => rand(5, 15), // %5-15 indirim
                'set' => rand(10, 20), // %10-20 indirim
                'combo' => rand(15, 25), // %15-25 indirim
                default => 10,
            };

            $bundle = ProductBundle::create([
                'main_product_id' => $mainProduct->id,
                'title' => match ($bundleType) {
                    'quantity_discount' => "Çok Al Az Öde - {$mainProduct->title}",
                    'set' => "Set Ürün - {$mainProduct->title}",
                    'combo' => "Kombinasyon Paketi - {$mainProduct->title}",
                    default => "Paket - {$mainProduct->title}",
                },
                'bundle_type' => $bundleType,
                'discount_rate' => $discountRate,
                'is_active' => true,
            ]);

            // Bundle'a ürünleri ekle
            $order = 0;
            foreach ($relatedProducts as $product) {
                $bundle->products()->attach($product->id, ['order' => $order++]);
            }

            $createdCount++;
        }

        $this->command->info("ProductBundle seed tamamlandı: {$createdCount} paket oluşturuldu.");
    }
}
