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
        Schema::create('brand_vendor', function (Blueprint $table) {
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_authorized')->default(true);
            $table->timestamp('authorized_at')->nullable();
            $table->timestamps();

            $table->primary(['brand_id', 'vendor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_vendor');
    }
};
