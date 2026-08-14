<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: client_organizations
     * Purpose: Organization details when a client is a company/institution (Annex F §4).
     * Dependency order: #9 — references clients(client_id) [CASCADE], addresses(address_id).
     */
    public function up(): void
    {
        Schema::create('client_organizations', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('org_id');

            $table->unsignedBigInteger('client_id');
            $table->string('organization_name', 200);
            $table->string('registration_no', 50)->nullable();
            $table->string('pan_vat_no', 50)->nullable();
            $table->string('authorized_person', 150)->nullable();
            $table->string('designation', 100)->nullable();
            $table->unsignedBigInteger('office_address_id')->nullable();

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('client_id')
                  ->references('client_id')
                  ->on('clients')
                  ->cascadeOnDelete();

            $table->foreign('office_address_id')
                  ->references('address_id')
                  ->on('addresses');
            // ON DELETE: RESTRICT (default)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_organizations');
    }
};
