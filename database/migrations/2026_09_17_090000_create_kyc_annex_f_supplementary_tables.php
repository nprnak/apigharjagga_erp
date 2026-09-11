<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Annex F has three sections the KYC wizard was still missing —
     * §4 Organization Details (if applicable), §5 Property Requirement
     * Details, and §7 Required Service Selection. These mirror the
     * client_organizations / client_property_requirements /
     * client_service_requests tables built for the staff-side Annex F
     * intake (ClientRegistrationController), scoped to kyc_verification_id
     * instead of client_id since this is the self-service, User-driven
     * path. §6 Property Owner Details is deliberately not included here —
     * it belongs to a later "list my property" step, not identity KYC.
     */
    public function up(): void
    {
        Schema::create('kyc_organization_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kyc_verification_id')->unique();
            $table->string('organization_name', 200)->nullable();
            $table->string('registration_no', 50)->nullable();
            $table->string('pan_vat_no', 50)->nullable();
            $table->string('authorized_person', 150)->nullable();
            $table->string('designation', 100)->nullable();
            $table->string('office_address', 255)->nullable();

            $table->foreign('kyc_verification_id')->references('id')->on('kyc_verifications')->cascadeOnDelete();
        });

        Schema::create('kyc_property_requirements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kyc_verification_id')->unique();
            $table->enum('purpose', ['purchase', 'investment', 'rent'])->nullable();
            $table->enum('property_type', ['land', 'house', 'apartment', 'commercial', 'other'])->nullable();
            $table->string('preferred_location', 200)->nullable();
            $table->string('required_area', 100)->nullable();
            $table->decimal('estimated_budget', 14, 2)->nullable();
            $table->string('purchase_timeline', 100)->nullable();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('kyc_verification_id')->references('id')->on('kyc_verifications')->cascadeOnDelete();
        });

        Schema::create('kyc_service_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kyc_verification_id');
            $table->unsignedSmallInteger('service_type_id');
            $table->timestamp('requested_at')->useCurrent();

            $table->foreign('kyc_verification_id')->references('id')->on('kyc_verifications')->cascadeOnDelete();
            $table->foreign('service_type_id')->references('service_type_id')->on('service_types');
            $table->unique(['kyc_verification_id', 'service_type_id'], 'kyc_service_requests_kyc_service_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_service_requests');
        Schema::dropIfExists('kyc_property_requirements');
        Schema::dropIfExists('kyc_organization_details');
    }
};
