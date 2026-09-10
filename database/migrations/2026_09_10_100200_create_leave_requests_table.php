<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id('leave_request_id');
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('total_days');
            $table->string('reason', 255)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by_staff_id')->nullable();
            $table->timestamp('applied_at')->useCurrent();

            $table->foreign('staff_id')->references('staff_id')->on('staff');
            $table->foreign('leave_type_id')->references('leave_type_id')->on('leave_types');
            $table->foreign('approved_by_staff_id')->references('staff_id')->on('staff');
            $table->index('status', 'idx_leave_requests_status');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `leave_requests` ADD CONSTRAINT `chk_leave_dates` CHECK (`end_date` >= `start_date`)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
