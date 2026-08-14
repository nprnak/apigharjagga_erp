<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: client_documents
     * Purpose: Documents a client submits at registration (Annex F §8).
     * Dependency order: #11 — references clients(client_id) [CASCADE],
     *                          document_types(doc_type_id).
     */
    public function up(): void
    {
        Schema::create('client_documents', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('client_doc_id');

            $table->unsignedBigInteger('client_id');

            // document_types.doc_type_id is SMALLSERIAL (unsigned SMALLINT)
            $table->unsignedSmallInteger('doc_type_id');

            // CHECK (status IN ('submitted','pending')) → enum
            $table->enum('status', ['submitted', 'pending'])->default('pending');

            $table->text('file_ref')->nullable();

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('updated_at')->useCurrent();

            // Composite unique constraint from original schema
            $table->unique(['client_id', 'doc_type_id']);

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('client_id')
                  ->references('client_id')
                  ->on('clients')
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
        Schema::dropIfExists('client_documents');
    }
};
