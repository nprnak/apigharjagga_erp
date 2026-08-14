<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: properties
     * Source forms: Annex A / C / E / H / J
     * Dependency order: #13 — references clients(client_id), addresses(address_id).
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('property_id');

            // "Property ID" / "Listing ID" on forms
            $table->string('property_code', 30)->unique()
                  ->comment('Property ID / Listing ID as printed on forms');

            $table->unsignedBigInteger('owner_client_id');

            // CHECK (ownership_role IN (...)) → enum; nullable (no NOT NULL in original)
            $table->enum('ownership_role', [
                'self',
                'family_member',
                'authorized_representative',
                'company',
            ])->nullable();

            // CHECK (property_type IN (...)) → enum; NOT NULL
            $table->enum('property_type', [
                'land',
                'house',
                'apartment',
                'commercial_building',
                'office_space',
                'industrial_property',
                'agricultural_land',
                'other',
            ]);

            $table->unsignedBigInteger('address_id')->nullable();

            $table->string('kitta_no', 50)->nullable();

            // Area kept as VARCHAR — unit varies (ropani / aana / sqft)
            $table->string('area', 100)->nullable()
                  ->comment('Area value; unit varies: ropani / aana / sqft');

            $table->string('map_sheet_no', 50)->nullable();

            // private / joint / other
            $table->string('ownership_type', 50)->nullable()
                  ->comment('private / joint / other');

            // Lalpurja No.
            $table->string('ownership_certificate_no', 50)->nullable()
                  ->comment('Land Ownership Certificate (Lalpurja) number');

            $table->string('road_access', 20)->nullable();
            $table->string('road_width', 50)->nullable();
            $table->string('facing_direction', 30)->nullable();
            $table->integer('year_of_construction')->nullable();
            $table->integer('no_of_floors')->nullable();
            $table->string('covered_area', 100)->nullable();

            // RCC / Load Bearing / Steel / Other
            $table->string('structure_type', 50)->nullable()
                  ->comment('RCC / Load Bearing / Steel / Other');

            $table->string('roof_type', 50)->nullable();
            $table->string('parking', 50)->nullable();
            $table->string('water_supply', 50)->nullable();
            $table->string('electricity', 50)->nullable();
            $table->string('internet', 50)->nullable();
            $table->string('drainage', 50)->nullable();

            // Naksa Pass No.
            $table->string('building_permit_no', 50)->nullable()
                  ->comment('Building permit / Naksa Pass number');

            // CHECK (current_building_condition IN (...)) → enum; nullable
            $table->enum('current_building_condition', ['excellent', 'good', 'fair', 'poor'])
                  ->nullable();

            // CHECK (status IN (...)) → enum; NOT NULL with default
            $table->enum('status', [
                'draft',
                'listed',
                'under_verification',
                'under_valuation',
                'under_negotiation',
                'sold',
                'rented',
                'leased',
                'withdrawn',
                'rejected',
            ])->default('draft');

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('created_at')->useCurrent();
            // Application layer is responsible for updating this field on every write.
            $table->timestamp('updated_at')->useCurrent();

            // Foreign keys
            $table->foreign('owner_client_id')
                  ->references('client_id')
                  ->on('clients');
            // ON DELETE: RESTRICT (default)

            $table->foreign('address_id')
                  ->references('address_id')
                  ->on('addresses');
            // ON DELETE: RESTRICT (default)

            // Indexes from original schema
            $table->index('owner_client_id', 'idx_properties_owner');
            $table->index('status', 'idx_properties_status');
            $table->index('kitta_no', 'idx_properties_kitta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
