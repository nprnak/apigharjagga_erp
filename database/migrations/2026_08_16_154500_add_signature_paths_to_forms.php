<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store scanned signature image paths for Annex A (applicant)
     * and Annex B (seller, buyer, witnesses).
     */
    public function up(): void
    {
        Schema::table('property_listings', function (Blueprint $table) {
            $table->string('applicant_signature_path', 255)->nullable()->after('received_by_staff_id');
        });

        Schema::table('agreements', function (Blueprint $table) {
            $table->string('seller_signature_path', 255)->nullable()->after('governing_law');
            $table->string('buyer_signature_path', 255)->nullable()->after('seller_signature_path');
        });

        Schema::table('agreement_witnesses', function (Blueprint $table) {
            $table->string('signature_path', 255)->nullable()->after('citizenship_no');
        });
    }

    public function down(): void
    {
        Schema::table('property_listings', function (Blueprint $table) {
            $table->dropColumn('applicant_signature_path');
        });

        Schema::table('agreements', function (Blueprint $table) {
            $table->dropColumn(['seller_signature_path', 'buyer_signature_path']);
        });

        Schema::table('agreement_witnesses', function (Blueprint $table) {
            $table->dropColumn('signature_path');
        });
    }
};
