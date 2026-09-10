<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_openings', function (Blueprint $table) {
            $table->id('job_id');
            $table->string('title', 200);
            $table->string('department', 100)->nullable();
            $table->string('location', 150)->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship'])->default('full_time');
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->date('posted_date')->nullable();
            $table->date('closing_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_openings');
    }
};
