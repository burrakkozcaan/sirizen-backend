<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductStatsSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $adminUsers = User::where('role', 'admin')->get();

        if ($products->isEmpty()) {
            return;
        }

        $vendors = \App\Models\Vendor::where('status', 'active')->get();

        foreach ($products as $product) {
            // Product Stats
            $views = fake()->numberBetween(100, 10000);
            $addToCart = (int) ($views * fake()->randomFloat(2, 0.05, 0.2));
            $purchases = (int) ($addToCart * fake()->randomFloat(2, 0.3, 0.7));

            DB::table('product_stats')->insert([
                'product_id' => $product->id,
                'views' => $views,
                'add_to_cart' => $addToCart,
                'purchases' => $purchases,
                'created_at' => $product->created_at,
                'updated_at' => now(),
            ]);

            // Product Approvals (ürün onay geçmişi)
            // Ürünün satıcısı yoksa rastgele bir satıcı ata
            $vendorId = $product->vendor_id;
            if (! $vendorId && $vendors->isNotEmpty()) {
                $vendorId = $vendors->random()->id;
            }

            if ($vendorId) {
                $status = fake()->randomElement(['pending', 'approved', 'rejected', 'revision_requested']);
                $reviewedBy = $status !== 'pending' && $adminUsers->isNotEmpty()
                    ? $adminUsers->random()->id
                    : null;

                DB::table('product_approvals')->insert([
                    'product_id' => $product->id,
                    'vendor_id' => $vendorId,
                    'status' => $status,
                    'rejection_reason' => $status === 'rejected' ? 'Ürün görselleri yetersiz' : null,
                    'admin_notes' => $reviewedBy ? fake()->optional(0.5)->sentence() : null,
                    'changes_requested' => $status === 'revision_requested' ? json_encode([
                        'Ürün açıklaması daha detaylı olmalı',
                        'Ürün görselleri yüksek çözünürlüklü olmalı',
                    ]) : null,
                    'reviewed_by' => $reviewedBy,
                    'submitted_at' => $product->created_at,
                    'reviewed_at' => $reviewedBy ? fake()->dateTimeBetween($product->created_at, 'now') : null,
                    'created_at' => $product->created_at,
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
