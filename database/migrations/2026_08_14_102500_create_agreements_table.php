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
     * Table: agreements
     * Source forms: Annex B PO-PB (Sale/Purchase), Annex B PO-RA (Listing/Brokerage)
     * Dependency order: #26 — references properties(property_id).
     *
     * Composite CHECK constraint (cannot be expressed in Laravel schema builder):
     *   Business rule: a 'sale_purchase' agreement MUST have total_price set;
     *   a 'listing_brokerage' agreement does not require it.
     *   → Added via DB::statement() after table creation.
     */
    public function up(): void
    {
        Schema::create('agreements', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('agreement_id');

            // CHECK (agreement_type IN (...)) → enum; NOT NULL
            $table->enum('agreement_type', ['sale_purchase', 'listing_brokerage']);

            $table->unsignedBigInteger('property_id');

            $table->date('agreement_date');
            $table->string('place', 150)->nullable();

            // Fields for sale_purchase agreements — NUMERIC(14,2) → decimal(14, 2)
            $table->decimal('total_price', 14, 2)->nullable();
            $table->decimal('advance_payment', 14, 2)->nullable();
            $table->decimal('balance_payment', 14, 2)->nullable();
            $table->date('final_payment_date')->nullable();

            // Fields for listing_brokerage agreements — NUMERIC(5,2) → decimal(5, 2)
            $table->decimal('commission_rate_percent', 5, 2)->nullable();
            $table->decimal('commission_fixed_amount', 14, 2)->nullable();

            $table->integer('agreement_period_months')->nullable();
            $table->integer('termination_notice_days')->nullable();

            // CHECK (status IN (...)) → enum; NOT NULL with default 'active'
            $table->enum('status', [
                'draft',
                'active',
                'completed',
                'terminated',
                'breached',
            ])->default('active');

            // MySQL TEXT columns do not support literal DEFAULT values via the schema builder.
            // Original default 'Prevailing laws of Nepal' must be supplied at the application layer.
            $table->text('governing_law')->nullable()
                  ->comment('Default value in original schema: Prevailing laws of Nepal — must be set at application layer');

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('created_at')->useCurrent();

            // Foreign key
            $table->foreign('property_id')
                  ->references('property_id')
                  ->on('properties');
            // ON DELETE: RESTRICT (default — schema.sql does not specify CASCADE here)

            // Index from original schema
            $table->index('property_id', 'idx_agreements_property');
        });

        // -----------------------------------------------------------------------
        // Composite CHECK constraint — MySQL 8.0.16+ native CHECK enforcement.
        // Business rule: agreement_type = 'sale_purchase' requires total_price IS NOT NULL;
        //                agreement_type = 'listing_brokerage' has no price requirement.
        // Cannot be expressed via Laravel's schema builder — using raw DDL.
        // -----------------------------------------------------------------------
        DB::statement(
            "ALTER TABLE `agreements`
             ADD CONSTRAINT `chk_agreements_sale_purchase_price`
             CHECK (
                 (`agreement_type` = 'sale_purchase' AND `total_price` IS NOT NULL)
                 OR `agreement_type` = 'listing_brokerage'
             )"
        );
    }

    /**
     * Reverse the migrations.
     * The CHECK constraint is automatically dropped with the table.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreements');
    }
};
