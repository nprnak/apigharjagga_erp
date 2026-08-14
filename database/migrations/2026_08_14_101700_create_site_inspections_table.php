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
     * Table: site_inspections
     * Source form: Annex D
     * Dependency order: #18 — references properties(property_id) [CASCADE],
     *                           property_listings(listing_id),
     *                           staff(staff_id) ×3.
     */
    public function up(): void
    {
        Schema::create('site_inspections', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('inspection_id');

            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('listing_id')->nullable();
            $table->unsignedBigInteger('inspector_staff_id')->nullable();

            // DATE NOT NULL DEFAULT CURRENT_DATE
            // MySQL 8.0.13+: expression defaults require parentheses
            $table->date('inspection_date')
                  ->default(DB::raw('(CURRENT_DATE)'));

            $table->string('distance_from_main_road', 100)->nullable();
            $table->text('nearby_facilities')->nullable();
            $table->text('commercial_potential')->nullable();
            $table->text('residential_suitability')->nullable();
            $table->text('future_development_potential')->nullable();
            $table->text('observation_notes')->nullable();

            // CHECK (final_status IN (...)) → enum; nullable
            $table->enum('final_status', [
                'suitable_for_listing',
                'requires_additional_verification',
                'not_recommended',
            ])->nullable();

            $table->unsignedBigInteger('prepared_by_staff_id')->nullable();
            $table->date('prepared_date')->nullable();
            $table->unsignedBigInteger('verified_by_staff_id')->nullable();
            $table->date('verified_date')->nullable();

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('created_at')->useCurrent();

            // Foreign keys
            // ON DELETE CASCADE — explicitly specified in schema.sql for property_id
            $table->foreign('property_id')
                  ->references('property_id')
                  ->on('properties')
                  ->cascadeOnDelete();

            $table->foreign('listing_id')
                  ->references('listing_id')
                  ->on('property_listings');
            // ON DELETE: RESTRICT (default)

            $table->foreign('inspector_staff_id')
                  ->references('staff_id')
                  ->on('staff');
            $table->foreign('prepared_by_staff_id')
                  ->references('staff_id')
                  ->on('staff');
            $table->foreign('verified_by_staff_id')
                  ->references('staff_id')
                  ->on('staff');
            // ON DELETE: RESTRICT (default) for all staff FKs

            // Index from original schema
            $table->index('property_id', 'idx_site_inspections_property');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_inspections');
    }
};
