<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: service_deliverables
     * Purpose: Deliverable documents/outputs provided as part of service completion.
     *          e.g. Listing Report, Verification Report, Valuation Report...
     * Dependency order: #37 — references service_completion_certificates(certificate_id) [CASCADE].
     */
    public function up(): void
    {
        Schema::create('service_deliverables', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY — original PK column is "id"
            $table->bigIncrements('id');

            $table->unsignedBigInteger('certificate_id');

            // Listing Report, Verification Report, Valuation Report...
            $table->string('deliverable_name', 150)
                  ->comment('e.g. Listing Report, Verification Report, Valuation Report');

            $table->boolean('is_provided')->default(false);
            $table->text('file_ref')->nullable();

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('certificate_id')
                  ->references('certificate_id')
                  ->on('service_completion_certificates')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_deliverables');
    }
};
