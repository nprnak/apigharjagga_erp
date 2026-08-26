<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds nullable FK reference columns to the existing addresses table.
     * The original string columns (province, district, municipality, ward_no)
     * are preserved for backward compatibility.
     *
     * New columns allow structured FK relationships for dropdown-driven forms,
     * validation, and query filtering by location.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->foreignId('province_id')
                  ->nullable()
                  ->after('province')
                  ->constrained('provinces')
                  ->nullOnDelete()
                  ->comment('FK to provinces — set when address is created via structured dropdown');

            $table->foreignId('district_id')
                  ->nullable()
                  ->after('district')
                  ->constrained('districts')
                  ->nullOnDelete()
                  ->comment('FK to districts');

            $table->foreignId('municipality_id')
                  ->nullable()
                  ->after('municipality')
                  ->constrained('municipalities')
                  ->nullOnDelete()
                  ->comment('FK to municipalities (includes rural municipalities / metro / sub-metro)');

            $table->foreignId('ward_id')
                  ->nullable()
                  ->after('ward_no')
                  ->constrained('wards')
                  ->nullOnDelete()
                  ->comment('FK to wards');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['municipality_id']);
            $table->dropForeign(['ward_id']);

            $table->dropColumn(['province_id', 'district_id', 'municipality_id', 'ward_id']);
        });
    }
};
