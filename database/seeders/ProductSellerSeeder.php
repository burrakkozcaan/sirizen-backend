<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductSeller;
use App\Models\ProductVariant;
use App\Models\Vendor;
use App\Services\BuyBoxService;
use Illuminate\Database\Seeder;

class ProductSellerSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = Vendor::where('status', 'active')->get();
        $products = Product::with('variants')->get();

        if ($vendors->isEmpty() || $products->isEmpty()) {
            return;
        }

        foreach ($products as $product) {
            $vendorCount = rand(2, min(4, $vendors->count()));
            $selectedVendors = $vendors->random($vendorCount);

            // Ürün bazlı satıcılar (varyant yoksa)
            if ($product->variants->isEmpty()) {
                $this->createProductSellers($product, null, $selectedVendors);
            } else {
                // Her varyant için satıcılar
                foreach ($product->variants as $variant) {
                    $variantVendors = $selectedVendors->random(rand(1, $selectedVendors->count()));
                    $this->createProductSellers($product, $variant, $variantVendors);
                }
            }
        }

        // BuyBox hesapla
        $buyBoxService = new BuyBoxService;
        $buyBoxService->recalculateAll();
    }

    private function createProductSellers(Product $product, ?ProductVariant $variant, $vendors): void
    {
        $basePrice = $variant?->price ?? $product->price ?? fake()->randomFloat(2, 50, 500);

        foreach ($vendors as $index => $vendor) {
            // Fiyat varyasyonu: -%5 ile +%15 arası
            $priceVariation = fake()->randomFloat(2, -5, 15);
            $vendorPrice = $basePrice * (1 + $priceVariation / 100);

            // %30 ihtimalle indirimli fiyat
            $hasSale = fake()->boolean(30);
            $salePrice = $hasSale ? $vendorPrice * fake()->randomFloat(2, 0.8, 0.95) : null;

            // Satıcı SKU oluştur
            $skuParts = [
                strtoupper(substr($vendor->slug, 0, 3)),
                $product->id,
                $variant?->id ?? 0,
                fake()->randomNumber(4, true),
            ];

            ProductSeller::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'vendor_id' => $vendor->id,
                ],
                [
                    'seller_sku' => implode('-', $skuParts),
                    'price' => round($vendorPrice, 2),
                    'sale_price' => $salePrice ? round($salePrice, 2) : null,
                    'stock' => fake()->numberBetween(0, 150),
                    'dispatch_days' => fake()->numberBetween(0, 5),
                    'shipping_type' => fake()->randomElement(['normal', 'express', 'same_day']),
                    'free_shipping' => fake()->boolean(40),
                    'is_featured' => $index === 0,
                    'is_buybox_winner' => false, // BuyBoxService hesaplayacak
                    'created_at' => now()->subDays(fake()->numberBetween(1, 60)),
                ]
            );
        }
    }
}
