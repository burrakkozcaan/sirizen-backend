<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = Vendor::where('status', 'active')->get();
        $products = Product::all();
        $customers = User::where('role', 'customer')->get();
        $orders = Order::all();

        // Genel kuponlar (vendor_id = null)
        $generalCoupons = [
            [
                'code' => 'HOSGELDIN10',
                'title' => 'Hoş Geldin İndirimi',
                'description' => 'İlk alışverişinize özel %10 indirim',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'min_order_amount' => 100,
                'max_discount_amount' => 50,
                'usage_limit' => 1000,
                'per_user_limit' => 1,
            ],
            [
                'code' => 'YAZ2024',
                'title' => 'Yaz Kampanyası',
                'description' => 'Tüm yaz koleksiyonunda geçerli',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'min_order_amount' => 200,
                'max_discount_amount' => 100,
                'usage_limit' => 500,
                'per_user_limit' => 2,
            ],
            [
                'code' => 'KARGO50',
                'title' => '50 TL Kargo İndirimi',
                'description' => '250 TL ve üzeri alışverişlerde kargo bedava',
                'discount_type' => 'fixed',
                'discount_value' => 50,
                'min_order_amount' => 250,
                'max_discount_amount' => 50,
                'usage_limit' => 2000,
                'per_user_limit' => 5,
            ],
        ];

        foreach ($generalCoupons as $coupon) {
            $couponId = DB::table('coupons')->insertGetId([
                'vendor_id' => null,
                'product_id' => null,
                'code' => $coupon['code'],
                'title' => $coupon['title'],
                'description' => $coupon['description'],
                'discount_type' => $coupon['discount_type'],
                'discount_value' => $coupon['discount_value'],
                'min_order_amount' => $coupon['min_order_amount'],
                'max_discount_amount' => $coupon['max_discount_amount'],
                'usage_limit' => $coupon['usage_limit'],
                'per_user_limit' => $coupon['per_user_limit'],
                'starts_at' => now()->subDays(30),
                'expires_at' => now()->addDays(60),
                'is_active' => true,
                'created_at' => now()->subDays(30),
                'updated_at' => now(),
            ]);

            // Bazı kullanımlar ekle
            $this->createCouponUsages($couponId, $customers, $orders, $coupon['discount_type'], $coupon['discount_value']);
        }

        // Vendor bazlı kuponlar
        foreach ($vendors as $vendor) {
            DB::table('coupons')->insert([
                'vendor_id' => $vendor->id,
                'product_id' => null,
                'code' => strtoupper(substr($vendor->slug, 0, 4)) . rand(10, 99),
                'title' => $vendor->name . ' Özel İndirim',
                'description' => $vendor->name . ' mağazasına özel indirim kuponu',
                'discount_type' => fake()->randomElement(['percentage', 'fixed']),
                'discount_value' => fake()->randomElement([10, 15, 20, 25, 50]),
                'min_order_amount' => fake()->randomElement([100, 150, 200, 250]),
                'max_discount_amount' => fake()->randomElement([50, 75, 100, 150]),
                'usage_limit' => rand(100, 500),
                'per_user_limit' => rand(1, 3),
                'starts_at' => now()->subDays(rand(1, 15)),
                'expires_at' => now()->addDays(rand(15, 45)),
                'is_active' => fake()->boolean(80),
                'created_at' => now()->subDays(rand(15, 45)),
                'updated_at' => now(),
            ]);
        }

        // Ürün bazlı kuponlar
        if ($products->isNotEmpty()) {
            foreach ($products->random(min(3, $products->count())) as $product) {
                DB::table('coupons')->insert([
                    'vendor_id' => $product->vendor_id,
                    'product_id' => $product->id,
                    'code' => 'URUN' . $product->id . rand(10, 99),
                    'title' => $product->title . ' İndirimi',
                    'description' => 'Bu ürüne özel indirim',
                    'discount_type' => 'percentage',
                    'discount_value' => rand(5, 20),
                    'min_order_amount' => null,
                    'max_discount_amount' => 100,
                    'usage_limit' => rand(50, 200),
                    'per_user_limit' => 1,
                    'starts_at' => now(),
                    'expires_at' => now()->addDays(14),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function createCouponUsages(int $couponId, $customers, $orders, string $discountType, float $discountValue): void
    {
        if ($customers->isEmpty() || $orders->isEmpty()) {
            return;
        }

        // Rastgele 2-5 kullanım ekle
        $usageCount = rand(2, min(5, $customers->count()));
        $selectedCustomers = $customers->random($usageCount);

        foreach ($selectedCustomers as $customer) {
            $customerOrders = $orders->where('user_id', $customer->id);

            if ($customerOrders->isNotEmpty()) {
                $order = $customerOrders->random();
                $discountAmount = $discountType === 'percentage'
                    ? $order->total_price * ($discountValue / 100)
                    : $discountValue;

                DB::table('coupon_usages')->insert([
                    'coupon_id' => $couponId,
                    'user_id' => $customer->id,
                    'order_id' => $order->id,
                    'discount_amount' => min($discountAmount, 100),
                    'used_at' => $order->created_at,
                    'created_at' => $order->created_at,
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
