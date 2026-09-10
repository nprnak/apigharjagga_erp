<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->id('payslip_id');
            $table->unsignedBigInteger('payroll_run_id');
            $table->unsignedBigInteger('staff_id');
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('net_pay', 12, 2)->default(0);

            $table->foreign('payroll_run_id')->references('payroll_run_id')->on('payroll_runs')->cascadeOnDelete();
            $table->foreign('staff_id')->references('staff_id')->on('staff');
            $table->unique(['payroll_run_id', 'staff_id'], 'uniq_payslip_run_staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};
