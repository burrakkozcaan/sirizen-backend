<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Cargo Provider
    |--------------------------------------------------------------------------
    |
    | Varsayılan kargo sağlayıcısı.
    |
    */
    'default' => env('CARGO_PROVIDER', 'aras'),

    /*
    |--------------------------------------------------------------------------
    | Cargo Providers
    |--------------------------------------------------------------------------
    |
    | Desteklenen kargo sağlayıcıları ve yapılandırmaları.
    |
    */
    'providers' => [
        'aras' => [
            'base_url' => env('ARAS_BASE_URL', 'https://customerservices.araskargo.com.tr'),
            'username' => env('ARAS_USERNAME'),
            'password' => env('ARAS_PASSWORD'),
            'customer_code' => env('ARAS_CUSTOMER_CODE'),
            'query_type' => env('ARAS_QUERY_TYPE', '1'),
            'test_mode' => env('ARAS_TEST_MODE', true),
            'timeout' => env('ARAS_TIMEOUT', 30),
        ],

        'yurtici' => [
            'base_url' => env('YURTICI_BASE_URL', 'https://ws.yurticikargo.com'),
            'username' => env('YURTICI_USERNAME'),
            'password' => env('YURTICI_PASSWORD'),
            'user_language' => env('YURTICI_USER_LANGUAGE', 'TR'),
            'test_mode' => env('YURTICI_TEST_MODE', true),
            'timeout' => env('YURTICI_TIMEOUT', 30),
        ],

        'mng' => [
            'base_url' => env('MNG_BASE_URL', 'https://api.mngkargo.com.tr'),
            'api_key' => env('MNG_API_KEY'),
            'api_secret' => env('MNG_API_SECRET'),
            'customer_number' => env('MNG_CUSTOMER_NUMBER'),
            'test_mode' => env('MNG_TEST_MODE', true),
            'timeout' => env('MNG_TIMEOUT', 30),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tracking Sync Schedule
    |--------------------------------------------------------------------------
    |
    | Kargo takip senkronizasyon ayarları.
    |
    */
    'tracking_sync' => [
        'enabled' => env('CARGO_TRACKING_SYNC_ENABLED', true),
        'interval_minutes' => env('CARGO_TRACKING_SYNC_INTERVAL', 30),
        'batch_size' => env('CARGO_TRACKING_SYNC_BATCH_SIZE', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Shipping Settings
    |--------------------------------------------------------------------------
    |
    | Varsayılan gönderim ayarları.
    |
    */
    'defaults' => [
        'weight_unit' => 'kg',
        'dimension_unit' => 'cm',
        'desi_divider' => 3000,
    ],
];
