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
        Schema::table('products', function (Blueprint $table) {
            $table->text('short_description')->nullable()->after('description');
            $table->text('additional_information')->nullable()->after('short_description');
            $table->text('safety_information')->nullable()->after('additional_information');
            $table->string('manufacturer_name')->nullable()->after('safety_information');
            $table->text('manufacturer_address')->nullable()->after('manufacturer_name');
            $table->string('manufacturer_contact')->nullable()->after('manufacturer_address');
            $table->string('responsible_party_name')->nullable()->after('manufacturer_contact');
            $table->text('responsible_party_address')->nullable()->after('responsible_party_name');
            $table->string('responsible_party_contact')->nullable()->after('responsible_party_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'short_description',
                'additional_information',
                'safety_information',
                'manufacturer_name',
                'manufacturer_address',
                'manufacturer_contact',
                'responsible_party_name',
                'responsible_party_address',
                'responsible_party_contact',
            ]);
        });
    }
};
