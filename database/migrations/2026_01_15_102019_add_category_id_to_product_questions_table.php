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
        Schema::table('product_questions', function (Blueprint $table) {
            $table->foreignId('product_question_category_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_questions', function (Blueprint $table) {
            $table->dropForeign(['product_question_category_id']);
            $table->dropColumn('product_question_category_id');
        });
    }
};
