<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: property_features
     * Purpose: Junction table — links properties to their feature/amenity types.
     * Dependency order: #14 — references properties(property_id) [CASCADE],
     *                           property_feature_types(feature_id).
     */
    public function up(): void
    {
        Schema::create('property_features', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY — original PK column is "id"
            $table->bigIncrements('id');

            $table->unsignedBigInteger('property_id');

            // property_feature_types.feature_id is SMALLSERIAL (unsigned SMALLINT)
            $table->unsignedSmallInteger('feature_id');

            // Composite unique constraint from original schema
            $table->unique(['property_id', 'feature_id']);

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('property_id')
                  ->references('property_id')
                  ->on('properties')
                  ->cascadeOnDelete();

            $table->foreign('feature_id')
                  ->references('feature_id')
                  ->on('property_feature_types');
            // ON DELETE: RESTRICT (default)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_features');
    }
};
