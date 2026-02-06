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
        Schema::table('price_alerts', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('target_price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamp('notified_at')->nullable();
            $table->index(['user_id', 'product_id']);
        });

        Schema::table('stock_alerts', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamp('notified_at')->nullable();
            $table->index(['user_id', 'product_id']);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->text('content');
            $table->string('cover_image')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
        });

        Schema::table('search_histories', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('query');
            $table->unsignedInteger('results_count')->default(0);
            $table->timestamp('searched_at')->useCurrent();
            $table->index(['user_id', 'searched_at']);
        });

        Schema::table('review_images', function (Blueprint $table) {
            $table->foreignId('product_review_id')->constrained('product_reviews')->cascadeOnDelete();
            $table->string('image_path');
            $table->string('alt_text')->nullable();
            $table->integer('sort_order')->default(0);
        });

        Schema::table('static_pages', function (Blueprint $table) {
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('replied_at')->nullable();
        });

        Schema::dropIfExists('notification_settings');
        Schema::dropIfExists('vendor_badges');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('vendor_badges', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn([
                'name',
                'email',
                'phone',
                'subject',
                'message',
                'is_read',
                'replied_at',
            ]);
        });

        Schema::table('static_pages', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'slug',
                'content',
                'is_active',
                'meta_title',
                'meta_description',
            ]);
        });

        Schema::table('review_images', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_review_id');
            $table->dropColumn([
                'image_path',
                'alt_text',
                'sort_order',
            ]);
        });

        Schema::table('search_histories', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'searched_at']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn([
                'query',
                'results_count',
                'searched_at',
            ]);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn([
                'title',
                'slug',
                'excerpt',
                'content',
                'cover_image',
                'is_published',
                'published_at',
                'meta_title',
                'meta_description',
            ]);
        });

        Schema::table('stock_alerts', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'product_id']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn([
                'is_active',
                'notified_at',
            ]);
        });

        Schema::table('price_alerts', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'product_id']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn([
                'target_price',
                'is_active',
                'notified_at',
            ]);
        });
    }
};
