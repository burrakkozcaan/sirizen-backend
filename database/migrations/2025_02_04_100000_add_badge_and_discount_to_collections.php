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
        Schema::table('collections', function (Blueprint $table) {
            $table->string('badge')->nullable()->after('cta'); // "Yeni Koleksiyon", "Kampanya", "Özel Fırsat"
            $table->string('discount_text')->nullable()->after('badge'); // "%50'ye varan indirim"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn(['badge', 'discount_text']);
        });
    }
};
