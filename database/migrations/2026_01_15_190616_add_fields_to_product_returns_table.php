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
        Schema::table('product_returns', function (Blueprint $table) {
            $table->string('reason')->after('order_item_id');
            $table->text('reason_description')->nullable()->after('reason');
            $table->string('status')->default('pending')->after('reason_description');
            $table->foreignId('user_id')->nullable()->after('status')->constrained()->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->decimal('refund_amount', 10, 2)->nullable()->after('vendor_id');
            $table->timestamp('requested_at')->nullable()->after('refund_amount');
            $table->timestamp('approved_at')->nullable()->after('requested_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_returns', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['vendor_id']);
            $table->dropColumn([
                'reason',
                'reason_description',
                'status',
                'user_id',
                'vendor_id',
                'refund_amount',
                'requested_at',
                'approved_at',
                'rejected_at',
            ]);
        });
    }
};
