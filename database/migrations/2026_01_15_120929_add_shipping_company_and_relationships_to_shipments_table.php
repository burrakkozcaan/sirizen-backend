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
        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('shipping_company_id')->nullable()->after('order_item_id')->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->after('shipping_company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('address_id')->nullable()->after('order_id')->constrained('addresses')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->after('address_id')->constrained()->nullOnDelete();
            $table->string('status')->default('pending')->after('carrier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['shipping_company_id']);
            $table->dropForeign(['order_id']);
            $table->dropForeign(['address_id']);
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['shipping_company_id', 'order_id', 'address_id', 'vendor_id', 'status']);
        });
    }
};
