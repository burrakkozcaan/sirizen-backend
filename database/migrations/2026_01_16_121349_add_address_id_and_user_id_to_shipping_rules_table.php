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
        Schema::table('shipping_rules', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('vendor_id')->constrained()->nullOnDelete();
            $table->foreignId('address_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            // Make vendor_id nullable for general rules
            $table->foreignId('vendor_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_rules', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['address_id']);
            $table->dropColumn(['user_id', 'address_id']);
            $table->foreignId('vendor_id')->nullable(false)->change();
        });
    }
};
