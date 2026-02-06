<?php

/**
 * UNIFIED PDP CONFIG
 *
 * Trendyol-style PDP Engine
 * Frontend ASLA karar vermez. Backend bu config'e göre response oluşturur.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Category Groups
    |--------------------------------------------------------------------------
    | Her kategori grubunun key'i. DB'deki category_groups.key ile eşleşmeli.
    */
    'category_groups' => [
        'giyim' => 'giyim',
        'elektronik' => 'elektronik',
        'kozmetik' => 'kozmetik',
        'ev_yasam' => 'ev_yasam',
        'gida' => 'gida',
        'kitap' => 'kitap',
        'spor' => 'spor',
        'default' => 'default',
    ],

    /*
    |--------------------------------------------------------------------------
    | Category Attribute Schemas
    |--------------------------------------------------------------------------
    | Her kategori için hangi attribute'lara izin verildiği.
    | Vendor bu listeye olmayan attribute gönderirse → DROP
    */
    'category_attributes' => [
        'giyim' => [
            'allowed_variant_attributes' => ['beden', 'renk', 'boy'],
            'allowed_highlight_attributes' => ['material', 'kalip', 'yaka_tipi', 'kol_boyu', 'desen'],
            'required_attributes' => ['beden'],
        ],
        'elektronik' => [
            'allowed_variant_attributes' => ['renk', 'kapasite', 'ram', 'depolama'],
            'allowed_highlight_attributes' => ['marka', 'model', 'garanti', 'islemci', 'ekran_boyutu', 'pil_kapasitesi'],
            'required_attributes' => [],
        ],
        'kozmetik' => [
            // ❌ beden YOK - kozmetikte beden saçma
            'allowed_variant_attributes' => ['renk', 'hacim', 'ton'],
            'allowed_highlight_attributes' => ['cilt_tipi', 'icerik', 'kullanim_alani', 'spf', 'formul'],
            'required_attributes' => [],
        ],
        'ev_yasam' => [
            'allowed_variant_attributes' => ['renk', 'boyut', 'malzeme'],
            'allowed_highlight_attributes' => ['olcu', 'agirlik', 'malzeme', 'marka'],
            'required_attributes' => [],
        ],
        'gida' => [
            'allowed_variant_attributes' => ['agirlik', 'adet'],
            'allowed_highlight_attributes' => ['son_kullanma', 'mensei', 'besin_degeri', 'alerjen'],
            'required_attributes' => ['son_kullanma'],
        ],
        'default' => [
            'allowed_variant_attributes' => ['renk', 'boyut'],
            'allowed_highlight_attributes' => ['marka', 'model'],
            'required_attributes' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | PDP Rules (Kategori bazlı davranış kuralları)
    |--------------------------------------------------------------------------
    */
    'category_rules' => [
        'giyim' => [
            'disable_add_until_variant_selected' => true,
            'show_size_guide' => true,
            'show_stock_warning_threshold' => 5,
            'allow_multi_seller' => false,
            'show_quantity_selector' => true,
            'max_quantity' => 10,
            'show_installments' => true,
        ],
        'elektronik' => [
            'disable_add_until_variant_selected' => false,
            'show_size_guide' => false,
            'show_stock_warning_threshold' => 3,
            'allow_multi_seller' => true,  // Elektronik multi-seller
            'show_quantity_selector' => true,
            'max_quantity' => 5,
            'show_installments' => true,
        ],
        'kozmetik' => [
            'disable_add_until_variant_selected' => false,
            'show_size_guide' => false,
            'show_stock_warning_threshold' => 10,
            'allow_multi_seller' => false,
            'show_quantity_selector' => true,
            'max_quantity' => 10,
            'show_installments' => false,
        ],
        'ev_yasam' => [
            'disable_add_until_variant_selected' => false,
            'show_size_guide' => false,
            'show_stock_warning_threshold' => 5,
            'allow_multi_seller' => false,
            'show_quantity_selector' => true,
            'max_quantity' => 20,
            'show_installments' => true,
        ],
        'gida' => [
            'disable_add_until_variant_selected' => false,
            'show_size_guide' => false,
            'show_stock_warning_threshold' => 5,
            'allow_multi_seller' => false,
            'show_quantity_selector' => true,
            'max_quantity' => 50,
            'show_installments' => false,
        ],
        'default' => [
            'disable_add_until_variant_selected' => false,
            'show_size_guide' => false,
            'show_stock_warning_threshold' => 5,
            'allow_multi_seller' => false,
            'show_quantity_selector' => true,
            'max_quantity' => 10,
            'show_installments' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Proof Config (Kategori bazlı)
    |--------------------------------------------------------------------------
    */
    'social_proof' => [
        'giyim' => [
            'show_view_count' => true,
            'show_cart_count' => true,
            'show_sold_count' => true,
            'show_review_count' => true,
        ],
        'elektronik' => [
            'show_view_count' => true,
            'show_cart_count' => true,
            'show_sold_count' => true,
            'show_review_count' => true,
        ],
        'kozmetik' => [
            'show_view_count' => true,
            'show_cart_count' => false,  // Kozmetikte sepet sayısı gösterme
            'show_sold_count' => true,
            'show_review_count' => true,
        ],
        'ev_yasam' => [
            'show_view_count' => false,
            'show_cart_count' => false,
            'show_sold_count' => true,
            'show_review_count' => true,
        ],
        'gida' => [
            'show_view_count' => false,
            'show_cart_count' => false,
            'show_sold_count' => true,
            'show_review_count' => true,
        ],
        'default' => [
            'show_view_count' => false,
            'show_cart_count' => false,
            'show_sold_count' => false,
            'show_review_count' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Block Visibility by Context
    |--------------------------------------------------------------------------
    | Her context için hangi blokların gösterileceği
    */
    'block_visibility' => [
        'page' => [
            'gallery', 'thumbnail_nav', 'zoom',
            'breadcrumbs', 'title', 'rating', 'badges',
            'price', 'installments', 'coupon_input',
            'variant_selector', 'size_guide', 'color_selector',
            'quantity_selector', 'add_to_cart', 'add_to_favorites',
            'seller_info', 'seller_selector', 'shipping_info',
            'campaigns', 'bundles', 'flash_sale_timer',
            'description', 'attributes', 'highlights',
            'social_proof', 'reviews_summary', 'qa_summary',
            'related_products', 'similar_products', 'bought_together',
        ],
        'modal' => [
            'gallery',
            'title',
            'price',
            'badges',
            'variant_selector',
            'seller_selector',
            'campaigns',
            'quantity_selector',
            'add_to_cart',
        ],
        'quickview' => [
            'gallery',
            'title',
            'rating',
            'price',
            'badges',
            'variant_selector',
            'seller_info',
            'description',
            'add_to_cart',
        ],
        'cart' => [
            'title',
            'price',
            'variant_selector',
            'seller_info',
            'add_to_cart',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'pdp_ttl' => 60,           // PDP cache: 60 saniye
        'variant_ttl' => 30,       // Variant cache: 30 saniye
        'social_proof_ttl' => 10,  // Social proof: 10 saniye (dynamic)
        'filters_ttl' => 300,      // Filters cache: 5 dakika
    ],
];
