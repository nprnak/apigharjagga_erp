<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: company_profile
     * Purpose: The brokerage company's own regulatory registrations
     *          (Broker Licence "BRK...", Land Survey Licence "BRS...").
     * Dependency order: #7 — references addresses(address_id).
     */
    public function up(): void
    {
        Schema::create('company_profile', function (Blueprint $table) {
            // SMALLSERIAL PRIMARY KEY → smallIncrements
            $table->smallIncrements('company_id');

            $table->string('company_name', 200);
            $table->string('registration_no', 50)->nullable();

            // e.g. BRK1835451
            $table->string('broker_licence_no', 50)->nullable()
                  ->comment('e.g. BRK1835451');

            // e.g. BRS1873551
            $table->string('land_survey_licence_no', 50)->nullable()
                  ->comment('e.g. BRS1873551');

            $table->string('pan_vat_no', 50)->nullable();

            // addresses.address_id is a BIGSERIAL (unsigned BIGINT)
            $table->unsignedBigInteger('registered_office_id')->nullable();

            $table->string('contact_no', 20)->nullable();
            $table->date('licence_expiry_date')->nullable();
            $table->boolean('is_active')->default(true);

            $table->foreign('registered_office_id')
                  ->references('address_id')
                  ->on('addresses');
            // ON DELETE: RESTRICT (default) — schema.sql does not specify CASCADE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profile');
    }
};
