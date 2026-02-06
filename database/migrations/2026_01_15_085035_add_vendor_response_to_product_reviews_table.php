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
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
            $table->text('vendor_response')->nullable()->after('comment');
            $table->timestamp('vendor_response_at')->nullable()->after('vendor_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropColumn(['vendor_response', 'vendor_response_at']);
            $table->dropConstrainedForeignId('vendor_id');
        });
    }
};
