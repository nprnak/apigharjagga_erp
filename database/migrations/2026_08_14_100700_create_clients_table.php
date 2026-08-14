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
     * Table: clients
     * Source form: Annex F — Property Owner / Buyer / Investor / Tenant / Agent
     * Dependency order: #8 — references addresses(address_id) ×2, staff(staff_id).
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('client_id');

            // "Client ID" on forms
            $table->string('client_code', 30)->unique()
                  ->comment('Client ID as printed on registration forms');

            // CHECK (client_type IN (...)) → enum
            $table->enum('client_type', ['owner', 'buyer', 'investor', 'tenant', 'agent', 'other']);

            $table->string('full_name', 150);
            $table->string('father_mother_name', 150)->nullable();
            $table->string('spouse_name', 150)->nullable();

            // Annex A field
            $table->string('grandfather_name', 150)->nullable()
                  ->comment('Annex A field');

            $table->string('citizenship_no', 50)->unique()->nullable();

            // Nullable column with a default (no NOT NULL in original schema)
            $table->string('nationality', 50)->default('Nepali')->nullable();

            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('occupation', 100)->nullable();
            $table->string('mobile_no', 20);
            $table->string('alt_contact_no', 20)->nullable();
            $table->string('telephone_no', 20)->nullable();
            $table->string('email', 150)->nullable();

            // Two address FK columns — both nullable
            $table->unsignedBigInteger('permanent_address_id')->nullable();
            $table->unsignedBigInteger('current_address_id')->nullable();

            $table->string('mobile_app_user_id', 100)->nullable();

            // DATE NOT NULL DEFAULT CURRENT_DATE
            // MySQL 8.0.13+: expression defaults require parentheses
            $table->date('registration_date')
                  ->default(DB::raw('(CURRENT_DATE)'));

            $table->unsignedBigInteger('registered_by')->nullable();

            // CHECK (mis_entry_status IN (...)) → enum
            $table->enum('mis_entry_status', ['pending', 'completed'])->default('pending');

            $table->boolean('is_active')->default(true);

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('created_at')->useCurrent();
            // Application layer is responsible for updating this field on every write.
            $table->timestamp('updated_at')->useCurrent();

            // Foreign keys
            $table->foreign('permanent_address_id')
                  ->references('address_id')->on('addresses');
            $table->foreign('current_address_id')
                  ->references('address_id')->on('addresses');
            $table->foreign('registered_by')
                  ->references('staff_id')->on('staff');
            // ON DELETE: RESTRICT (default) — schema.sql does not specify CASCADE

            // Indexes from original schema
            $table->index('mobile_no', 'idx_clients_mobile');
            $table->index('client_type', 'idx_clients_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
