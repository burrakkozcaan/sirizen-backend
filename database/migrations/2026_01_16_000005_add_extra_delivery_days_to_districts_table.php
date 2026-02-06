<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('districts', function (Blueprint $table) {
            if (! Schema::hasColumn('districts', 'extra_delivery_days')) {
                $table->integer('extra_delivery_days')->default(0)->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('districts', function (Blueprint $table) {
            if (Schema::hasColumn('districts', 'extra_delivery_days')) {
                $table->dropColumn('extra_delivery_days');
            }
        });
    }
};
