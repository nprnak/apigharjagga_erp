<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: service_completion_items
     * Purpose: Checklist of services marked as completed on a certificate.
     *          e.g. Client Registration, Property Listing, Site Inspection...
     * Dependency order: #36 — references service_completion_certificates(certificate_id) [CASCADE].
     */
    public function up(): void
    {
        Schema::create('service_completion_items', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY — original PK column is "id"
            $table->bigIncrements('id');

            $table->unsignedBigInteger('certificate_id');

            // Client Registration, Property Listing, Site Inspection...
            $table->string('service_name', 150)
                  ->comment('e.g. Client Registration, Property Listing, Site Inspection');

            $table->boolean('is_completed')->default(false);

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
        Schema::dropIfExists('service_completion_items');
    }
};
