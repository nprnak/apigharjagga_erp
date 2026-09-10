<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Outgoing payments (salaries, rent, marketing spend, ...) — the
     * expense-side counterpart to payment_receipts (Annex-I, incoming).
     * Approving a voucher posts a finance_transaction crediting Cash/Bank
     * and debiting the chosen expense account.
     */
    public function up(): void
    {
        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->id('voucher_id');
            $table->string('voucher_no', 30)->unique();
            $table->date('voucher_date');
            $table->string('payee_name', 150);
            $table->string('purpose', 255);
            $table->unsignedBigInteger('account_id')->comment('Expense category this payment is charged to');
            $table->decimal('amount', 14, 2);
            $table->enum('mode_of_payment', ['cash', 'cheque'])->default('cash');
            $table->string('cheque_no', 30)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->date('cheque_date')->nullable();
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->unsignedBigInteger('approved_by_staff_id')->nullable();
            $table->unsignedBigInteger('created_by_staff_id')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('account_id')->on('finance_accounts');
            $table->foreign('approved_by_staff_id')->references('staff_id')->on('staff');
            $table->foreign('created_by_staff_id')->references('staff_id')->on('staff');
            $table->index('status', 'idx_payment_vouchers_status');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `payment_vouchers` ADD CONSTRAINT `chk_vouchers_amount_positive` CHECK (`amount` > 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_vouchers');
    }
};
