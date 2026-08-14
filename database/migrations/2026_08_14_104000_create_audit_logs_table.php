<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: audit_logs
     * Purpose: Cross-cutting audit trail for regulated fields (ownership, price, status).
     *          Captures full old/new value snapshots for insert/update/delete operations.
     * Dependency order: #41 — references staff(staff_id).
     *                          JSONB → JSON (see trade-off note below).
     *
     * JSONB vs JSON trade-off:
     *   PostgreSQL JSONB stores binary-indexed JSON with GIN index support and
     *   efficient key-path operators. MySQL JSON stores text-form JSON with
     *   limited functional indexing (generated columns only). If key-path queries
     *   on old_value/new_value are needed, add generated columns + indexes in a
     *   follow-up migration.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('log_id');

            // 'properties','agreements','payment_receipts',...
            $table->string('entity_type', 50)
                  ->comment("Table name of the audited entity, e.g. 'properties', 'agreements'");

            $table->unsignedBigInteger('entity_id');

            // CHECK (action IN ('insert','update','delete')) → enum; NOT NULL
            $table->enum('action', ['insert', 'update', 'delete']);

            $table->unsignedBigInteger('performed_by_staff_id')->nullable();

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('performed_at')->useCurrent();

            // JSONB → json (MySQL JSON type; see class-level trade-off note)
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();

            // Foreign key
            $table->foreign('performed_by_staff_id')
                  ->references('staff_id')
                  ->on('staff');
            // ON DELETE: RESTRICT (default)

            // Indexes from original schema
            $table->index(['entity_type', 'entity_id'], 'idx_audit_logs_entity');
            $table->index('performed_at', 'idx_audit_logs_performed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
