#!/bin/bash

# This script contains all migration schemas for the Trendyol-style marketplace
# You can copy the schemas from this file to implement the migrations

cat << 'EOF'

# MIGRATION SCHEMAS FOR TRENDYOL MARKETPLACE
# ==========================================

# Already created:
# - vendor_tiers ✓
# - vendors ✓

# TODO: Copy each schema below to the corresponding migration file

# vendor_scores
$table->id();
$table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
$table->decimal('total_score', 5, 2)->default(0.00);
$table->decimal('delivery_score', 5, 2)->default(0.00);
$table->decimal('rating_score', 5, 2)->default(0.00);
$table->decimal('stock_score', 5, 2)->default(0.00);
$table->decimal('support_score', 5, 2)->default(0.00);
$table->timestamps();
$table->unique('vendor_id');

# vendor_badges
$table->id();
$table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
$table->string('badge_key'); // fast_shipping, super_seller
$table->timestamp('earned_at')->useCurrent();
$table->unique(['vendor_id', 'badge_key']);

# vendor_penalties
$table->id();
$table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
$table->text('reason');
$table->integer('penalty_points');
$table->timestamp('expires_at')->nullable();
$table->timestamps();

# categories
$table->id();
$table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
$table->string('name');
$table->string('slug')->unique();
$table->string('icon')->nullable();
$table->unsignedInteger('order')->default(0);
$table->timestamps();
$table->index(['parent_id', 'order']);

# brands
$table->id();
$table->string('name')->unique();
$table->string('slug')->unique();
$table->string('logo')->nullable();
$table->timestamps();

# products
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

# product_attributes
$table->id();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->string('key'); // SPF, Cilt Tipi
$table->string('value');
$table->timestamps();
$table->index(['product_id', 'key']);

# product_variants
$table->id();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->string('sku')->unique();
$table->string('color')->nullable();
$table->string('size')->nullable();
$table->unsignedInteger('stock')->default(0);
$table->timestamps();
$table->index(['product_id', 'stock']);

# product_images
$table->id();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->string('url');
$table->boolean('is_main')->default(false);
$table->unsignedInteger('order')->default(0);
$table->timestamps();
$table->index(['product_id', 'is_main']);

# product_videos
$table->id();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->string('title')->nullable();
$table->string('url');
$table->string('video_type')->default('youtube');
$table->unsignedInteger('order')->default(0);
$table->boolean('is_featured')->default(false);
$table->timestamps();
$table->index(['product_id', 'video_type']);

# product_sellers (MULTI-VENDOR PIVOT)
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

# product_reviews
$table->id();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->unsignedTinyInteger('rating'); // 1-5
$table->text('comment')->nullable();
$table->boolean('is_verified_purchase')->default(false);
$table->timestamps();
$table->index(['product_id', 'rating']);

# seller_reviews
$table->id();
$table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->unsignedTinyInteger('delivery_rating'); // 1-5
$table->unsignedTinyInteger('communication_rating'); // 1-5
$table->unsignedTinyInteger('packaging_rating'); // 1-5
$table->text('comment')->nullable();
$table->timestamps();

# product_questions
$table->id();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->text('question');
$table->text('answer')->nullable();
$table->boolean('answered_by_vendor')->default(false);
$table->timestamps();
$table->index('product_id');

# favorites
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->timestamp('created_at')->useCurrent();
$table->unique(['user_id', 'product_id']);

# carts
$table->id();
$table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
$table->timestamps();

# cart_items
$table->id();
$table->foreignId('cart_id')->constrained()->cascadeOnDelete();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->foreignId('product_seller_id')->constrained()->cascadeOnDelete();
$table->unsignedInteger('quantity')->default(1);
$table->decimal('price', 10, 2);
$table->timestamps();
$table->unique(['cart_id', 'product_seller_id']);

# orders
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->string('order_number')->unique();
$table->decimal('total_price', 10, 2);
$table->string('status'); // pending, confirmed, shipped, delivered, cancelled
$table->string('payment_method');
$table->timestamps();
$table->index(['user_id', 'status']);
$table->index('order_number');

# order_items
$table->id();
$table->foreignId('order_id')->constrained()->cascadeOnDelete();
$table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->unsignedInteger('quantity');
$table->decimal('price', 10, 2);
$table->string('status'); // preparing, shipped, delivered
$table->timestamps();
$table->index(['order_id', 'vendor_id']);

# shipments
$table->id();
$table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
$table->string('tracking_number')->unique();
$table->string('carrier');
$table->timestamp('estimated_delivery')->nullable();
$table->timestamp('shipped_at')->nullable();
$table->timestamp('delivered_at')->nullable();
$table->timestamps();

# addresses
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

# campaigns
$table->id();
$table->string('title');
$table->string('discount_type'); // percentage, fixed
$table->decimal('discount_value', 10, 2);
$table->timestamp('starts_at');
$table->timestamp('ends_at');
$table->boolean('is_active')->default(true);
$table->timestamps();

# product_campaigns
$table->id();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
$table->unique(['product_id', 'campaign_id']);

# search_logs
$table->id();
$table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
$table->string('query');
$table->unsignedInteger('results_count')->default(0);
$table->timestamp('created_at')->useCurrent();
$table->index(['query', 'created_at']);

# recently_vieweds
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->foreignId('product_id')->constrained()->cascadeOnDelete();
$table->timestamp('viewed_at')->useCurrent();
$table->unique(['user_id', 'product_id']);
$table->index(['user_id', 'viewed_at']);

# PAYMENT & FINANCIAL SYSTEM

# payments
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

# commissions
$table->id();
$table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
$table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
$table->decimal('commission_rate', 5, 2);
$table->decimal('commission_amount', 10, 2);
$table->decimal('net_amount', 10, 2);
$table->string('status'); // pending, settled
$table->timestamps();
$table->index(['vendor_id', 'status']);

# vendor_balances
$table->id();
$table->foreignId('vendor_id')->unique()->constrained()->cascadeOnDelete();
$table->decimal('balance', 10, 2)->default(0.00);
$table->decimal('pending_balance', 10, 2)->default(0.00);
$table->timestamp('last_settlement_at')->nullable();
$table->timestamps();

# vendor_payouts
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

# refunds
$table->id();
$table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
$table->text('reason');
$table->string('status'); // requested, approved, rejected, processed
$table->decimal('refund_amount', 10, 2);
$table->timestamps();
$table->index(['order_item_id', 'status']);

# product_returns
$table->id();
$table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
$table->string('tracking_number')->nullable();
$table->string('carrier')->nullable();
$table->timestamp('received_at')->nullable();
$table->string('condition_status')->nullable(); // good, damaged
$table->timestamps();

# vendor_performance_logs
$table->id();
$table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
$table->string('metric'); // late_shipment, cancel
$table->decimal('value', 10, 2);
$table->timestamp('logged_at')->useCurrent();
$table->index(['vendor_id', 'metric', 'logged_at']);

# search_indices
$table->id();
$table->string('entity_type'); // product, vendor
$table->unsignedBigInteger('entity_id');
$table->text('searchable_text');
$table->timestamps();
$table->index(['entity_type', 'entity_id']);

# product_stats
$table->id();
$table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
$table->unsignedBigInteger('views')->default(0);
$table->unsignedBigInteger('add_to_cart')->default(0);
$table->unsignedBigInteger('purchases')->default(0);
$table->timestamps();

# activity_logs
$table->id();
$table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
$table->string('action');
$table->string('ip_address', 45)->nullable();
$table->text('user_agent')->nullable();
$table->json('properties')->nullable();
$table->timestamp('created_at')->useCurrent();
$table->index(['user_id', 'created_at']);

# disputes
$table->id();
$table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
$table->text('reason');
$table->string('status'); // open, investigating, resolved, closed
$table->timestamps();
$table->index(['order_item_id', 'status']);

# translations
$table->id();
$table->string('entity_type'); // product, category
$table->unsignedBigInteger('entity_id');
$table->string('locale', 5);
$table->string('field');
$table->text('value');
$table->timestamps();
$table->unique(['entity_type', 'entity_id', 'locale', 'field']);

EOF
