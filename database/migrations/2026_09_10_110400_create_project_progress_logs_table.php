<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_progress_logs', function (Blueprint $table) {
            $table->id('progress_id');
            $table->unsignedBigInteger('project_id');
            $table->date('log_date');
            $table->unsignedTinyInteger('percent_complete')->default(0);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('logged_by_staff_id')->nullable();

            $table->foreign('project_id')->references('project_id')->on('projects')->cascadeOnDelete();
            $table->foreign('logged_by_staff_id')->references('staff_id')->on('staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_progress_logs');
    }
};
