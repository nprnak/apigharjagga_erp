<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Double-entry postings. Every finance_transaction must have lines that
     * balance (sum(debit) = sum(credit)) — enforced at the application
     * layer in FinanceTransaction::postBalanced(), since a cross-row SUM
     * check isn't expressible as a column CHECK constraint. A line is
     * either a debit or a credit, never both.
     */
    public function up(): void
    {
        Schema::create('finance_transaction_lines', function (Blueprint $table) {
            $table->id('line_id');
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('account_id');
            $table->decimal('debit', 14, 2)->default(0);
            $table->decimal('credit', 14, 2)->default(0);

            $table->foreign('transaction_id')->references('transaction_id')->on('finance_transactions')->cascadeOnDelete();
            $table->foreign('account_id')->references('account_id')->on('finance_accounts');
            $table->index('account_id', 'idx_finance_transaction_lines_account');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                'ALTER TABLE `finance_transaction_lines`
                 ADD CONSTRAINT `chk_finance_lines_one_sided`
                 CHECK (
                     (`debit` > 0 AND `credit` = 0) OR (`credit` > 0 AND `debit` = 0)
                 )'
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_transaction_lines');
    }
};
