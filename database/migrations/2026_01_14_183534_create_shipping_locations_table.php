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
        // İL / İLÇE (Türkiye için location database)
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->integer('plate_code')->unique();
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->timestamps();

            $table->index(['city_id', 'slug']);
        });

        // KARGO HESAPLAMA İÇİN
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Anadolu, Marmara, Akdeniz
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('shipping_zone_cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained('shipping_zones')->onDelete('cascade');
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['zone_id', 'city_id']);
        });

        // KARGO FİYATLANDIRMA
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('cascade');
            $table->foreignId('zone_id')->constrained('shipping_zones')->onDelete('cascade');
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('per_kg_price', 10, 2)->default(0);
            $table->integer('estimated_days_min')->default(2);
            $table->integer('estimated_days_max')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['vendor_id', 'zone_id', 'is_active']);
        });

        // KARGO TESLİMAT TAHMİNİ (cache için)
        Schema::create('delivery_estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->foreignId('district_id')->nullable()->constrained('districts')->onDelete('cascade');
            $table->date('estimated_delivery_date');
            $table->integer('business_days');
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();

            $table->index(['product_id', 'city_id', 'district_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_estimates');
        Schema::dropIfExists('shipping_rates');
        Schema::dropIfExists('shipping_zone_cities');
        Schema::dropIfExists('shipping_zones');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('cities');
    }
};
