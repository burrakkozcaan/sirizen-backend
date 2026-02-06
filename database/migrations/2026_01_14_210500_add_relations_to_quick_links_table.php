<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quick_links', function (Blueprint $table) {
            $table->string('category_slug')->nullable()->after('path');
            $table->string('campaign_slug')->nullable()->after('category_slug');
            $table->unsignedBigInteger('product_id')->nullable()->after('campaign_slug');
        });
    }

    public function down(): void
    {
        Schema::table('quick_links', function (Blueprint $table) {
            $table->dropColumn(['category_slug', 'campaign_slug', 'product_id']);
        });
    }
};
