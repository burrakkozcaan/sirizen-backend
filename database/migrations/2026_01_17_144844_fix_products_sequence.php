<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('SELECT setval(pg_get_serial_sequence(\'products\', \'id\'), COALESCE((SELECT MAX(id) FROM products), 1), true);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this operation
    }
};
