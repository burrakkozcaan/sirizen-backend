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
            $table->string('label_url')->nullable()->after('tracking_number');
            $table->string('barcode_url')->nullable()->after('label_url');
            $table->string('cargo_reference_id')->nullable()->index()->after('barcode_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['label_url', 'barcode_url', 'cargo_reference_id']);
        });
    }
};
