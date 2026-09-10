<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id('application_id');
            $table->unsignedBigInteger('job_id');
            $table->string('applicant_name', 150);
            $table->string('email', 150);
            $table->string('phone', 20)->nullable();
            $table->string('resume_path', 255)->nullable();
            $table->text('cover_letter')->nullable();
            $table->enum('status', ['received', 'reviewed', 'shortlisted', 'rejected', 'hired'])->default('received');
            $table->timestamp('applied_at')->useCurrent();

            $table->foreign('job_id')->references('job_id')->on('job_openings')->cascadeOnDelete();
            $table->index('status', 'idx_job_applications_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
