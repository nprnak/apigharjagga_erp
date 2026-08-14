<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: service_order_items
     * Purpose: Line-items on a service order — links orders to service types.
     * Dependency order: #34 — references service_orders(order_id) [CASCADE],
     *                           service_types(service_type_id).
     */
    public function up(): void
    {
        Schema::create('service_order_items', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY — original PK column is "id"
            $table->bigIncrements('id');

            $table->unsignedBigInteger('order_id');

            // service_types.service_type_id is SMALLSERIAL (unsigned SMALLINT)
            $table->unsignedSmallInteger('service_type_id');

            // Composite unique constraint from original schema
            $table->unique(['order_id', 'service_type_id']);

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('service_orders')
                  ->cascadeOnDelete();

            $table->foreign('service_type_id')
                  ->references('service_type_id')
                  ->on('service_types');
            // ON DELETE: RESTRICT (default)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_order_items');
    }
};
