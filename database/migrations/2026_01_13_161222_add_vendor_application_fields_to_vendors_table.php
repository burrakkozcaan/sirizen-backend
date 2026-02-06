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
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->string('company_type')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('reference_code')->nullable();
            $table->text('address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'company_type',
                'tax_number',
                'city',
                'district',
                'reference_code',
                'address',
            ]);
        });
    }
};
