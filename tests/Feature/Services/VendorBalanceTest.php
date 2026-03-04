<?php

use App\CommissionStatus;
use App\Events\PaymentCompleted;
use App\Events\PaymentFailed;
use App\Models\Commission;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorBalance;
use App\PaymentStatus;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('HandlePaymentCompleted Listener', function () {

    test('ödeme tamamlanınca vendor pending_balance artar', function () {
        $vendor  = Vendor::factory()->create();
        $user    = User::factory()->create();
        $order   = Order::factory()->create(['user_id' => $user->id]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'user_id'  => $user->id,
            'amount'   => 100.00,
            'status'   => PaymentStatus::Completed,
        ]);

        $orderItem = OrderItem::factory()->create([
            'order_id'  => $order->id,
            'vendor_id' => $vendor->id,
            'price'     => 100.0,
            'quantity'  => 1,
        ]);

        Commission::factory()->create([
            'vendor_id'         => $vendor->id,
            'order_item_id'     => $orderItem->id,
            'gross_amount'      => 100.0,
            'commission_rate'   => 18.0,
            'commission_amount' => 15.0,
            'net_amount'        => 85.0,
            'status'            => CommissionStatus::PENDING,
        ]);

        event(new PaymentCompleted($payment));

        $balance = VendorBalance::where('vendor_id', $vendor->id)->first();

        expect($balance)->not->toBeNull();
        expect((float) $balance->pending_balance)->toBe(85.0);
        expect((float) $balance->total_earnings)->toBe(85.0);
    });

    test('ödeme tamamlanınca komisyon durumu PAID olur', function () {
        $vendor  = Vendor::factory()->create();
        $user    = User::factory()->create();
        $order   = Order::factory()->create(['user_id' => $user->id]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'user_id'  => $user->id,
            'amount'   => 100.00,
            'status'   => PaymentStatus::Completed,
        ]);

        $orderItem = OrderItem::factory()->create([
            'order_id'  => $order->id,
            'vendor_id' => $vendor->id,
            'price'     => 100.0,
            'quantity'  => 1,
        ]);

        $commission = Commission::factory()->create([
            'vendor_id'         => $vendor->id,
            'order_item_id'     => $orderItem->id,
            'gross_amount'      => 100.0,
            'commission_rate'   => 18.0,
            'commission_amount' => 15.0,
            'net_amount'        => 85.0,
            'status'            => CommissionStatus::PENDING,
        ]);

        event(new PaymentCompleted($payment));

        $commission->refresh();
        expect($commission->status)->toBe(CommissionStatus::PAID);
        expect($commission->payment_id)->toBe($payment->id);
    });

});

describe('HandlePaymentFailed Listener', function () {

    test('ödeme başarısız olunca bekleyen komisyonlar iptal edilir', function () {
        $vendor  = Vendor::factory()->create();
        $user    = User::factory()->create();
        $order   = Order::factory()->create(['user_id' => $user->id]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'user_id'  => $user->id,
            'status'   => PaymentStatus::Failed,
        ]);

        $orderItem = OrderItem::factory()->create([
            'order_id'  => $order->id,
            'vendor_id' => $vendor->id,
        ]);

        $commission = Commission::factory()->create([
            'vendor_id'     => $vendor->id,
            'order_item_id' => $orderItem->id,
            'status'        => CommissionStatus::PENDING,
        ]);

        event(new PaymentFailed($payment, 'Kart reddi'));

        $commission->refresh();
        expect($commission->status)->toBe(CommissionStatus::CANCELLED);
    });

});

describe('VendorBalance isSplitValid', function () {

    test('split tutarları doğru olunca geçerli döner', function () {
        $payment = Payment::factory()->make([
            'amount'            => 100.00,
            'commission_amount' => 15.00,
            'vendor_amount'     => 85.00,
        ]);

        expect($payment->isSplitValid())->toBeTrue();
    });

    test('split tutarları yanlış olunca false döner', function () {
        $payment = Payment::factory()->make([
            'amount'            => 100.00,
            'commission_amount' => 20.00,
            'vendor_amount'     => 90.00, // 20+90=110 ≠ 100
        ]);

        expect($payment->isSplitValid())->toBeFalse();
    });

    test('split henüz yapılmamışsa geçerli döner', function () {
        $payment = Payment::factory()->make([
            'amount'            => 100.00,
            'commission_amount' => null,
            'vendor_amount'     => null,
        ]);

        expect($payment->isSplitValid())->toBeTrue();
    });

});
