<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Brings kyc_verifications up to full Annex-F field parity (it was
     * missing grandfather_name, alternate/telephone contacts, and an
     * applicant signature) and splits the single-stage pending/approved/
     * rejected status into a two-person pending -> verified -> approved
     * workflow, mirroring the Power of Attorney and Valuation Report
     * review patterns already used elsewhere in this app.
     */
    public function up(): void
    {
        Schema::table('kyc_verifications', function (Blueprint $table) {
            $table->string('grandfather_name', 150)->nullable()->after('spouse_name');
            $table->string('alt_contact_no', 20)->nullable()->after('mobile_no');
            $table->string('telephone_no', 20)->nullable()->after('alt_contact_no');

            $table->string('signature_path', 255)->nullable()->after('selfie_photo_path');
            $table->timestamp('signature_date')->nullable()->after('signature_path');

            $table->unsignedBigInteger('verified_by_staff_id')->nullable()->after('status');
            $table->timestamp('verified_at')->nullable()->after('verified_by_staff_id');
            $table->unsignedBigInteger('approved_by_staff_id')->nullable()->after('verified_at');
            $table->timestamp('approved_at')->nullable()->after('approved_by_staff_id');

            $table->foreign('verified_by_staff_id')->references('staff_id')->on('staff');
            $table->foreign('approved_by_staff_id')->references('staff_id')->on('staff');
        });

        DB::statement("ALTER TABLE `kyc_verifications` MODIFY `status` ENUM('pending', 'verified', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");

        Schema::create('kyc_verification_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kyc_verification_id');
            $table->unsignedSmallInteger('doc_type_id');
            $table->string('file_ref', 255)->nullable();
            $table->enum('status', ['pending', 'submitted'])->default('pending');
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('kyc_verification_id')->references('id')->on('kyc_verifications')->cascadeOnDelete();
            $table->foreign('doc_type_id')->references('doc_type_id')->on('document_types');
            $table->unique(['kyc_verification_id', 'doc_type_id'], 'kyc_docs_kyc_doctype_unique');
        });

        DB::table('document_types')->insertOrIgnore([
            'doc_name' => 'Proof of Current Address',
            'category' => 'identity',
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_verification_documents');

        DB::statement("UPDATE `kyc_verifications` SET `status` = 'pending' WHERE `status` NOT IN ('pending', 'approved', 'rejected')");
        DB::statement("ALTER TABLE `kyc_verifications` MODIFY `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");

        Schema::table('kyc_verifications', function (Blueprint $table) {
            $table->dropForeign(['verified_by_staff_id']);
            $table->dropForeign(['approved_by_staff_id']);
            $table->dropColumn([
                'grandfather_name',
                'alt_contact_no',
                'telephone_no',
                'signature_path',
                'signature_date',
                'verified_by_staff_id',
                'verified_at',
                'approved_by_staff_id',
                'approved_at',
            ]);
        });
    }
};
