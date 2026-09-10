<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per pay period (e.g. "2026-04"). Generating payslips creates
     * one Payslip per active Staff member; finalizing posts one Salaries &
     * Wages expense transaction to the Finance ledger for the total payout.
     */
    public function up(): void
    {
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id('payroll_run_id');
            $table->string('period_month', 7)->unique()->comment('YYYY-MM');
            $table->enum('status', ['draft', 'finalized'])->default('draft');
            $table->timestamp('finalized_at')->nullable();
            $table->unsignedBigInteger('created_by_staff_id')->nullable();
            $table->timestamps();

            $table->foreign('created_by_staff_id')->references('staff_id')->on('staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_runs');
    }
};
