<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Two follow-up corrections to the Annex-F KYC build:
     *
     * 1. Annex F §5 lets a Buyer/Investor/Tenant select more than one
     *    Purpose and Property Type (e.g. "purchase or rent", "house or
     *    apartment") — the single-value enum columns couldn't represent
     *    that, so they're widened to JSON arrays.
     * 2. Annex F §9 "Digital Registration Details" is filled in by the
     *    verifying staff member at the moment they verify the
     *    application, not by the applicant — it needs its own columns
     *    on kyc_verifications (Client ID is auto-generated at that
     *    point; Mobile App User ID is entered by the verifier).
     */
    public function up(): void
    {
        Schema::table('kyc_property_requirements', function (Blueprint $table) {
            $table->json('purpose_multi')->nullable()->after('purpose');
            $table->json('property_type_multi')->nullable()->after('property_type');
        });

        DB::table('kyc_property_requirements')->whereNotNull('purpose')->orWhereNotNull('property_type')->get()->each(function ($row) {
            DB::table('kyc_property_requirements')->where('id', $row->id)->update([
                'purpose_multi' => $row->purpose ? json_encode([$row->purpose]) : null,
                'property_type_multi' => $row->property_type ? json_encode([$row->property_type]) : null,
            ]);
        });

        Schema::table('kyc_property_requirements', function (Blueprint $table) {
            $table->dropColumn(['purpose', 'property_type']);
        });
        Schema::table('kyc_property_requirements', function (Blueprint $table) {
            $table->renameColumn('purpose_multi', 'purpose');
            $table->renameColumn('property_type_multi', 'property_type');
        });

        Schema::table('kyc_verifications', function (Blueprint $table) {
            $table->string('digital_client_id', 30)->nullable()->unique()->after('approved_at');
            $table->string('mobile_app_user_id', 50)->nullable()->after('digital_client_id');
        });
    }

    public function down(): void
    {
        Schema::table('kyc_verifications', function (Blueprint $table) {
            $table->dropColumn(['digital_client_id', 'mobile_app_user_id']);
        });

        Schema::table('kyc_property_requirements', function (Blueprint $table) {
            $table->string('purpose_single', 20)->nullable();
            $table->string('property_type_single', 20)->nullable();
        });

        DB::table('kyc_property_requirements')->get()->each(function ($row) {
            $purpose = json_decode((string) $row->purpose, true);
            $propertyType = json_decode((string) $row->property_type, true);
            DB::table('kyc_property_requirements')->where('id', $row->id)->update([
                'purpose_single' => $purpose[0] ?? null,
                'property_type_single' => $propertyType[0] ?? null,
            ]);
        });

        Schema::table('kyc_property_requirements', function (Blueprint $table) {
            $table->dropColumn(['purpose', 'property_type']);
        });
        Schema::table('kyc_property_requirements', function (Blueprint $table) {
            $table->renameColumn('purpose_single', 'purpose');
            $table->renameColumn('property_type_single', 'property_type');
        });
    }
};
