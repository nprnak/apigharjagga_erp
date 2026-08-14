<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: site_inspection_documents
     * Purpose: Tracks which document types were available at site inspection.
     * Dependency order: #20 — references site_inspections(inspection_id) [CASCADE],
     *                           document_types(doc_type_id).
     */
    public function up(): void
    {
        Schema::create('site_inspection_documents', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY — original PK column is "id"
            $table->bigIncrements('id');

            $table->unsignedBigInteger('inspection_id');

            // document_types.doc_type_id is SMALLSERIAL (unsigned SMALLINT)
            $table->unsignedSmallInteger('doc_type_id');

            $table->boolean('is_available')->default(false);

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('inspection_id')
                  ->references('inspection_id')
                  ->on('site_inspections')
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
        Schema::dropIfExists('site_inspection_documents');
    }
};
