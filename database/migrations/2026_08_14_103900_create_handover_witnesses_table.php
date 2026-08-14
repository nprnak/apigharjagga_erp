<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: handover_witnesses
     * Purpose: Witnesses who sign the property handover certificate.
     * Dependency order: #40 — references property_handover_certificates(handover_id) [CASCADE],
     *                           addresses(address_id).
     */
    public function up(): void
    {
        Schema::create('handover_witnesses', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY — original PK column is "id"
            $table->bigIncrements('id');

            $table->unsignedBigInteger('handover_id');
            $table->string('full_name', 150);
            $table->string('citizenship_no', 50)->nullable();
            $table->unsignedBigInteger('address_id')->nullable();

            // TIMESTAMPTZ — nullable, no DEFAULT
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('signed_at')->nullable();

            // Foreign keys
            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('handover_id')
                  ->references('handover_id')
                  ->on('property_handover_certificates')
                  ->cascadeOnDelete();

            $table->foreign('address_id')
                  ->references('address_id')
                  ->on('addresses');
            // ON DELETE: RESTRICT (default)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('handover_witnesses');
    }
};
