<?php

/**
 * This script updates all marketplace migration files with proper schemas
 * Run with: php update-migrations.php
 */

$migrations = [
    'vendor_badges' => <<<'PHP'
        Schema::create('vendor_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->string('badge_key'); // fast_shipping, super_seller
            $table->timestamp('earned_at')->useCurrent();
            $table->unique(['vendor_id', 'badge_key']);
        });
PHP,

    'vendor_penalties' => <<<'PHP'
        Schema::create('vendor_penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->text('reason');
            $table->integer('penalty_points');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
PHP,

    'categories' => <<<'PHP'
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->index(['parent_id', 'order']);
        });
PHP,

    'brands' => <<<'PHP'
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->timestamps();
        });
PHP,

    'products' => <<<'PHP'
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['category_id', 'is_active', 'rating']);
            $table->index('slug');
        });
PHP,

    'product_attributes' => <<<'PHP'
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('key'); // SPF, Cilt Tipi
            $table->string('value');
            $table->timestamps();
            $table->index(['product_id', 'key']);
        });
PHP,

    'product_variants' => <<<'PHP'
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();
            $table->index(['product_id', 'stock']);
        });
PHP,

    'product_images' => <<<'PHP'
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->boolean('is_main')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->index(['product_id', 'is_main']);
        });
PHP,

    'product_videos' => <<<'PHP'
        Schema::create('product_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('url');
            $table->string('video_type')->default('youtube');
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->index(['product_id', 'video_type']);
        });
PHP,

    'product_sellers' => <<<'PHP'
        Schema::create('product_sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('dispatch_days')->default(3); // days
            $table->enum('shipping_type', ['normal', 'express', 'same_day'])->default('normal');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->unique(['product_id', 'vendor_id']);
            $table->index(['vendor_id', 'stock']);
        });
PHP,

    'product_reviews' => <<<'PHP'
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->timestamps();
            $table->index(['product_id', 'rating']);
        });
PHP,

    'seller_reviews' => <<<'PHP'
        Schema::create('seller_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('delivery_rating'); // 1-5
            $table->unsignedTinyInteger('communication_rating'); // 1-5
            $table->unsignedTinyInteger('packaging_rating'); // 1-5
            $table->text('comment')->nullable();
            $table->timestamps();
        });
PHP,

    'product_questions' => <<<'PHP'
        Schema::create('product_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->text('answer')->nullable();
            $table->boolean('answered_by_vendor')->default(false);
            $table->timestamps();
            $table->index('product_id');
        });
PHP,

    'favorites' => <<<'PHP'
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['user_id', 'product_id']);
        });
PHP,

    'carts' => <<<'PHP'
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
PHP,

    'cart_items' => <<<'PHP'
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_seller_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->timestamps();
            $table->unique(['cart_id', 'product_seller_id']);
        });
PHP,

    'orders' => <<<'PHP'
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->decimal('total_price', 10, 2);
            $table->string('status'); // pending, confirmed, shipped, delivered, cancelled
            $table->string('payment_method');
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index('order_number');
        });
PHP,

    'order_items' => <<<'PHP'
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('price', 10, 2);
            $table->string('status'); // preparing, shipped, delivered
            $table->timestamps();
            $table->index(['order_id', 'vendor_id']);
        });
PHP,

    'shipments' => <<<'PHP'
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->string('tracking_number')->unique();
            $table->string('carrier');
            $table->timestamp('estimated_delivery')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
PHP,

    'addresses' => <<<'PHP'
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // Home, Office
            $table->string('city');
            $table->string('district');
            $table->text('address_line');
            $table->string('postal_code')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->index(['user_id', 'is_default']);
        });
PHP,

    'campaigns' => <<<'PHP'
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('discount_type'); // percentage, fixed
            $table->decimal('discount_value', 10, 2);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
PHP,

    'product_campaigns' => <<<'PHP'
        Schema::create('product_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->unique(['product_id', 'campaign_id']);
        });
PHP,

    'search_logs' => <<<'PHP'
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('query');
            $table->unsignedInteger('results_count')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->index(['query', 'created_at']);
        });
PHP,

    'recently_vieweds' => <<<'PHP'
        Schema::create('recently_vieweds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamp('viewed_at')->useCurrent();
            $table->unique(['user_id', 'product_id']);
            $table->index(['user_id', 'viewed_at']);
        });
PHP,

    'payments' => <<<'PHP'
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('payment_provider'); // iyzico, stripe, paytr
            $table->string('payment_type'); // card, transfer
            $table->string('status'); // pending, success, failed
            $table->string('transaction_id')->unique()->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index(['order_id', 'status']);
        });
PHP,

    'commissions' => <<<'PHP'
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('net_amount', 10, 2);
            $table->string('status'); // pending, settled
            $table->timestamps();
            $table->index(['vendor_id', 'status']);
        });
PHP,

    'vendor_balances' => <<<'PHP'
        Schema::create('vendor_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->decimal('pending_balance', 10, 2)->default(0.00);
            $table->timestamp('last_settlement_at')->nullable();
            $table->timestamps();
        });
PHP,

    'vendor_payouts' => <<<'PHP'
        Schema::create('vendor_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('payout_method'); // bank_transfer
            $table->string('status'); // waiting, paid
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index(['vendor_id', 'status']);
        });
PHP,

    'refunds' => <<<'PHP'
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->text('reason');
            $table->string('status'); // requested, approved, rejected, processed
            $table->decimal('refund_amount', 10, 2);
            $table->timestamps();
            $table->index(['order_item_id', 'status']);
        });
PHP,

    'product_returns' => <<<'PHP'
        Schema::create('product_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->string('condition_status')->nullable(); // good, damaged
            $table->timestamps();
        });
PHP,

    'vendor_performance_logs' => <<<'PHP'
        Schema::create('vendor_performance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->string('metric'); // late_shipment, cancel
            $table->decimal('value', 10, 2);
            $table->timestamp('logged_at')->useCurrent();
            $table->index(['vendor_id', 'metric', 'logged_at']);
        });
PHP,

    'search_indices' => <<<'PHP'
        Schema::create('search_indices', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // product, vendor
            $table->unsignedBigInteger('entity_id');
            $table->text('searchable_text');
            $table->timestamps();
            $table->index(['entity_type', 'entity_id']);
        });
PHP,

    'product_stats' => <<<'PHP'
        Schema::create('product_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('add_to_cart')->default(0);
            $table->unsignedBigInteger('purchases')->default(0);
            $table->timestamps();
        });
PHP,

    'activity_logs' => <<<'PHP'
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('properties')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['user_id', 'created_at']);
        });
PHP,

    'disputes' => <<<'PHP'
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->text('reason');
            $table->string('status'); // open, investigating, resolved, closed
            $table->timestamps();
            $table->index(['order_item_id', 'status']);
        });
PHP,

    'translations' => <<<'PHP'
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // product, category
            $table->unsignedBigInteger('entity_id');
            $table->string('locale', 5);
            $table->string('field');
            $table->text('value');
            $table->timestamps();
            $table->unique(['entity_type', 'entity_id', 'locale', 'field']);
        });
PHP,
];

echo "Migration schemas defined for " . count($migrations) . " tables.\n";
echo "Manually copy each schema to the corresponding migration file in database/migrations/\n";
echo "\nExample:\n";
echo "For 'brands' table, find the file: *_create_brands_table.php\n";
echo "And replace the Schema::create block with the schema above.\n";
