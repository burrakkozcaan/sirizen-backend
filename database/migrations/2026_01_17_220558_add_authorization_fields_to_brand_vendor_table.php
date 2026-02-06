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
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('brand_vendor', function (Blueprint $table) {
                $table->string('authorization_type')->default('authorized_dealer')->after('vendor_id');
                $table->string('authorization_document')->nullable()->after('authorization_type');
                $table->string('invoice_document')->nullable()->after('authorization_document');
                $table->date('valid_from')->nullable()->after('invoice_document');
                $table->date('valid_until')->nullable()->after('valid_from');
                $table->string('status')->default('pending')->after('valid_until');
            });

            return;
        }

        Schema::table('brand_vendor', function (Blueprint $table) {
            $table->dropPrimary(['brand_id', 'vendor_id']);
        });

        Schema::table('brand_vendor', function (Blueprint $table) {
            $table->id()->first();
            $table->string('authorization_type')->default('authorized_dealer')->after('vendor_id');
            $table->string('authorization_document')->nullable()->after('authorization_type');
            $table->string('invoice_document')->nullable()->after('authorization_document');
            $table->date('valid_from')->nullable()->after('invoice_document');
            $table->date('valid_until')->nullable()->after('valid_from');
            $table->string('status')->default('pending')->after('valid_until');
            $table->unique(['brand_id', 'vendor_id'], 'brand_vendor_unique');
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('brand_vendor', function (Blueprint $table) {
                $table->dropColumn([
                    'authorization_type',
                    'authorization_document',
                    'invoice_document',
                    'valid_from',
                    'valid_until',
                    'status',
                ]);
            });

            return;
        }

        Schema::table('brand_vendor', function (Blueprint $table) {
            $table->dropUnique('brand_vendor_unique');
            $table->primary(['brand_id', 'vendor_id']);
            $table->dropColumn([
                'id',
                'authorization_type',
                'authorization_document',
                'invoice_document',
                'valid_from',
                'valid_until',
                'status',
            ]);
        });
    }
};
