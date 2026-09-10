<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id('milestone_id');
            $table->unsignedBigInteger('project_id');
            $table->string('title', 200);
            $table->unsignedSmallInteger('sequence')->default(0);
            $table->date('due_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->enum('status', ['pending', 'completed', 'delayed'])->default('pending');

            $table->foreign('project_id')->references('project_id')->on('projects')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_milestones');
    }
};
