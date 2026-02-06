<?php

use App\Services\Payment\PaymentGatewayFactory;
use App\Services\Payment\TestPaymentGateway;

// These tests use the Laravel application context
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('PaymentGatewayFactory Unit Tests', function () {
    test('returns correct gateway instance', function () {
        $factory = new PaymentGatewayFactory;

        expect($factory->make('test'))->toBeInstanceOf(TestPaymentGateway::class);
    });

    test('supports method works correctly', function () {
        $factory = new PaymentGatewayFactory;

        expect($factory->supports('test'))->toBeTrue();
        expect($factory->supports('paytr'))->toBeTrue();
        expect($factory->supports('iyzico'))->toBeTrue();
        expect($factory->supports('nonexistent'))->toBeFalse();
    });

    test('can register new gateway', function () {
        $factory = new PaymentGatewayFactory;

        expect($factory->supports('custom'))->toBeFalse();

        $factory->register('custom', TestPaymentGateway::class);

        expect($factory->supports('custom'))->toBeTrue();
    });

    test('getRegisteredProviders returns all providers', function () {
        $factory = new PaymentGatewayFactory;

        $providers = $factory->getRegisteredProviders();

        expect($providers)->toContain('test');
        expect($providers)->toContain('paytr');
        expect($providers)->toContain('iyzico');
    });

    test('throws exception for unsupported provider', function () {
        $factory = new PaymentGatewayFactory;
        $factory->make('nonexistent');
    })->throws(InvalidArgumentException::class);
});

describe('TestPaymentGateway Unit Tests', function () {
    test('returns correct provider name', function () {
        $gateway = new TestPaymentGateway;

        expect($gateway->getProviderName())->toBe('test');
    });

    test('verifies successful payment', function () {
        $gateway = new TestPaymentGateway;

        $result = $gateway->verifyPayment([
            'token' => 'test_abc123',
            'status' => 'success',
        ]);

        expect($result['success'])->toBeTrue();
        expect($result['status'])->toBe('completed');
        expect($result['transaction_id'])->toStartWith('test_txn_');
    });

    test('verifies failed payment', function () {
        $gateway = new TestPaymentGateway;

        $result = $gateway->verifyPayment([
            'token' => 'test_abc123',
            'status' => 'fail',
        ]);

        expect($result['success'])->toBeFalse();
        expect($result['status'])->toBe('failed');
    });

    test('rejects invalid token', function () {
        $gateway = new TestPaymentGateway;

        $result = $gateway->verifyPayment([
            'token' => 'invalid_token',
        ]);

        expect($result['success'])->toBeFalse();
        expect($result['error'])->toContain('Invalid');
    });

    test('queries payment status', function () {
        $gateway = new TestPaymentGateway;

        $result = $gateway->queryPaymentStatus('test_txn_123');

        expect($result['success'])->toBeTrue();
        expect($result['status'])->toBe('completed');
    });

    test('rejects invalid transaction id in status query', function () {
        $gateway = new TestPaymentGateway;

        $result = $gateway->queryPaymentStatus('invalid_123');

        expect($result['success'])->toBeFalse();
    });
});
