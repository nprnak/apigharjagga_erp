<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: property_feature_types
     * Purpose: Lookup table for property amenity/feature types used in property listings.
     * Dependency order: #5 — no foreign keys.
     */
    public function up(): void
    {
        Schema::create('property_feature_types', function (Blueprint $table) {
            // SMALLSERIAL PRIMARY KEY → smallIncrements
            $table->smallIncrements('feature_id');

            // Corner Plot, Blacktopped Road, Drinking Water, School Nearby...
            $table->string('feature_name', 100)->unique()
                  ->comment('Corner Plot, Blacktopped Road, Drinking Water, School Nearby, etc.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_feature_types');
    }
};
