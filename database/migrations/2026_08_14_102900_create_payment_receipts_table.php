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
     * Table: payment_receipts
     * Source form: Annex I
     * Dependency order: #30 — references clients(client_id),
     *                           agreements(agreement_id),
     *                           properties(property_id),
     *                           staff(staff_id).
     *
     * Composite CHECK constraints (cannot be expressed in Laravel schema builder):
     *   1. Business rule: amount must be positive (amount > 0).
     *      → Added via DB::statement().
     *   2. Business rule: when mode_of_payment = 'cheque', both cheque_no AND bank_name
     *      must be provided. Cash payments have no such requirement.
     *      → Added via DB::statement().
     */
    public function up(): void
    {
        Schema::create('payment_receipts', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('receipt_id');

            $table->string('receipt_no', 30)->unique();

            // DATE NOT NULL DEFAULT CURRENT_DATE
            // MySQL 8.0.13+: expression defaults require parentheses
            $table->date('receipt_date')
                  ->default(DB::raw('(CURRENT_DATE)'));

            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('agreement_id')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();

            // NUMERIC(14,2) NOT NULL — the CHECK (amount > 0) is enforced via DB::statement below
            $table->decimal('amount', 14, 2);

            $table->string('amount_in_words', 300)->nullable();

            // CHECK (purpose IN (...)) → enum; NOT NULL
            $table->enum('purpose', [
                'property_valuation_fee',
                'field_visit_charge',
                'digital_marketing_charge',
                'preliminary_consultation_charge',
                'property_registration_service',
                'brokerage_commission',
                'other',
            ]);

            // CHECK (mode_of_payment IN ('cash','cheque')) → enum; NOT NULL
            $table->enum('mode_of_payment', ['cash', 'cheque']);

            // Cheque-specific fields — nullability enforced by composite CHECK below
            $table->string('cheque_no', 30)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->date('cheque_date')->nullable();

            $table->unsignedBigInteger('received_by_staff_id')->nullable();

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('created_at')->useCurrent();

            // Foreign keys
            $table->foreign('client_id')
                  ->references('client_id')
                  ->on('clients');
            // ON DELETE: RESTRICT (default)

            $table->foreign('agreement_id')
                  ->references('agreement_id')
                  ->on('agreements');
            // ON DELETE: RESTRICT (default)

            $table->foreign('property_id')
                  ->references('property_id')
                  ->on('properties');
            // ON DELETE: RESTRICT (default)

            $table->foreign('received_by_staff_id')
                  ->references('staff_id')
                  ->on('staff');
            // ON DELETE: RESTRICT (default)

            // Indexes from original schema
            $table->index('client_id', 'idx_payment_receipts_client');
            $table->index('receipt_date', 'idx_payment_receipts_date');
        });

        // -----------------------------------------------------------------------
        // CHECK #1 — MySQL 8.0.16+ native CHECK enforcement.
        // Business rule: payment amount must always be positive.
        // -----------------------------------------------------------------------
        DB::statement(
            "ALTER TABLE `payment_receipts`
             ADD CONSTRAINT `chk_payment_receipts_amount_positive`
             CHECK (`amount` > 0)"
        );

        // -----------------------------------------------------------------------
        // CHECK #2 — MySQL 8.0.16+ native CHECK enforcement.
        // Business rule: cheque payments must provide cheque_no AND bank_name;
        //                cash payments have no such requirement.
        // Cannot be expressed via Laravel's schema builder — using raw DDL.
        // -----------------------------------------------------------------------
        DB::statement(
            "ALTER TABLE `payment_receipts`
             ADD CONSTRAINT `chk_payment_receipts_cheque_fields`
             CHECK (
                 `mode_of_payment` = 'cash'
                 OR (`cheque_no` IS NOT NULL AND `bank_name` IS NOT NULL)
             )"
        );
    }

    /**
     * Reverse the migrations.
     * Both CHECK constraints are automatically dropped with the table.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};
