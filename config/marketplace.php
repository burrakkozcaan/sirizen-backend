<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Marketplace Commission Settings
    |--------------------------------------------------------------------------
    |
    | Trendyol tarzı komisyon sistemi ayarları
    |
    */

    // Varsayılan komisyon oranı (%)
    'default_commission_rate' => env('MARKETPLACE_DEFAULT_COMMISSION_RATE', 10.0),

    // KDV oranı (%)
    'vat_rate' => env('MARKETPLACE_VAT_RATE', 0.20),

    // Minimum komisyon tutarı (TL)
    'min_commission_amount' => env('MARKETPLACE_MIN_COMMISSION_AMOUNT', 1.0),

    /*
    |--------------------------------------------------------------------------
    | Kategori Bazlı Komisyon Oranları
    |--------------------------------------------------------------------------
    |
    | Her kategori için farklı komisyon oranları belirlenebilir.
    | Veritabanında categories.commission_rate kolonunda saklanır.
    |
    | Örnek:
    | - Elektronik: %8
    | - Giyim: %12
    | - Kozmetik: %15
    | - Gıda: %10
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Vendor Tier Komisyon İndirimleri
    |--------------------------------------------------------------------------
    |
    | Premium satıcılar için komisyon indirimleri.
    | vendor_tiers.commission_rate kolonunda saklanır.
    |
    | Örnek:
    | - Standart: 0% indirim
    | - Premium: -2% indirim (kategori komisyonundan 2% düşer)
    | - Elite: -3% indirim
    |
    */
];
