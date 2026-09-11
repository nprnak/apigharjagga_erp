<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Annex A §2 "Property Owner Details" only needs new fields when the
     * applicant isn't the owner themselves (family member / authorized
     * representative / company) — when `ownership_role = 'self'` the owner
     * *is* the applicant, whose identity already lives on the KYC record,
     * so nothing new is needed there.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('owner_full_name', 150)->nullable()->after('ownership_role');
            $table->string('owner_citizenship_no', 50)->nullable()->after('owner_full_name');
            $table->string('owner_relation', 100)->nullable()->after('owner_citizenship_no');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['owner_full_name', 'owner_citizenship_no', 'owner_relation']);
        });
    }
};
