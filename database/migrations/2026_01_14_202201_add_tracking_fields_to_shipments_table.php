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
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('tracking_url')->nullable();
            $table->string('current_location')->nullable();
            $table->unsignedSmallInteger('progress_percent')->default(0);
            $table->boolean('notify_on_status_change')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'tracking_url',
                'current_location',
                'progress_percent',
                'notify_on_status_change',
            ]);
        });
    }
};
