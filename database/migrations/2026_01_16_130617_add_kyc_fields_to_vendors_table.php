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
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('kyc_status')->default('pending')->after('status'); // pending, under_review, verified, rejected
            $table->timestamp('kyc_verified_at')->nullable()->after('kyc_status');
            $table->text('kyc_notes')->nullable()->after('kyc_verified_at');
            $table->unsignedBigInteger('kyc_verified_by')->nullable()->after('kyc_notes');

            $table->string('application_status')->default('pending')->after('kyc_verified_by'); // pending, under_review, approved, rejected
            $table->timestamp('application_submitted_at')->nullable()->after('application_status');
            $table->timestamp('application_reviewed_at')->nullable()->after('application_submitted_at');
            $table->unsignedBigInteger('application_reviewed_by')->nullable()->after('application_reviewed_at');
            $table->text('rejection_reason')->nullable()->after('application_reviewed_by');

            $table->string('business_license_number')->nullable()->after('tax_number');
            $table->string('iban')->nullable()->after('business_license_number');
            $table->string('bank_name')->nullable()->after('iban');
            $table->string('account_holder_name')->nullable()->after('bank_name');

            $table->foreign('kyc_verified_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('application_reviewed_by')->references('id')->on('users')->nullOnDelete();

            $table->index('kyc_status');
            $table->index('application_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign(['kyc_verified_by']);
            $table->dropForeign(['application_reviewed_by']);

            $table->dropColumn([
                'kyc_status',
                'kyc_verified_at',
                'kyc_notes',
                'kyc_verified_by',
                'application_status',
                'application_submitted_at',
                'application_reviewed_at',
                'application_reviewed_by',
                'rejection_reason',
                'business_license_number',
                'iban',
                'bank_name',
                'account_holder_name',
            ]);
        });
    }
};
