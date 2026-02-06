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
        Schema::table('vendor_tiers', function (Blueprint $table) {
            if (! Schema::hasColumn('vendor_tiers', 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)
                    ->nullable()
                    ->after('max_return_rate');
            }

            if (! Schema::hasColumn('vendor_tiers', 'max_products')) {
                $table->unsignedInteger('max_products')
                    ->nullable()
                    ->after('commission_rate');
            }

            if (! Schema::hasColumn('vendor_tiers', 'description')) {
                $table->text('description')
                    ->nullable()
                    ->after('max_products');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_tiers', function (Blueprint $table) {
            $columns = ['commission_rate', 'max_products', 'description'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('vendor_tiers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
