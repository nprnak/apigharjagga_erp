<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: addresses
     * Purpose: Reusable address value-object following Nepal's administrative hierarchy:
     *          Province / District / Municipality / Ward.
     *          Appears on almost every Annex form (A, B, C, E, F, J, receipts, etc.)
     * Dependency order: #3 — no foreign keys.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('address_id');

            $table->string('province', 100)->nullable();
            $table->string('district', 100)->nullable();

            // Municipality or Rural Municipality
            $table->string('municipality', 150)->nullable()
                  ->comment('Municipality or Rural Municipality');

            $table->string('ward_no', 10)->nullable();
            $table->string('tole_locality', 150)->nullable();

            // Free-text fallback (ठेगाना)
            $table->text('full_address_text')->nullable()
                  ->comment('Free-text address fallback (ठेगाना)');

            // NUMERIC(10,7) → decimal(10, 7)
            $table->decimal('gps_lat', 10, 7)->nullable();
            $table->decimal('gps_lng', 10, 7)->nullable();

            $table->boolean('gps_verified')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
