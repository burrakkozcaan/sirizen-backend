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
        // 1️⃣ SATICI MESAJLARI (Messaging System)
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->string('subject');
            $table->string('status')->default('open'); // open | closed | resolved
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['vendor_id', 'status']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->string('sender_type'); // user | vendor | admin
            $table->unsignedBigInteger('sender_id');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });

        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');
            $table->timestamps();
        });

        // 2️⃣ KAYITLI KARTLAR (Payment Methods)
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('card_holder_name');
            $table->string('card_last_four', 4);
            $table->string('card_brand'); // visa | mastercard | amex
            $table->string('card_token')->unique(); // Şifrelenmiş token
            $table->string('expiry_month', 2);
            $table->string('expiry_year', 4);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });

        // 3️⃣ BİLDİRİM TERCİHLERİ (Notification Preferences)
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');

            // Email tercihleri
            $table->boolean('email_campaigns')->default(true);
            $table->boolean('email_orders')->default(true);
            $table->boolean('email_promotions')->default(true);
            $table->boolean('email_reviews')->default(true);

            // SMS tercihleri
            $table->boolean('sms_campaigns')->default(false);
            $table->boolean('sms_orders')->default(true);
            $table->boolean('sms_promotions')->default(false);

            // Push bildirimleri
            $table->boolean('push_enabled')->default(true);
            $table->boolean('push_campaigns')->default(true);
            $table->boolean('push_orders')->default(true);
            $table->boolean('push_messages')->default(true);

            $table->timestamps();
        });

        // 4️⃣ AKTİF OTURUMLAR (Active Sessions)
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_token')->unique();
            $table->ipAddress('ip_address');
            $table->text('user_agent');
            $table->string('device_type')->nullable(); // mobile | desktop | tablet
            $table->string('device_name')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->timestamp('last_activity');
            $table->boolean('is_current')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_current']);
            $table->index(['user_id', 'last_activity']);
        });

        // 5️⃣ ÜYELİK PROGRAMLARI (Loyalty/Membership - Trendyol Plus)
        Schema::create('membership_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Plus, Gold, VIP
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->json('benefits')->nullable(); // [{"key": "free_shipping", "value": true}]
            $table->string('badge_icon')->nullable();
            $table->string('badge_color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('user_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained('membership_programs')->onDelete('cascade');
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->string('status')->default('active'); // active | expired | cancelled | paused
            $table->boolean('auto_renew')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // 6️⃣ WALLET / KREDİLER (User Wallet & Transactions)
        Schema::create('user_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->decimal('balance', 12, 2)->default(0);
            $table->string('currency', 3)->default('TRY');
            $table->timestamps();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('user_wallets')->onDelete('cascade');
            $table->string('type'); // credit | debit | refund | cashback | bonus
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_after', 12, 2);
            $table->text('description');
            $table->string('reference_type')->nullable(); // order | refund | campaign
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();

            $table->index(['wallet_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });

        // 7️⃣ ŞANSLI ÇEKİLİŞ / GAMİFİCATİON (Raffle/Campaigns)
        Schema::create('raffles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('prize_title');
            $table->text('prize_description')->nullable();
            $table->string('prize_image')->nullable();
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->integer('max_entries_per_user')->default(1);
            $table->integer('total_winners')->default(1);
            $table->string('status')->default('upcoming'); // upcoming | active | ended | winners_selected
            $table->json('rules')->nullable();
            $table->timestamps();

            $table->index(['status', 'start_date', 'end_date']);
        });

        Schema::create('raffle_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('entry_count')->default(1);
            $table->string('entry_code')->unique();
            $table->boolean('is_winner')->default(false);
            $table->timestamps();

            $table->unique(['raffle_id', 'user_id']);
            $table->index(['raffle_id', 'is_winner']);
        });

        Schema::create('raffle_winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->onDelete('cascade');
            $table->foreignId('entry_id')->constrained('raffle_entries')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('prize_rank'); // 1 = first prize, 2 = second, etc.
            $table->timestamp('announced_at')->nullable();
            $table->boolean('prize_claimed')->default(false);
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
        });

        // 8️⃣ TEKRAR SATIN AL (Quick Reorder)
        Schema::create('quick_reorders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            $table->timestamp('last_ordered_at');
            $table->integer('order_count')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'product_id', 'variant_id']);
            $table->index(['user_id', 'last_ordered_at']);
        });

        // 9️⃣ TRENDYOL ASİSTAN / CHATBOT (AI Assistant)
        Schema::create('assistant_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->string('status')->default('active'); // active | resolved | closed
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('assistant_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('assistant_conversations')->onDelete('cascade');
            $table->string('role'); // user | assistant
            $table->text('message');
            $table->json('metadata')->nullable(); // suggested products, links, etc.
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistant_messages');
        Schema::dropIfExists('assistant_conversations');
        Schema::dropIfExists('quick_reorders');
        Schema::dropIfExists('raffle_winners');
        Schema::dropIfExists('raffle_entries');
        Schema::dropIfExists('raffles');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('user_wallets');
        Schema::dropIfExists('user_memberships');
        Schema::dropIfExists('membership_programs');
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('message_attachments');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }
};
