<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: property_verifications
     * Source form: Annex E
     * Dependency order: #21 — references properties(property_id) [CASCADE],
     *                           staff(staff_id) ×2.
     */
    public function up(): void
    {
        Schema::create('property_verifications', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('verification_id');

            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('verifier_staff_id')->nullable();
            $table->unsignedBigInteger('approver_staff_id')->nullable();

            // DATE NOT NULL DEFAULT CURRENT_DATE
            // MySQL 8.0.13+: expression defaults require parentheses
            $table->date('verification_date')
                  ->default(DB::raw('(CURRENT_DATE)'));

            $table->date('approved_date')->nullable();

            // CHECK (result IN (...)) → enum; nullable
            $table->enum('result', [
                'verified',
                'verified_with_remarks',
                'additional_documents_required',
                'not_verified',
            ])->nullable();

            $table->boolean('gps_coordinates_recorded')->default(false);
            $table->boolean('mis_entry_completed')->default(false);
            $table->boolean('mobile_app_verification_completed')->default(false);

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('created_at')->useCurrent();

            // Foreign keys
            // ON DELETE CASCADE — explicitly specified in schema.sql for property_id
            $table->foreign('property_id')
                  ->references('property_id')
                  ->on('properties')
                  ->cascadeOnDelete();

            $table->foreign('verifier_staff_id')
                  ->references('staff_id')
                  ->on('staff');
            $table->foreign('approver_staff_id')
                  ->references('staff_id')
                  ->on('staff');
            // ON DELETE: RESTRICT (default) for all staff FKs

            // Index from original schema
            $table->index('property_id', 'idx_property_verifications_property');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_verifications');
    }
};
