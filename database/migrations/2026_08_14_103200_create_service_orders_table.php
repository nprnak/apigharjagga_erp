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
     * Table: service_orders
     * Source form: Annex H
     * Dependency order: #33 — references clients(client_id), properties(property_id).
     */
    public function up(): void
    {
        Schema::create('service_orders', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('order_id');

            // "Service Order No."
            $table->string('order_no', 30)->unique()
                  ->comment('Service Order No.');

            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('property_id');

            // DATE NOT NULL DEFAULT CURRENT_DATE
            // MySQL 8.0.13+: expression defaults require parentheses
            $table->date('order_date')
                  ->default(DB::raw('(CURRENT_DATE)'));

            // CHECK (status IN (...)) → enum; NOT NULL with default
            $table->enum('status', ['open', 'in_progress', 'completed', 'cancelled'])
                  ->default('open');

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
            // ON DELETE: RESTRICT (default) — schema.sql does not specify CASCADE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
