<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: client_service_requests
     * Purpose: Services a client has requested (Annex F §7).
     * Dependency order: #12 — references clients(client_id) [CASCADE],
     *                           service_types(service_type_id).
     */
    public function up(): void
    {
        Schema::create('client_service_requests', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY — original PK column is "id"
            $table->bigIncrements('id');

            $table->unsignedBigInteger('client_id');

            // service_types.service_type_id is SMALLSERIAL (unsigned SMALLINT)
            $table->unsignedSmallInteger('service_type_id');

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('requested_at')->useCurrent();

            // Composite unique constraint from original schema
            $table->unique(['client_id', 'service_type_id']);

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('client_id')
                  ->references('client_id')
                  ->on('clients')
                  ->cascadeOnDelete();

            $table->foreign('service_type_id')
                  ->references('service_type_id')
                  ->on('service_types');
            // ON DELETE: RESTRICT (default)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_service_requests');
    }
};
