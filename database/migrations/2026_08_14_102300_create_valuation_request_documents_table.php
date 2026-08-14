<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: valuation_request_documents
     * Purpose: Tracks which document types were submitted with a valuation request.
     * Dependency order: #24 — references valuation_requests(request_id) [CASCADE],
     *                           document_types(doc_type_id).
     */
    public function up(): void
    {
        Schema::create('valuation_request_documents', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY — original PK column is "id"
            $table->bigIncrements('id');

            $table->unsignedBigInteger('request_id');

            // document_types.doc_type_id is SMALLSERIAL (unsigned SMALLINT)
            $table->unsignedSmallInteger('doc_type_id');

            $table->boolean('is_available')->default(false);

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('request_id')
                  ->references('request_id')
                  ->on('valuation_requests')
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
        Schema::dropIfExists('valuation_request_documents');
    }
};
