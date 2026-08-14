<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: handover_documents
     * Purpose: Tracks which document types were received at property handover.
     * Dependency order: #39 — references property_handover_certificates(handover_id) [CASCADE],
     *                           document_types(doc_type_id).
     */
    public function up(): void
    {
        Schema::create('handover_documents', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY — original PK column is "id"
            $table->bigIncrements('id');

            $table->unsignedBigInteger('handover_id');

            // document_types.doc_type_id is SMALLSERIAL (unsigned SMALLINT)
            $table->unsignedSmallInteger('doc_type_id');

            $table->boolean('is_received')->default(false);

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('handover_id')
                  ->references('handover_id')
                  ->on('property_handover_certificates')
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
        Schema::dropIfExists('handover_documents');
    }
};
