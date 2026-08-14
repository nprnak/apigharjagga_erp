<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: client_property_requirements
     * Purpose: Buyer / Investor / Tenant search-requirement profile (Annex F §5).
     * Dependency order: #10 — references clients(client_id) [CASCADE].
     */
    public function up(): void
    {
        Schema::create('client_property_requirements', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('requirement_id');

            $table->unsignedBigInteger('client_id');

            // CHECK (purpose IN (...)) → enum; nullable (no NOT NULL in original)
            $table->enum('purpose', ['purchase', 'investment', 'rent'])->nullable();

            // CHECK (property_type IN (...)) → enum; nullable
            $table->enum('property_type', ['land', 'house', 'apartment', 'commercial', 'other'])
                  ->nullable();

            $table->string('preferred_location', 200)->nullable();
            $table->string('required_area', 100)->nullable();

            // NUMERIC(14,2) → decimal(14, 2)
            $table->decimal('estimated_budget', 14, 2)->nullable();

            $table->string('purchase_timeline', 100)->nullable();

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('created_at')->useCurrent();

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('client_id')
                  ->references('client_id')
                  ->on('clients')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_property_requirements');
    }
};
