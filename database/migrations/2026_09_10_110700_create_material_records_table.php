<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_records', function (Blueprint $table) {
            $table->id('material_id');
            $table->unsignedBigInteger('project_id');
            $table->string('material_name', 150);
            $table->decimal('quantity', 12, 2)->default(0);
            $table->string('unit', 30)->nullable();
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->decimal('total_cost', 14, 2)->default(0);
            $table->string('supplier', 150)->nullable();
            $table->date('received_date')->nullable();
            $table->unsignedBigInteger('recorded_by_staff_id')->nullable();

            $table->foreign('project_id')->references('project_id')->on('projects')->cascadeOnDelete();
            $table->foreign('recorded_by_staff_id')->references('staff_id')->on('staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_records');
    }
};
