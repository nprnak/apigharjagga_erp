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
     * Table: complaints
     * Source form: Annex G
     * Dependency order: #31 — references clients(client_id),
     *                           properties(property_id),
     *                           staff(staff_id) ×2.
     *
     * Partial index trade-off:
     *   Original: CREATE INDEX idx_complaints_priority ON complaints(priority)
     *             WHERE status NOT IN ('resolved','closed')
     *   MySQL does NOT support partial/filtered indexes.
     *   Replaced with a normal composite index on (status, priority).
     *   Trade-off: the index covers all rows including resolved/closed complaints,
     *   resulting in slightly larger index size, but query optimizer can still
     *   efficiently filter on both columns together.
     */
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('complaint_id');

            $table->string('complaint_code', 30)->unique();

            // DATE NOT NULL DEFAULT CURRENT_DATE
            // MySQL 8.0.13+: expression defaults require parentheses
            $table->date('complaint_date')
                  ->default(DB::raw('(CURRENT_DATE)'));

            $table->time('complaint_time')->nullable();

            // CHECK (received_through IN (...)) → enum; nullable
            $table->enum('received_through', [
                'mobile_app',
                'website',
                'office',
                'email',
                'phone',
                'other',
            ])->nullable();

            $table->unsignedBigInteger('received_by_staff_id')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('property_id')->nullable();

            // Free text: "service taken"
            $table->string('service_reference', 200)->nullable()
                  ->comment('Free text describing the service taken');

            $table->date('service_date')->nullable();

            // CHECK (category IN (...)) → enum; nullable
            $table->enum('category', [
                'property_listing_issue',
                'property_information_incorrect',
                'valuation_related_issue',
                'site_visit_issue',
                'digital_platform_issue',
                'staff_service_behaviour',
                'payment_billing_issue',
                'documentation_issue',
                'other',
            ])->nullable();

            $table->text('description')->nullable();

            // CHECK (priority IN (...)) → enum; NOT NULL with default
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                  ->default('medium');

            $table->string('assigned_department', 100)->nullable();
            $table->unsignedBigInteger('assigned_officer_staff_id')->nullable();
            $table->date('investigation_date')->nullable();
            $table->text('findings')->nullable();
            $table->text('corrective_action_taken')->nullable();
            $table->date('resolution_date')->nullable();

            // CHECK (status IN (...)) → enum; NOT NULL with default
            $table->enum('status', [
                'registered',
                'under_investigation',
                'resolved',
                'closed',
                'pending_customer_response',
            ])->default('registered');

            // CHECK (satisfaction_level IN (...)) → enum; nullable
            $table->enum('satisfaction_level', [
                'very_satisfied',
                'satisfied',
                'neutral',
                'dissatisfied',
            ])->nullable();

            $table->text('customer_remarks')->nullable();

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('created_at')->useCurrent();
            // Application layer is responsible for updating this field on every write.
            $table->timestamp('updated_at')->useCurrent();

            // Foreign keys
            $table->foreign('received_by_staff_id')
                  ->references('staff_id')
                  ->on('staff');

            $table->foreign('client_id')
                  ->references('client_id')
                  ->on('clients');

            $table->foreign('property_id')
                  ->references('property_id')
                  ->on('properties');

            $table->foreign('assigned_officer_staff_id')
                  ->references('staff_id')
                  ->on('staff');
            // ON DELETE: RESTRICT (default) for all FKs

            // Indexes from original schema
            $table->index('status', 'idx_complaints_status');
            $table->index('client_id', 'idx_complaints_client');

            // Partial index replacement:
            // Original: CREATE INDEX idx_complaints_priority ON complaints(priority)
            //           WHERE status NOT IN ('resolved','closed')
            // MySQL does not support partial indexes — using composite (status, priority) instead.
            // The optimizer can filter active complaints efficiently using both columns.
            $table->index(['status', 'priority'], 'idx_complaints_status_priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
