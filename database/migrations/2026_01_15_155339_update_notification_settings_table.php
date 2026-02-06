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
        if (! Schema::hasTable('notification_settings')) {
            Schema::create('notification_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->boolean('email_campaigns')->default(true);
                $table->boolean('email_orders')->default(true);
                $table->boolean('email_promotions')->default(true);
                $table->boolean('email_reviews')->default(true);
                $table->boolean('sms_campaigns')->default(false);
                $table->boolean('sms_orders')->default(true);
                $table->boolean('sms_promotions')->default(false);
                $table->boolean('push_enabled')->default(true);
                $table->boolean('push_campaigns')->default(true);
                $table->boolean('push_orders')->default(true);
                $table->boolean('push_messages')->default(true);
                $table->timestamps();

                $table->unique('user_id');
            });

            return;
        }

        Schema::table('notification_settings', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('email_campaigns')->default(true);
            $table->boolean('email_orders')->default(true);
            $table->boolean('email_promotions')->default(true);
            $table->boolean('email_reviews')->default(true);
            $table->boolean('sms_campaigns')->default(false);
            $table->boolean('sms_orders')->default(true);
            $table->boolean('sms_promotions')->default(false);
            $table->boolean('push_enabled')->default(true);
            $table->boolean('push_campaigns')->default(true);
            $table->boolean('push_orders')->default(true);
            $table->boolean('push_messages')->default(true);

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('notification_settings')) {
            return;
        }

        Schema::table('notification_settings', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
            $table->dropColumn([
                'push_messages',
                'push_orders',
                'push_campaigns',
                'push_enabled',
                'sms_promotions',
                'sms_orders',
                'sms_campaigns',
                'email_reviews',
                'email_promotions',
                'email_orders',
                'email_campaigns',
            ]);
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
