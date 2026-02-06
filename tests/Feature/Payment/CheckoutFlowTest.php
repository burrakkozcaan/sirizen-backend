<?php

use App\Models\Order;
use App\Models\User;
use App\PaymentProvider;
use App\PaymentStatus;
use App\Services\Payment\PaymentGatewayFactory;
use App\Services\Payment\PaymentService;
use App\Services\Payment\TestPaymentGateway;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('Checkout Flow', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
            'total_price' => 150.00,
        ]);
    });

    test('can initiate payment with test gateway', function () {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/checkout/pay', [
            'order_id' => $this->order->id,
            'gateway' => 'test',
        ]);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'success',
                'payment_id',
                'checkout_token',
                'html',
            ]);

        expect($response->json('success'))->toBeTrue();
        expect($response->json('checkout_token'))->toStartWith('test_');
    });

    test('cannot pay for other users order', function () {
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser, 'sanctum');

        $response = $this->postJson('/api/checkout/pay', [
            'order_id' => $this->order->id,
            'gateway' => 'test',
        ]);

        $response->assertForbidden();
    });

    test('cannot pay for already paid order', function () {
        $this->order->update(['status' => 'paid']);
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/checkout/pay', [
            'order_id' => $this->order->id,
            'gateway' => 'test',
        ]);

        $response->assertStatus(400);
    });

    test('requires authentication', function () {
        $response = $this->postJson('/api/checkout/pay', [
            'order_id' => $this->order->id,
            'gateway' => 'test',
        ]);

        $response->assertUnauthorized();
    });

    test('validates request data', function () {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/checkout/pay', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['order_id', 'gateway']);
    });

    test('validates gateway is valid', function () {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/checkout/pay', [
            'order_id' => $this->order->id,
            'gateway' => 'invalid_gateway',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['gateway']);
    });
});

describe('Test Payment Webhook', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
            'total_price' => 150.00,
        ]);
    });

    test('handles successful test payment callback', function () {
        // First initiate payment
        $paymentService = app(PaymentService::class);
        $result = $paymentService->initiatePayment($this->order, PaymentProvider::Test);

        expect($result['success'])->toBeTrue();

        $checkoutToken = $result['checkout_token'];

        // Simulate successful callback
        $response = $this->get("/api/webhooks/payment/test?token={$checkoutToken}&order_id={$this->order->id}&status=success");

        $response->assertSuccessful();

        $this->order->refresh();
        expect($this->order->payment->status)->toBe(PaymentStatus::Completed);
    });

    test('handles failed test payment callback', function () {
        $paymentService = app(PaymentService::class);
        $result = $paymentService->initiatePayment($this->order, PaymentProvider::Test);

        $checkoutToken = $result['checkout_token'];

        $response = $this->get("/api/webhooks/payment/test?token={$checkoutToken}&order_id={$this->order->id}&status=fail");

        $response->assertSuccessful();

        $this->order->refresh();
        expect($this->order->payment->status)->toBe(PaymentStatus::Failed);
    });
});

describe('Payment Gateway Factory', function () {
    test('creates test gateway', function () {
        $factory = new PaymentGatewayFactory;
        $gateway = $factory->make('test');

        expect($gateway)->toBeInstanceOf(TestPaymentGateway::class);
        expect($gateway->getProviderName())->toBe('test');
    });

    test('creates gateway from enum', function () {
        $factory = new PaymentGatewayFactory;
        $gateway = $factory->make(PaymentProvider::Test);

        expect($gateway)->toBeInstanceOf(TestPaymentGateway::class);
    });

    test('throws exception for unsupported provider', function () {
        $factory = new PaymentGatewayFactory;
        $factory->make('unsupported');
    })->throws(InvalidArgumentException::class);

    test('checks if provider is supported', function () {
        $factory = new PaymentGatewayFactory;

        expect($factory->supports('test'))->toBeTrue();
        expect($factory->supports('paytr'))->toBeTrue();
        expect($factory->supports('iyzico'))->toBeTrue();
        expect($factory->supports('unsupported'))->toBeFalse();
    });
});
