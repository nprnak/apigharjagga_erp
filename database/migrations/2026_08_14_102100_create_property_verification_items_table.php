<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: property_verification_items
     * Purpose: Normalized checklist rows for property verification.
     * Dependency order: #22 — references property_verifications(verification_id) [CASCADE].
     */
    public function up(): void
    {
        Schema::create('property_verification_items', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('item_id');

            $table->unsignedBigInteger('verification_id');

            // CHECK (category IN (...)) → enum; NOT NULL
            $table->enum('category', [
                'ownership',
                'land_document',
                'legal_status',
                'building',
                'physical',
            ]);

            $table->string('item_name', 150);

            // No CHECK constraint in original schema — free-form status text
            // (verified / not_verified / yes / no / available)
            $table->string('status', 20)->nullable()
                  ->comment('Free-form status: verified / not_verified / yes / no / available');

            $table->string('remarks', 300)->nullable();

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('verification_id')
                  ->references('verification_id')
                  ->on('property_verifications')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_verification_items');
    }
};
