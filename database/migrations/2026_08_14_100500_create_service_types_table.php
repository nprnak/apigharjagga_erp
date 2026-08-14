<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: service_types
     * Purpose: Lookup table for services offered by the brokerage platform.
     * Dependency order: #6 — no foreign keys.
     */
    public function up(): void
    {
        Schema::create('service_types', function (Blueprint $table) {
            // SMALLSERIAL PRIMARY KEY → smallIncrements
            $table->smallIncrements('service_type_id');

            // Property Listing, Verification, Valuation,
            // Digital Marketing, Consultation, Documentation Support
            $table->string('service_name', 150)->unique()
                  ->comment('Property Listing, Verification, Valuation, Digital Marketing, etc.');

            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_types');
    }
};
