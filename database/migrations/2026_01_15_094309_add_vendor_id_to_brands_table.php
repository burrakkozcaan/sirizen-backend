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
        Schema::table('brands', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('slug')->constrained()->nullOnDelete();
            $table->boolean('is_vendor_brand')->default(false)->after('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['vendor_id', 'is_vendor_brand']);
        });
    }
};
