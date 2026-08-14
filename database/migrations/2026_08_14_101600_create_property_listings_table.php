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
     * Table: property_listings
     * Source form: Annex A
     * Dependency order: #17 — references properties(property_id) [CASCADE],
     *                           clients(client_id),
     *                           staff(staff_id) ×2.
     */
    public function up(): void
    {
        Schema::create('property_listings', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('listing_id');

            $table->string('application_no', 30)->unique();

            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('applicant_client_id');

            // CHECK (purpose_of_listing IN (...)) → enum; NOT NULL
            $table->enum('purpose_of_listing', [
                'sale',
                'rent',
                'lease',
                'exchange',
                'investment',
                'other',
            ]);

            // NUMERIC(14,2) → decimal(14, 2)
            $table->decimal('expected_selling_price', 14, 2)->nullable();
            $table->boolean('negotiable')->default(false);
            $table->decimal('minimum_acceptable_price', 14, 2)->nullable();
            $table->decimal('rental_amount', 14, 2)->nullable();

            $table->date('effective_date')->nullable();

            // DATE NOT NULL DEFAULT CURRENT_DATE
            // MySQL 8.0.13+: expression defaults require parentheses
            $table->date('date_received')
                  ->default(DB::raw('(CURRENT_DATE)'));

            $table->unsignedBigInteger('assigned_officer_id')->nullable();

            $table->boolean('inspection_required')->default(true);
            $table->date('inspection_date')->nullable();
            $table->boolean('valuation_required')->default(false);
            $table->boolean('photographs_received')->default(false);
            $table->boolean('gis_location_verified')->default(false);

            // CHECK (legal_verification_status IN (...)) → enum
            $table->enum('legal_verification_status', ['pending', 'completed'])
                  ->default('pending');

            // CHECK (listing_status IN (...)) → enum
            $table->enum('listing_status', ['approved', 'pending', 'rejected'])
                  ->default('pending');

            $table->text('remarks')->nullable();

            $table->unsignedBigInteger('received_by_staff_id')->nullable();

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('created_at')->useCurrent();
            // Application layer is responsible for updating this field on every write.
            $table->timestamp('updated_at')->useCurrent();

            // Foreign keys
            // ON DELETE CASCADE — explicitly specified in schema.sql for property_id
            $table->foreign('property_id')
                  ->references('property_id')
                  ->on('properties')
                  ->cascadeOnDelete();

            $table->foreign('applicant_client_id')
                  ->references('client_id')
                  ->on('clients');
            // ON DELETE: RESTRICT (default)

            $table->foreign('assigned_officer_id')
                  ->references('staff_id')
                  ->on('staff');
            // ON DELETE: RESTRICT (default)

            $table->foreign('received_by_staff_id')
                  ->references('staff_id')
                  ->on('staff');
            // ON DELETE: RESTRICT (default)

            // Indexes from original schema
            $table->index('listing_status', 'idx_listings_status');
            $table->index('property_id', 'idx_listings_property');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_listings');
    }
};
