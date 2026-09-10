<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chart of accounts. Every finance_transaction_lines row posts against
     * one of these. account_type drives which reports an account shows up
     * in: asset/liability/equity feed the Balance Sheet, income/expense
     * feed the Profit & Loss. is_system protects the accounts the app
     * posts to automatically (Cash, income categories mirroring
     * payment_receipts.purpose) from being deleted from the UI.
     */
    public function up(): void
    {
        Schema::create('finance_accounts', function (Blueprint $table) {
            $table->id('account_id');
            $table->string('account_code', 20)->unique();
            $table->string('account_name', 150);
            $table->enum('account_type', ['asset', 'liability', 'equity', 'income', 'expense']);
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_accounts');
    }
};
