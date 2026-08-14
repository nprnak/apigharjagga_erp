<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: agreement_parties
     * Purpose: Normalized parties for an agreement:
     *          Seller/Buyer for sale_purchase, Company/PropertyOwner for listing_brokerage.
     *          Company party rows have client_id NULL and reference company_profile instead.
     * Dependency order: #27 — references agreements(agreement_id) [CASCADE],
     *                           clients(client_id),
     *                           company_profile(company_id).
     *
     * Composite CHECK constraint (cannot be expressed in Laravel schema builder):
     *   Business rule: when party_role = 'company', company_id MUST be set (client_id is NULL);
     *                  for any other role, client_id MUST be set (company_id may be NULL).
     *   → Added via DB::statement() after table creation.
     */
    public function up(): void
    {
        Schema::create('agreement_parties', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY — original PK column is "id"
            $table->bigIncrements('id');

            $table->unsignedBigInteger('agreement_id');

            // CHECK (party_role IN (...)) → enum; NOT NULL
            $table->enum('party_role', ['seller', 'buyer', 'property_owner', 'company']);

            $table->unsignedBigInteger('client_id')->nullable();

            // company_profile.company_id is SMALLSERIAL (unsigned SMALLINT)
            $table->unsignedSmallInteger('company_id')->nullable();

            $table->string('representative_name', 150)->nullable();
            $table->string('designation', 100)->nullable();

            // Foreign keys
            // ON DELETE CASCADE — explicitly specified in schema.sql for agreement_id
            $table->foreign('agreement_id')
                  ->references('agreement_id')
                  ->on('agreements')
                  ->cascadeOnDelete();

            $table->foreign('client_id')
                  ->references('client_id')
                  ->on('clients');
            // ON DELETE: RESTRICT (default)

            $table->foreign('company_id')
                  ->references('company_id')
                  ->on('company_profile');
            // ON DELETE: RESTRICT (default)

            // Index from original schema
            $table->index('agreement_id', 'idx_agreement_parties_agreement');
        });

        // -----------------------------------------------------------------------
        // Composite CHECK constraint — MySQL 8.0.16+ native CHECK enforcement.
        // Business rule: party_role = 'company' → company_id must be provided;
        //                any other role          → client_id must be provided.
        // Cannot be expressed via Laravel's schema builder — using raw DDL.
        // -----------------------------------------------------------------------
        DB::statement(
            "ALTER TABLE `agreement_parties`
             ADD CONSTRAINT `chk_agreement_parties_role_entity`
             CHECK (
                 (`party_role` = 'company' AND `company_id` IS NOT NULL)
                 OR (`party_role` <> 'company' AND `client_id` IS NOT NULL)
             )"
        );
    }

    /**
     * Reverse the migrations.
     * The CHECK constraint is automatically dropped with the table.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreement_parties');
    }
};
