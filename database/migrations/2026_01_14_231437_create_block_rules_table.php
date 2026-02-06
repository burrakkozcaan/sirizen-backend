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
        Schema::create('block_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained('product_blocks')->cascadeOnDelete();
            $table->string('rule_type'); // category | brand | attribute | price | stock | seller
            $table->string('operator'); // = | > | < | >= | <= | in | contains
            $table->text('value'); // JSON for complex values (e.g., ["bot", "ayakkabı"])
            $table->timestamps();
            
            $table->index(['block_id', 'rule_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_rules');
    }
};
