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
            $table->foreignId('category_group_id')->nullable()->after('parent_id')->constrained()->nullOnDelete();
            $table->index(['category_group_id', 'is_active']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_group_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
            $table->foreignId('attribute_set_id')->nullable()->after('category_group_id')->constrained()->nullOnDelete();
            $table->index(['category_group_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_group_id']);
            $table->dropForeign(['attribute_set_id']);
            $table->dropColumn(['category_group_id', 'attribute_set_id']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['category_group_id']);
            $table->dropColumn('category_group_id');
        });
    }
};
