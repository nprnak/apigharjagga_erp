<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kyc_verifications', function (Blueprint $table) {
            // Annex F personal details collected during KYC
            $table->string('full_name', 150)->nullable()->after('user_id');
            $table->string('father_mother_name', 150)->nullable()->after('full_name');
            $table->string('spouse_name', 150)->nullable()->after('father_mother_name');
            $table->string('citizenship_no', 50)->nullable()->after('spouse_name');
            $table->date('date_of_birth')->nullable()->after('citizenship_no');
            $table->string('gender', 20)->nullable()->after('date_of_birth');
            $table->string('nationality', 50)->nullable()->default('Nepali')->after('gender');
            $table->string('occupation', 100)->nullable()->after('nationality');
            $table->string('mobile_no', 20)->nullable()->after('occupation');
            $table->string('email', 150)->nullable()->after('mobile_no');

            // Permanent address
            $table->string('permanent_province', 100)->nullable()->after('email');
            $table->string('permanent_district', 100)->nullable()->after('permanent_province');
            $table->string('permanent_municipality', 100)->nullable()->after('permanent_district');
            $table->string('permanent_ward_no', 10)->nullable()->after('permanent_municipality');
            $table->string('permanent_tole', 100)->nullable()->after('permanent_ward_no');

            // Current / temporary address
            $table->string('current_province', 100)->nullable()->after('permanent_tole');
            $table->string('current_district', 100)->nullable()->after('current_province');
            $table->string('current_municipality', 100)->nullable()->after('current_district');
            $table->string('current_ward_no', 10)->nullable()->after('current_municipality');
            $table->string('current_tole', 100)->nullable()->after('current_ward_no');

            // Selfie / face photo
            $table->string('selfie_photo_path')->nullable()->after('id_document_path');
        });
    }

    public function down(): void
    {
        Schema::table('kyc_verifications', function (Blueprint $table) {
            $table->dropColumn([
                'full_name', 'father_mother_name', 'spouse_name',
                'citizenship_no', 'date_of_birth', 'gender',
                'nationality', 'occupation', 'mobile_no', 'email',
                'permanent_province', 'permanent_district', 'permanent_municipality',
                'permanent_ward_no', 'permanent_tole',
                'current_province', 'current_district', 'current_municipality',
                'current_ward_no', 'current_tole',
                'selfie_photo_path',
            ]);
        });
    }
};
