<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id('review_id');
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('reviewer_staff_id')->nullable();
            $table->string('review_period', 20)->comment('e.g. "2026 Q1" or "2026-04"');
            $table->unsignedTinyInteger('rating')->comment('1-5');
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->date('review_date');
            $table->timestamps();

            $table->foreign('staff_id')->references('staff_id')->on('staff');
            $table->foreign('reviewer_staff_id')->references('staff_id')->on('staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
};
