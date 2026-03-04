<?php

use App\CommissionStatus;
use App\Models\Category;
use App\Models\Commission;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\VendorTier;
use App\Services\CommissionService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('CommissionService — Komisyon oranı hesaplama', function () {

    test('kategori bazlı komisyon oranını doğru hesaplar', function () {
        $category = Category::factory()->create(['commission_rate' => 18.00]);
        $vendor   = Vendor::factory()->create(['tier_id' => null]);
        $product  = Product::factory()->create([
            'category_id'          => $category->id,
            'custom_commission_rate' => null,
        ]);

        $service = new CommissionService();
        $rate = $service->calculateCommissionRate($product, $vendor, $category);

        expect($rate)->toBe(18.0);
    });

    test('ürün bazlı özel oran kategori oranını geçersiz kılar', function () {
        $category = Category::factory()->create(['commission_rate' => 18.00]);
        $vendor   = Vendor::factory()->create(['tier_id' => null]);
        $product  = Product::factory()->create([
            'category_id'          => $category->id,
            'custom_commission_rate' => 5.00,
        ]);

        $service = new CommissionService();
        $rate = $service->calculateCommissionRate($product, $vendor, $category);

        expect($rate)->toBe(5.0);
    });

    test('kategori oranı yoksa varsayılan oran kullanılır', function () {
        $category = Category::factory()->create(['commission_rate' => null]);
        $vendor   = Vendor::factory()->create(['tier_id' => null]);
        $product  = Product::factory()->create([
            'category_id'          => $category->id,
            'custom_commission_rate' => null,
        ]);

        $service = new CommissionService();
        $rate = $service->calculateCommissionRate($product, $vendor, $category);

        expect($rate)->toBe((float) config('marketplace.default_commission_rate', 10.0));
    });

    test('vendor tier indirimi kategori oranından düşülür', function () {
        $tier     = VendorTier::factory()->create(['commission_rate' => 200]); // 2.00% indirim
        $category = Category::factory()->create(['commission_rate' => 18.00]);
        $vendor   = Vendor::factory()->create(['tier_id' => $tier->id]);
        $product  = Product::factory()->create([
            'category_id'          => $category->id,
            'custom_commission_rate' => null,
        ]);

        $service = new CommissionService();
        $rate = $service->calculateCommissionRate($product, $vendor, $category);

        expect($rate)->toBe(16.0); // 18 - 2 = 16
    });

    test('komisyon oranı negatife düşmez', function () {
        $tier     = VendorTier::factory()->create(['commission_rate' => 2000]); // %20 indirim
        $category = Category::factory()->create(['commission_rate' => 8.00]);
        $vendor   = Vendor::factory()->create(['tier_id' => $tier->id]);
        $product  = Product::factory()->create([
            'category_id'          => $category->id,
            'custom_commission_rate' => null,
        ]);

        $service = new CommissionService();
        $rate = $service->calculateCommissionRate($product, $vendor, $category);

        expect($rate)->toBeGreaterThanOrEqual(0.0);
    });

});

describe('CommissionService — Komisyon tutarı hesaplama', function () {

    test('KDV hariç komisyon tutarı doğru hesaplanır', function () {
        $service = new CommissionService();

        // 100 TL ürün, %18 komisyon
        // KDV hariç: 100 / 1.20 = 83.33
        // Komisyon: 83.33 * 0.18 = 15.00
        $amount = $service->calculateCommissionAmount(100.0, 1, 18.0);

        expect($amount)->toBe(15.00);
    });

    test('miktar çarpımı doğru çalışır', function () {
        $service = new CommissionService();

        // 50 TL × 2 adet = 100 TL → aynı sonuç
        $amount = $service->calculateCommissionAmount(50.0, 2, 18.0);

        expect($amount)->toBe(15.00);
    });

});

describe('CommissionService — createCommission', function () {

    test('order_item için komisyon kaydı oluşturur', function () {
        $category = Category::factory()->create(['commission_rate' => 18.00]);
        $vendor   = Vendor::factory()->create(['tier_id' => null]);
        $product  = Product::factory()->create([
            'category_id'          => $category->id,
            'custom_commission_rate' => null,
        ]);
        $user  = \App\Models\User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $orderItem = OrderItem::factory()->create([
            'order_id'  => $order->id,
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'price'     => 100.0,
            'quantity'  => 1,
        ]);

        $service    = new CommissionService();
        $commission = $service->createCommission($orderItem);

        expect($commission)->toBeInstanceOf(Commission::class);
        expect($commission->vendor_id)->toBe($vendor->id);
        expect($commission->order_item_id)->toBe($orderItem->id);
        expect($commission->status)->toBe(CommissionStatus::PENDING);
        expect((float) $commission->gross_amount)->toBe(100.0);
        expect((float) $commission->commission_rate)->toBe(18.0);
    });

    test('refundCommission tam iade durumunda status REFUNDED yapar', function () {
        $commission = Commission::factory()->create([
            'commission_amount' => 15.00,
            'status'            => CommissionStatus::PAID,
            'refunded_amount'   => 0,
        ]);

        $service = new CommissionService();
        $service->refundCommission($commission, 15.00);

        $commission->refresh();
        expect($commission->status)->toBe(CommissionStatus::REFUNDED);
        expect((float) $commission->refunded_amount)->toBe(15.0);
    });

    test('refundCommission kısmi iade durumunda status PARTIALLY_REFUNDED yapar', function () {
        $commission = Commission::factory()->create([
            'commission_amount' => 15.00,
            'status'            => CommissionStatus::PAID,
            'refunded_amount'   => 0,
        ]);

        $service = new CommissionService();
        $service->refundCommission($commission, 5.00);

        $commission->refresh();
        expect($commission->status)->toBe(CommissionStatus::PARTIALLY_REFUNDED);
        expect((float) $commission->refunded_amount)->toBe(5.0);
    });

});
