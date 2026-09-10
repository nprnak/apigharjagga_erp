<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per accounting event (a receipt, a voucher payment, a manual
     * journal entry). The actual debit/credit postings live in
     * finance_transaction_lines — this table is the header. reference_type
     * + reference_id optionally point back at the source record (e.g.
     * 'payment_receipt' / receipts.receipt_id) for transactions the app
     * posts automatically, so they can be traced back to their Annex form.
     */
    public function up(): void
    {
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->date('transaction_date');
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description', 255)->nullable();
            $table->unsignedBigInteger('created_by_staff_id')->nullable();
            $table->timestamps();

            $table->foreign('created_by_staff_id')->references('staff_id')->on('staff');
            $table->index(['reference_type', 'reference_id'], 'idx_finance_transactions_reference');
            $table->index('transaction_date', 'idx_finance_transactions_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};
