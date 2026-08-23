<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: provinces
     * Purpose: Top-level administrative division of Nepal (7 provinces).
     *          Used as a lookup/reference table for address dropdowns.
     */
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->comment('Province name in English');
            $table->string('name_np', 150)->nullable()->comment('Province name in Nepali (Devanagari)');
            $table->string('code', 10)->nullable()->comment('Short code, e.g. P1–P7');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
