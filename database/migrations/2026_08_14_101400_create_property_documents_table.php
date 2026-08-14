<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: property_documents
     * Purpose: Tracks document submission status for a property.
     * Dependency order: #15 — references properties(property_id) [CASCADE],
     *                           document_types(doc_type_id).
     */
    public function up(): void
    {
        Schema::create('property_documents', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY — original PK column is "id"
            $table->bigIncrements('id');

            $table->unsignedBigInteger('property_id');

            // document_types.doc_type_id is SMALLSERIAL (unsigned SMALLINT)
            $table->unsignedSmallInteger('doc_type_id');

            // CHECK (status IN ('submitted','pending')) → enum
            $table->enum('status', ['submitted', 'pending'])->default('pending');

            $table->text('file_ref')->nullable();

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('updated_at')->useCurrent();

            // Composite unique constraint from original schema
            $table->unique(['property_id', 'doc_type_id']);

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('property_id')
                  ->references('property_id')
                  ->on('properties')
                  ->cascadeOnDelete();

            $table->foreign('doc_type_id')
                  ->references('doc_type_id')
                  ->on('document_types');
            // ON DELETE: RESTRICT (default)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_documents');
    }
};
