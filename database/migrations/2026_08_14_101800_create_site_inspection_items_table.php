<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: site_inspection_items
     * Purpose: Normalized checklist rows for site inspection (land or building items).
     * Dependency order: #19 — references site_inspections(inspection_id) [CASCADE].
     */
    public function up(): void
    {
        Schema::create('site_inspection_items', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('item_id');

            $table->unsignedBigInteger('inspection_id');

            // CHECK (category IN ('land','building')) → enum; NOT NULL
            $table->enum('category', ['land', 'building']);

            // e.g. "Road access available"
            $table->string('item_name', 150)
                  ->comment('Checklist item name, e.g. "Road access available"');

            $table->boolean('is_verified')->nullable();
            $table->string('remarks', 300)->nullable();

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('inspection_id')
                  ->references('inspection_id')
                  ->on('site_inspections')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_inspection_items');
    }
};
