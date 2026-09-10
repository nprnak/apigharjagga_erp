<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One allocation per account per period. Actual-vs-budget is computed
     * at read time from finance_transaction_lines, not stored here.
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id('budget_id');
            $table->unsignedBigInteger('account_id');
            $table->string('fiscal_year', 20);
            $table->enum('period_type', ['annual', 'monthly'])->default('annual');
            $table->string('period_label', 20)->nullable()->comment('e.g. "2026-01" for a monthly budget; null for annual');
            $table->decimal('allocated_amount', 14, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('account_id')->on('finance_accounts');
            $table->unique(['account_id', 'fiscal_year', 'period_type', 'period_label'], 'uniq_budget_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
