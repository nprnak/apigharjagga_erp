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
     * Table: property_handover_certificates
     * Source form: Annex J
     * Dependency order: #38 — references clients(client_id),
     *                           staff(staff_id),
     *                           properties(property_id).
     */
    public function up(): void
    {
        Schema::create('property_handover_certificates', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('handover_id');

            $table->string('certificate_no', 30)->unique();

            // DATE NOT NULL DEFAULT CURRENT_DATE
            // MySQL 8.0.13+: expression defaults require parentheses
            $table->date('handover_date')
                  ->default(DB::raw('(CURRENT_DATE)'));

            $table->string('place', 150)->nullable();
            $table->unsignedBigInteger('owner_client_id');
            $table->unsignedBigInteger('company_rep_staff_id')->nullable();
            $table->unsignedBigInteger('property_id');

            // CHECK (purpose IN (...)) → enum; nullable
            $table->enum('purpose', [
                'property_listing_service',
                'marketing_promotion',
                'valuation_process',
                'sale_purchase_facilitation',
                'lease_rent_management',
                'other',
            ])->nullable();

            // CHECK (possession_status IN (...)) → enum; NOT NULL with default
            $table->enum('possession_status', ['handed_over', 'pending'])
                  ->default('pending');

            // CHECK (keys_status IN (...)) → enum; nullable
            $table->enum('keys_status', ['received', 'not_applicable'])->nullable();

            // CHECK (ownership_docs_status IN (...)) → enum; nullable
            $table->enum('ownership_docs_status', ['received', 'pending'])->nullable();

            // CHECK (tax_docs_status IN (...)) → enum; nullable
            $table->enum('tax_docs_status', ['received', 'pending'])->nullable();

            // CHECK (utility_docs_status IN (...)) → enum; nullable
            $table->enum('utility_docs_status', ['received', 'pending'])->nullable();

            // CHECK (land_boundary_condition IN (...)) → enum; nullable
            $table->enum('land_boundary_condition', ['clear', 'not_clear'])->nullable();

            // CHECK (building_structure_condition IN (...)) → enum; nullable
            $table->enum('building_structure_condition', ['good', 'repair_required'])->nullable();

            // CHECK (electrical_condition IN (...)) → enum; nullable
            $table->enum('electrical_condition', ['functional', 'not_functional'])->nullable();

            // CHECK (water_supply_condition IN (...)) → enum; nullable
            $table->enum('water_supply_condition', ['available', 'not_available'])->nullable();

            // CHECK (sanitation_condition IN (...)) → enum; nullable
            $table->enum('sanitation_condition', ['available', 'not_available'])->nullable();

            // CHECK (furniture_equipment_status IN (...)) → enum; nullable
            $table->enum('furniture_equipment_status', ['available', 'none'])->nullable();

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('created_at')->useCurrent();

            // Foreign keys
            $table->foreign('owner_client_id')
                  ->references('client_id')
                  ->on('clients');

            $table->foreign('company_rep_staff_id')
                  ->references('staff_id')
                  ->on('staff');

            $table->foreign('property_id')
                  ->references('property_id')
                  ->on('properties');
            // ON DELETE: RESTRICT (default) for all FKs

            // Index from original schema
            $table->index('property_id', 'idx_handover_certs_property');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_handover_certificates');
    }
};
