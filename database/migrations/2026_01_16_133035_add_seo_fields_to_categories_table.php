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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('description');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->text('header_text')->nullable()->after('meta_keywords');
            $table->text('footer_text')->nullable()->after('header_text');
            $table->string('canonical_url')->nullable()->after('footer_text');

            $table->index('canonical_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'meta_keywords',
                'header_text',
                'footer_text',
                'canonical_url',
            ]);
        });
    }
};
