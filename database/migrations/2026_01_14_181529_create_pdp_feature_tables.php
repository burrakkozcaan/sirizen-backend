<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1️⃣ TAKİP ET – KAZAN (Follow & Reward)
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('target_type'); // seller | brand
            $table->unsignedBigInteger('target_id');
            $table->string('reward_type')->nullable(); // coupon | discount
            $table->decimal('reward_value', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'target_type', 'target_id']);
            $table->unique(['user_id', 'target_type', 'target_id']);
        });

        Schema::create('follow_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follow_id')->constrained()->onDelete('cascade');
            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });

        // 2️⃣ SATICI ROZETLERİ
        Schema::create('seller_badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('seller_badge_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('badge_id')->constrained('seller_badges')->onDelete('cascade');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['vendor_id', 'badge_id']);
        });

        // 3️⃣ BİRLİKTE AL (Product Bundles)
        Schema::create('product_bundles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_product_id')->constrained('products')->onDelete('cascade');
            $table->string('title');
            $table->string('bundle_type')->default('together'); // together | suggested | frequently_bought
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('main_product_id');
        });

        Schema::create('product_bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained('product_bundles')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['bundle_id', 'product_id']);
        });

        // 4️⃣ CANLI İSTATİSTİKLER (Live Stats)
        Schema::create('product_live_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('view_count')->default(0);
            $table->integer('cart_count')->default(0);
            $table->integer('purchase_count')->default(0);
            $table->integer('view_count_24h')->default(0);
            $table->timestamps();

            $table->unique('product_id');
        });

        // 5️⃣ KULLANICI KUPONLARI (User Coupons)
        Schema::create('user_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('active'); // active | used | expired
            $table->timestamp('added_at')->useCurrent();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // 6️⃣ TESLİMAT KURALLARI (Shipping Rules)
        Schema::create('shipping_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->time('cutoff_time')->nullable(); // Saat kaçtan önce sipariş = bugün kargoda
            $table->boolean('same_day_shipping')->default(false);
            $table->boolean('free_shipping')->default(false);
            $table->decimal('free_shipping_min_amount', 10, 2)->nullable();
            $table->timestamps();

            $table->index('vendor_id');
        });

        // 7️⃣ ÜRÜN GARANTİLERİ
        Schema::create('product_guarantees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('type'); // original | warranty | refund
            $table->text('description');
            $table->timestamps();

            $table->index(['product_id', 'type']);
        });

        // 8️⃣ İADE POLİTİKALARI
        Schema::create('return_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->integer('days')->default(15);
            $table->boolean('is_free')->default(true);
            $table->text('conditions')->nullable();
            $table->timestamps();

            $table->index('vendor_id');
        });

        // 9️⃣ SORU OYL AMA (Question Votes)
        Schema::create('question_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('product_questions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_helpful')->default(true);
            $table->timestamps();

            $table->unique(['question_id', 'user_id']);
        });

        // 1️⃣1️⃣ BENZER ÜRÜNLER
        Schema::create('similar_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('similar_product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('score', 5, 2)->default(0);
            $table->string('relation_type')->default('similar'); // similar | alternative | cross_sell | up_sell
            $table->timestamps();

            $table->index(['product_id', 'relation_type']);
            $table->unique(['product_id', 'similar_product_id']);
        });

        // 1️⃣2️⃣ SATICI SAYFALARI
        Schema::create('seller_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('seo_slug')->unique();
            $table->text('description')->nullable();
            $table->string('banner')->nullable();
            $table->timestamps();

            $table->index('seo_slug');
        });

        // FİYAT GEÇMİŞİ
        Schema::create('price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['variant_id', 'created_at']);
        });

        // KAMPANYA AUDIT LOGS
        Schema::create('campaign_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // price_change | discount_applied | fake_discount_flagged
            $table->decimal('old_value', 10, 2)->nullable();
            $table->decimal('new_value', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'created_at']);
        });

        // ÜRÜN FAQ
        Schema::create('product_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->text('question');
            $table->text('answer');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'is_active']);
            $table->index(['category_id', 'is_active']);
        });

        // İLİŞKİLİ ÜRÜNLER (Related Products)
        Schema::create('related_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('related_product_id')->constrained('products')->onDelete('cascade');
            $table->string('type')->default('cross'); // cross | up | also_bought
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'type']);
            $table->unique(['product_id', 'related_product_id', 'type']);
        });

        // VARYANT KAMPANYALARI
        Schema::create('variant_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['variant_id', 'campaign_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_campaigns');
        Schema::dropIfExists('related_products');
        Schema::dropIfExists('product_faqs');
        Schema::dropIfExists('campaign_audit_logs');
        Schema::dropIfExists('price_history');
        Schema::dropIfExists('seller_pages');
        Schema::dropIfExists('similar_products');
        Schema::dropIfExists('question_votes');
        Schema::dropIfExists('return_policies');
        Schema::dropIfExists('product_guarantees');
        Schema::dropIfExists('shipping_rules');
        Schema::dropIfExists('user_coupons');
        Schema::dropIfExists('product_live_stats');
        Schema::dropIfExists('product_bundle_items');
        Schema::dropIfExists('product_bundles');
        Schema::dropIfExists('seller_badge_assignments');
        Schema::dropIfExists('seller_badges');
        Schema::dropIfExists('follow_rewards');
        Schema::dropIfExists('follows');
    }
};
