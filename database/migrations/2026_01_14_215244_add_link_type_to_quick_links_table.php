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
        Schema::table('quick_links', function (Blueprint $table) {
            $table->string('link_type')->default('category')->after('icon');
            // Make path nullable since it will be auto-generated
            $table->string('path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quick_links', function (Blueprint $table) {
            $table->dropColumn('link_type');
            $table->string('path')->nullable(false)->change();
        });
    }
};
