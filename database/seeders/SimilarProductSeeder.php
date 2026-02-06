<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SimilarProduct;
use Illuminate\Database\Seeder;

class SimilarProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::where('is_active', true)
            ->with(['category', 'brand'])
            ->limit(100) // İlk 100 aktif ürün için benzer ürünler oluştur
            ->get();

        if ($products->isEmpty()) {
            $this->command->warn('Aktif ürün bulunamadı. Önce ProductSeeder çalıştırın.');
            return;
        }

        $relationTypes = ['cross_sell', 'up_sell', 'also_bought', 'similar'];
        $createdCount = 0;

        foreach ($products as $product) {
            // Her ürün için 3-8 benzer ürün oluştur
            $similarCount = rand(3, 8);

            // Aynı kategorideki veya aynı markadaki ürünleri bul
            $similarProducts = Product::where('is_active', true)
                ->where('id', '!=', $product->id)
                ->where(function ($query) use ($product) {
                    $query->where('category_id', $product->category_id)
                        ->orWhere('brand_id', $product->brand_id);
                })
                ->limit($similarCount * 2) // Daha fazla seçenek
                ->get()
                ->shuffle()
                ->take($similarCount);

            foreach ($similarProducts as $similarProduct) {
                // Benzerlik skoru hesapla (0.5 - 1.0 arası)
                $score = 0.5;
                
                // Aynı kategori ise +0.3
                if ($similarProduct->category_id === $product->category_id) {
                    $score += 0.3;
                }
                
                // Aynı marka ise +0.2
                if ($similarProduct->brand_id === $product->brand_id) {
                    $score += 0.2;
                }

                // Fiyat benzerliği (yakın fiyatlar daha benzer)
                $priceDiff = abs($similarProduct->price - $product->price);
                $priceRatio = min($priceDiff / max($product->price, 1), 1);
                $score += (1 - $priceRatio) * 0.2;

                $score = min($score, 1.0); // Maksimum 1.0

                $relationType = $relationTypes[array_rand($relationTypes)];

                SimilarProduct::create([
                    'product_id' => $product->id,
                    'similar_product_id' => $similarProduct->id,
                    'score' => round($score, 2),
                    'relation_type' => $relationType,
                ]);

                $createdCount++;
            }
        }

        $this->command->info("SimilarProduct seed tamamlandı: {$createdCount} benzer ürün ilişkisi oluşturuldu.");
    }
}
