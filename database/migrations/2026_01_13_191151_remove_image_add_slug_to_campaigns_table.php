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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->string('slug')->nullable()->after('title');
        });

        // Update existing records with slugs
        \DB::table('campaigns')->update(['slug' => \DB::raw("LOWER(REPLACE(title, ' ', '-'))")]);

        // Make slug unique and not null
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->string('image')->nullable()->after('title');
        });
    }
};
