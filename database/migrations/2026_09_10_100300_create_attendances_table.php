<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id('attendance_id');
            $table->unsignedBigInteger('staff_id');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'half_day', 'on_leave', 'holiday'])->default('present');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('notes', 255)->nullable();

            $table->foreign('staff_id')->references('staff_id')->on('staff');
            $table->unique(['staff_id', 'attendance_date'], 'uniq_attendance_staff_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
