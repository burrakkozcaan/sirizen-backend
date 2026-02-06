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
        Schema::table('disputes', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_to')->nullable()->after('status');
            $table->text('resolution_notes')->nullable()->after('assigned_to');
            $table->unsignedBigInteger('resolved_by')->nullable()->after('resolution_notes');
            $table->string('resolution_type')->nullable()->after('resolved_by'); // refund, replacement, vendor_penalty, no_action
            $table->json('evidence_files')->nullable()->after('resolution_type'); // array of file paths
            $table->timestamp('assigned_at')->nullable()->after('evidence_files');
            $table->timestamp('resolved_at')->nullable()->after('assigned_at');

            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            $table->foreign('resolved_by')->references('id')->on('users')->nullOnDelete();

            $table->index('assigned_to');
            $table->index('resolved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disputes', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['resolved_by']);

            $table->dropColumn([
                'assigned_to',
                'resolution_notes',
                'resolved_by',
                'resolution_type',
                'evidence_files',
                'assigned_at',
                'resolved_at',
            ]);
        });
    }
};
