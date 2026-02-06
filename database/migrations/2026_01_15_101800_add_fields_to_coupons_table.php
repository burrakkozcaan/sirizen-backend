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
        Schema::table('coupons', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete()->after('id');
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete()->after('vendor_id');
            $table->string('code')->unique()->after('product_id');
            $table->string('title')->after('code');
            $table->text('description')->nullable()->after('title');
            $table->string('discount_type')->after('description');
            $table->decimal('discount_value', 10, 2)->after('discount_type');
            $table->decimal('min_order_amount', 10, 2)->nullable()->after('discount_value');
            $table->decimal('max_discount_amount', 10, 2)->nullable()->after('min_order_amount');
            $table->unsignedInteger('usage_limit')->nullable()->after('max_discount_amount');
            $table->unsignedInteger('per_user_limit')->nullable()->after('usage_limit');
            $table->timestamp('starts_at')->nullable()->after('per_user_limit');
            $table->timestamp('expires_at')->nullable()->after('starts_at');
            $table->boolean('is_active')->default(true)->after('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn([
                'code',
                'title',
                'description',
                'discount_type',
                'discount_value',
                'min_order_amount',
                'max_discount_amount',
                'usage_limit',
                'per_user_limit',
                'starts_at',
                'expires_at',
                'is_active',
            ]);
            $table->dropConstrainedForeignId('product_id');
            $table->dropConstrainedForeignId('vendor_id');
        });
    }
};
