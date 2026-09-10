<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Payment Tracking" for construction projects reuses the existing
     * PaymentVoucher -> Finance ledger flow instead of a second,
     * disconnected payment concept — a voucher just optionally links back
     * to the project it was spent on.
     */
    public function up(): void
    {
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->after('account_id');
            $table->foreign('project_id')->references('project_id')->on('projects');
        });
    }

    public function down(): void
    {
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};
