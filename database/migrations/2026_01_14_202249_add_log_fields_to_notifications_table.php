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
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('shipment_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type')->nullable();
            $table->string('channel')->nullable();
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();

            $table->index(['user_id', 'order_id', 'shipment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'order_id', 'shipment_id']);
            $table->dropConstrainedForeignId('shipment_id');
            $table->dropConstrainedForeignId('order_id');
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn([
                'type',
                'channel',
                'title',
                'message',
                'data',
                'sent_at',
                'read_at',
            ]);
        });
    }
};
