<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Engineering/construction projects — distinct from the pre-listing
     * Annex-D site_inspections. Client Assignment and Engineer Assignment
     * (from the proposal's Engineering Project Management feature list) are
     * client_id and assigned_engineer_staff_id respectively.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id('project_id');
            $table->string('project_code', 30)->unique();
            $table->string('project_name', 200);
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->unsignedBigInteger('assigned_engineer_staff_id')->nullable();
            $table->enum('status', ['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'])->default('planning');
            $table->date('start_date')->nullable();
            $table->date('expected_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->text('description')->nullable();
            $table->string('completion_certificate_path', 255)->nullable();
            $table->unsignedBigInteger('created_by_staff_id')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('client_id')->on('clients');
            $table->foreign('property_id')->references('property_id')->on('properties');
            $table->foreign('assigned_engineer_staff_id')->references('staff_id')->on('staff');
            $table->foreign('created_by_staff_id')->references('staff_id')->on('staff');
            $table->index('status', 'idx_projects_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
