<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Construction-phase quality inspections — distinct from the
     * pre-listing Annex-D site_inspections table.
     */
    public function up(): void
    {
        Schema::create('project_inspection_reports', function (Blueprint $table) {
            $table->id('inspection_id');
            $table->unsignedBigInteger('project_id');
            $table->date('inspection_date');
            $table->unsignedBigInteger('inspected_by_staff_id')->nullable();
            $table->text('findings')->nullable();
            $table->enum('result', ['pass', 'fail', 'needs_correction'])->default('pass');

            $table->foreign('project_id')->references('project_id')->on('projects')->cascadeOnDelete();
            $table->foreign('inspected_by_staff_id')->references('staff_id')->on('staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_inspection_reports');
    }
};
