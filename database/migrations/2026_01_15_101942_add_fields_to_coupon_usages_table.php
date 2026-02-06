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
        Schema::table('coupon_usages', function (Blueprint $table) {
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete()->after('id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->after('coupon_id');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('order_id');
            $table->timestamp('used_at')->nullable()->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupon_usages', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'used_at']);
            $table->dropConstrainedForeignId('order_id');
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('coupon_id');
        });
    }
};
