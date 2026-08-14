<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: valuation_reports
     * Source form: Annex C
     * Dependency order: #25 — references valuation_requests(request_id),
     *                           properties(property_id),
     *                           staff(staff_id) ×2.
     */
    public function up(): void
    {
        Schema::create('valuation_reports', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('report_id');

            $table->string('report_no', 30)->unique();

            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('property_id');

            // CHECK (valuation_type IN (...)) → enum; NOT NULL
            $table->enum('valuation_type', [
                'market_value',
                'forced_sale_value',
                'mortgage_valuation',
                'fair_value',
                'insurance_value',
                'investment_value',
                'rental_value',
                'government_valuation',
                'asset_valuation',
            ]);

            // NUMERIC(14,2) NOT NULL → decimal(14, 2)
            $table->decimal('valuated_amount', 14, 2);

            // Rate calculation notes / per-unit rate used
            $table->text('rate_basis')->nullable()
                  ->comment('Rate calculation notes or per-unit rate used');

            $table->unsignedBigInteger('valuator_staff_id')->nullable();
            $table->unsignedBigInteger('approved_by_staff_id')->nullable();

            // CHECK (approval_status IN (...)) → enum; NOT NULL with default
            $table->enum('approval_status', [
                'draft',
                'pending_approval',
                'approved',
                'rejected',
            ])->default('draft');

            $table->boolean('digitally_signed')->default(false);
            $table->text('report_file_ref')->nullable();
            $table->date('issued_date')->nullable();

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('created_at')->useCurrent();

            // Foreign keys
            $table->foreign('request_id')
                  ->references('request_id')
                  ->on('valuation_requests');
            // ON DELETE: RESTRICT (default)

            $table->foreign('property_id')
                  ->references('property_id')
                  ->on('properties');
            // ON DELETE: RESTRICT (default — property_id in this table is NOT cascade)

            $table->foreign('valuator_staff_id')
                  ->references('staff_id')
                  ->on('staff');
            $table->foreign('approved_by_staff_id')
                  ->references('staff_id')
                  ->on('staff');
            // ON DELETE: RESTRICT (default) for all staff FKs

            // Index from original schema
            $table->index('property_id', 'idx_valuation_reports_property');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valuation_reports');
    }
};
