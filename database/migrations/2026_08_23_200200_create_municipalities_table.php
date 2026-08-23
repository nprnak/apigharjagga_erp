<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: municipalities
     * Purpose: 753 local government units of Nepal.
     *          Covers Metropolitan Cities, Sub-Metropolitan Cities,
     *          Municipalities, and Rural Municipalities.
     *          Each belongs to a district.
     */
    public function up(): void
    {
        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->string('name', 150)->comment('Municipality name in English');
            $table->string('name_np', 200)->nullable()->comment('Municipality name in Nepali (Devanagari)');

            // Type enum following Nepal's classification
            $table->enum('type', [
                'metropolitan_city',
                'sub_metropolitan_city',
                'municipality',
                'rural_municipality',
            ])->default('municipality')->comment('Type of local government unit');

            $table->unsignedTinyInteger('total_wards')->default(0)
                  ->comment('Total number of wards in this local government');

            $table->timestamps();

            $table->unique(['district_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipalities');
    }
};
