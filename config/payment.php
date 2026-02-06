<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | Varsayılan ödeme gateway'i. Geliştirme ortamında 'test' kullanılır.
    |
    */
    'default' => env('PAYMENT_GATEWAY', 'test'),

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways
    |--------------------------------------------------------------------------
    |
    | Desteklenen ödeme gateway'leri ve yapılandırmaları.
    |
    */
    'gateways' => [
        'test' => [
            'auto_approve' => env('TEST_PAYMENT_AUTO_APPROVE', true),
            'delay_seconds' => env('TEST_PAYMENT_DELAY', 0),
        ],

        'paytr' => [
            'merchant_id' => env('PAYTR_MERCHANT_ID'),
            'merchant_key' => env('PAYTR_MERCHANT_KEY'),
            'merchant_salt' => env('PAYTR_MERCHANT_SALT'),
            'base_url' => env('PAYTR_BASE_URL', 'https://www.paytr.com'),
            'test_mode' => env('PAYTR_TEST_MODE', true),
            'timeout' => env('PAYTR_TIMEOUT', 30),
            'debug_on' => env('PAYTR_DEBUG', false),
            'lang' => env('PAYTR_LANG', 'tr'),
            'no_installment' => env('PAYTR_NO_INSTALLMENT', false),
            'max_installment' => env('PAYTR_MAX_INSTALLMENT', 12),
        ],

        'iyzico' => [
            'api_key' => env('IYZICO_API_KEY'),
            'secret_key' => env('IYZICO_SECRET_KEY'),
            'base_url' => env('IYZICO_BASE_URL', 'https://api.iyzipay.com'),
            'test_mode' => env('IYZICO_TEST_MODE', true),
            'locale' => env('IYZICO_LOCALE', 'tr'),
            'currency' => env('IYZICO_CURRENCY', 'TRY'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Commission Settings
    |--------------------------------------------------------------------------
    |
    | Platform komisyon ayarları.
    |
    */
    'commission' => [
        'default_rate' => env('COMMISSION_DEFAULT_RATE', 10.00),
        'min_amount' => env('COMMISSION_MIN_AMOUNT', 1.00),
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | Varsayılan para birimi.
    |
    */
    'currency' => env('PAYMENT_CURRENCY', 'TRY'),

    /*
    |--------------------------------------------------------------------------
    | Callback URLs
    |--------------------------------------------------------------------------
    |
    | Ödeme callback URL'leri.
    |
    */
    'callbacks' => [
        'success_url' => env('PAYMENT_SUCCESS_URL', '/checkout/success'),
        'fail_url' => env('PAYMENT_FAIL_URL', '/checkout/fail'),
    ],
];
