<?php

use App\Models\Commission;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorBalance;
use App\Models\VendorTier;
use App\PaymentProvider;
use App\PaymentStatus;
use App\Services\Payment\PaymentService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('Commission Calculation', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->vendor = Vendor::factory()->create();

        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
            'total_price' => 200.00,
        ]);

        $this->orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'vendor_id' => $this->vendor->id,
            'unit_price' => 100.00,
            'quantity' => 2,
            'price' => 200.00,
        ]);

        $this->payment = Payment::factory()->create([
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
            'amount' => 200.00,
            'payment_provider' => PaymentProvider::Test,
            'status' => PaymentStatus::Completed,
        ]);
    });

    test('calculates commission with default rate', function () {
        $paymentService = app(PaymentService::class);

        $commission = $paymentService->calculateCommission($this->orderItem);

        // Default rate is 10% of 200 = 20
        $defaultRate = config('payment.commission.default_rate', 10.0);
        expect($commission)->toBe(200.0 * ($defaultRate / 100));
    });

    test('uses custom rate when provided', function () {
        $paymentService = app(PaymentService::class);

        $commission = $paymentService->calculateCommission($this->orderItem, 20.0);

        // %20 of 200 = 40
        expect($commission)->toBe(40.0);
    });

    test('uses vendor tier commission rate when available', function () {
        $tier = VendorTier::create([
            'name' => 'Special',
            'commission_rate' => 15.00,
        ]);

        $this->vendor->update([
            'tier_id' => $tier->id,
        ]);

        $paymentService = app(PaymentService::class);

        $commission = $paymentService->calculateCommission($this->orderItem);

        // %15 of 200 = 30
        expect($commission)->toBe(30.0);
    });

    test('respects minimum commission amount', function () {
        $this->orderItem->update(['price' => 5.00]);

        $paymentService = app(PaymentService::class);
        $commission = $paymentService->calculateCommission($this->orderItem, 1.0); // %1

        // %1 of 5 = 0.05, but minimum is 1.00
        $minAmount = config('payment.commission.min_amount', 1.0);
        expect($commission)->toBeGreaterThanOrEqual($minAmount);
    });

    test('settles commission and updates vendor balance', function () {
        $paymentService = app(PaymentService::class);

        $paymentService->settleCommission($this->order, $this->payment);

        // Check commission was created
        $commission = Commission::where('payment_id', $this->payment->id)->first();
        expect($commission)->not->toBeNull();
        expect($commission->vendor_id)->toBe($this->vendor->id);
        expect((float) $commission->gross_amount)->toBe(200.0);

        // Default rate is 10%
        $defaultRate = config('payment.commission.default_rate', 10.0);
        expect((float) $commission->commission_rate)->toBe($defaultRate);
        expect((float) $commission->commission_amount)->toBe(20.0);
        expect((float) $commission->net_amount)->toBe(180.0);

        // Check vendor balance was updated
        $balance = VendorBalance::where('vendor_id', $this->vendor->id)->first();
        expect($balance)->not->toBeNull();
        expect((float) $balance->pending_balance)->toBe(180.0);
        expect((float) $balance->total_earnings)->toBe(180.0);

        // Check payment was updated
        $this->payment->refresh();
        expect((float) $this->payment->commission_amount)->toBe(20.0);
        expect((float) $this->payment->vendor_amount)->toBe(180.0);
        expect($this->payment->split_status)->toBe('settled');
    });

    test('handles multiple order items from different vendors', function () {
        $vendor2 = Vendor::factory()->create();

        OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'vendor_id' => $vendor2->id,
            'unit_price' => 50.00,
            'quantity' => 1,
            'price' => 50.00,
        ]);

        $this->order->update(['total_price' => 250.00]);
        $this->payment->update(['amount' => 250.00]);

        $paymentService = app(PaymentService::class);
        $paymentService->settleCommission($this->order, $this->payment);

        // Check commissions for both vendors
        $commissions = Commission::where('payment_id', $this->payment->id)->get();
        expect($commissions)->toHaveCount(2);

        // Both use default 10% rate
        // Vendor 1: 200 * 10% = 20 commission, 180 net
        $commission1 = $commissions->where('vendor_id', $this->vendor->id)->first();
        expect((float) $commission1->commission_amount)->toBe(20.0);

        // Vendor 2: 50 * 10% = 5 commission, 45 net
        $commission2 = $commissions->where('vendor_id', $vendor2->id)->first();
        expect((float) $commission2->commission_amount)->toBe(5.0);
    });
});

describe('Commission Refund', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->vendor = Vendor::factory()->create();

        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'total_price' => 100.00,
        ]);

        $this->orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'vendor_id' => $this->vendor->id,
            'price' => 100.00,
        ]);

        $this->payment = Payment::factory()->create([
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'payment_provider' => PaymentProvider::Test,
            'status' => PaymentStatus::Completed,
        ]);

        // Settle commission first
        $paymentService = app(PaymentService::class);
        $paymentService->settleCommission($this->order, $this->payment);
    });

    test('processes full refund and reverses commissions', function () {
        $paymentService = app(PaymentService::class);

        $result = $paymentService->processRefund($this->payment);

        expect($result['success'])->toBeTrue();

        $this->payment->refresh();
        expect($this->payment->status)->toBe(PaymentStatus::Refunded);

        // Check commission was marked as refunded
        $commission = Commission::where('payment_id', $this->payment->id)->first();
        expect($commission->status)->toBe('refunded');

        // Check vendor balance was reduced
        $balance = VendorBalance::where('vendor_id', $this->vendor->id)->first();
        expect((float) $balance->pending_balance)->toBe(0.0);
    });

    test('processes partial refund', function () {
        $paymentService = app(PaymentService::class);

        $result = $paymentService->processRefund($this->payment, 50.00);

        expect($result['success'])->toBeTrue();

        $this->payment->refresh();
        expect($this->payment->status)->toBe(PaymentStatus::PartiallyRefunded);
        expect((float) $this->payment->refunded_amount)->toBe(50.0);
    });
});
