<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: document_types
     * Purpose: Lookup table for document categories accepted/required across all modules.
     * Dependency order: #4 — no foreign keys.
     */
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            // SMALLSERIAL PRIMARY KEY → smallIncrements
            $table->smallIncrements('doc_type_id');

            // Citizenship Copy, Lalpurja, Tax Clearance, Blueprint,
            // Building Completion Cert, Valuation Report, PoA,
            // Utility Bills, Photographs...
            $table->string('doc_name', 150)->unique()
                  ->comment('Citizenship Copy, Lalpurja, Tax Clearance, Blueprint, etc.');

            // identity / land / building / financial
            $table->string('category', 50)->nullable()
                  ->comment('identity / land / building / financial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
