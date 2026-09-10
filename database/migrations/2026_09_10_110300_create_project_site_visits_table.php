<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_site_visits', function (Blueprint $table) {
            $table->id('site_visit_id');
            $table->unsignedBigInteger('project_id');
            $table->date('visit_date');
            $table->unsignedBigInteger('visited_by_staff_id')->nullable();
            $table->text('notes')->nullable();
            $table->json('photo_paths')->nullable();

            $table->foreign('project_id')->references('project_id')->on('projects')->cascadeOnDelete();
            $table->foreign('visited_by_staff_id')->references('staff_id')->on('staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_site_visits');
    }
};
