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
     * Table: valuation_requests
     * Source forms: Annex C + Valuation Module
     * Dependency order: #23 — references clients(client_id),
     *                           properties(property_id) [CASCADE],
     *                           staff(staff_id).
     */
    public function up(): void
    {
        Schema::create('valuation_requests', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('request_id');

            $table->string('request_code', 30)->unique();

            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('property_id');

            // CHECK (purpose_of_valuation IN (...)) → enum; nullable
            $table->enum('purpose_of_valuation', [
                'bank_loan_mortgage',
                'buying_selling',
                'insurance',
                'legal',
                'investment_decision',
                'other',
            ])->nullable();

            // CHECK (requested_valuation_type IN (...)) → enum; nullable
            $table->enum('requested_valuation_type', [
                'market_value',
                'forced_sale_value',
                'government_value_reference',
                'rental_value',
            ])->nullable();

            $table->date('preferred_visit_date')->nullable();
            $table->string('preferred_visit_time', 20)->nullable();
            $table->string('site_contact_person_name', 150)->nullable();
            $table->string('site_contact_mobile', 20)->nullable();

            $table->unsignedBigInteger('assigned_valuator_staff_id')->nullable();

            $table->date('field_visit_date')->nullable();

            // DATE NOT NULL DEFAULT CURRENT_DATE
            // MySQL 8.0.13+: expression defaults require parentheses
            $table->date('application_received_date')
                  ->default(DB::raw('(CURRENT_DATE)'));

            // CHECK (status IN (...)) → enum; NOT NULL with default
            $table->enum('status', [
                'received',
                'site_visit_scheduled',
                'in_progress',
                'report_issued',
                'cancelled',
            ])->default('received');

            $table->text('remarks')->nullable();

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('created_at')->useCurrent();

            // Foreign keys
            $table->foreign('client_id')
                  ->references('client_id')
                  ->on('clients');
            // ON DELETE: RESTRICT (default)

            // ON DELETE CASCADE — explicitly specified in schema.sql for property_id
            $table->foreign('property_id')
                  ->references('property_id')
                  ->on('properties')
                  ->cascadeOnDelete();

            $table->foreign('assigned_valuator_staff_id')
                  ->references('staff_id')
                  ->on('staff');
            // ON DELETE: RESTRICT (default)

            // Index from original schema
            $table->index('property_id', 'idx_valuation_requests_property');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valuation_requests');
    }
};
