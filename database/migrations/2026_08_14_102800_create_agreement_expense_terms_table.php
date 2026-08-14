<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: agreement_expense_terms
     * Purpose: Expense/fee schedule attached to listing_brokerage agreements (Annex PO-RA §6).
     * Dependency order: #29 — references agreements(agreement_id) [CASCADE].
     */
    public function up(): void
    {
        Schema::create('agreement_expense_terms', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY — original PK column is "id"
            $table->bigIncrements('id');

            $table->unsignedBigInteger('agreement_id');

            // CHECK (expense_type IN (...)) → enum; NOT NULL
            $table->enum('expense_type', [
                'property_tax',
                'land_revenue',
                'capital_gains_tax',
                'document_prep_notarization',
                'utility_bill_clearance',
                'registration_charge',
                'preliminary_consultation_charge',
                'field_visit_charge',
                'valuation_charge',
                'digital_marketing_charge',
            ]);

            // e.g. "NPR 1,000" or "As per GoN Rules"
            $table->string('amount_or_basis', 150)->nullable()
                  ->comment('e.g. "NPR 1,000" or "As per GoN Rules"');

            // CHECK (borne_by IN (...)) → enum; nullable
            $table->enum('borne_by', ['owner', 'buyer', 'company', 'as_agreed'])->nullable();

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('agreement_id')
                  ->references('agreement_id')
                  ->on('agreements')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreement_expense_terms');
    }
};
