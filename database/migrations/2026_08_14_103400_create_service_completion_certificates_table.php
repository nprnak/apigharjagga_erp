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
     * Table: service_completion_certificates
     * Source form: Annex H
     * Dependency order: #35 — references clients(client_id),
     *                           properties(property_id),
     *                           service_orders(order_id),
     *                           staff(staff_id) ×4.
     */
    public function up(): void
    {
        Schema::create('service_completion_certificates', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('certificate_id');

            $table->string('certificate_no', 30)->unique();

            // DATE NOT NULL DEFAULT CURRENT_DATE
            // MySQL 8.0.13+: expression defaults require parentheses
            $table->date('issue_date')
                  ->default(DB::raw('(CURRENT_DATE)'));

            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('service_order_id')->nullable();

            $table->date('service_start_date')->nullable();
            $table->date('service_completion_date')->nullable();

            $table->unsignedBigInteger('assigned_officer_staff_id')->nullable();
            $table->unsignedBigInteger('technical_reviewer_staff_id')->nullable();

            // CHECK (final_status IN (...)) → enum; NOT NULL with default
            $table->enum('final_status', ['completed', 'completed_with_remarks'])
                  ->default('completed');

            $table->date('client_acceptance_date')->nullable();
            $table->text('client_remarks')->nullable();

            $table->unsignedBigInteger('prepared_by_staff_id')->nullable();
            $table->unsignedBigInteger('verified_by_staff_id')->nullable();

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('created_at')->useCurrent();

            // Foreign keys
            $table->foreign('client_id')
                  ->references('client_id')
                  ->on('clients');

            $table->foreign('property_id')
                  ->references('property_id')
                  ->on('properties');

            $table->foreign('service_order_id')
                  ->references('order_id')
                  ->on('service_orders');

            // Explicit name required: auto-generated name would be 67 chars (MySQL max 64)
            $table->foreign('assigned_officer_staff_id', 'fk_scc_assigned_officer_staff')
                  ->references('staff_id')
                  ->on('staff');

            // Explicit name required: auto-generated name would be 69 chars (MySQL max 64)
            $table->foreign('technical_reviewer_staff_id', 'fk_scc_technical_reviewer_staff')
                  ->references('staff_id')
                  ->on('staff');

            $table->foreign('prepared_by_staff_id')
                  ->references('staff_id')
                  ->on('staff');

            $table->foreign('verified_by_staff_id')
                  ->references('staff_id')
                  ->on('staff');
            // ON DELETE: RESTRICT (default) for all FKs

            // Index from original schema
            $table->index('property_id', 'idx_completion_certs_property');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_completion_certificates');
    }
};
