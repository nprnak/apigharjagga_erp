<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: wards
     * Purpose: Individual wards for every municipality/rural municipality in Nepal.
     *          Total: ~6,743 wards nationwide.
     *          Each ward belongs to one municipality.
     */
    public function up(): void
    {
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipality_id')->constrained('municipalities')->cascadeOnDelete();
            $table->unsignedTinyInteger('ward_number')->comment('Ward number within the municipality');
            $table->timestamps();

            $table->unique(['municipality_id', 'ward_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};
