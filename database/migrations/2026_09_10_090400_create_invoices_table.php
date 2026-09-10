<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->string('invoice_no', 30)->unique();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->unsignedBigInteger('agreement_id')->nullable();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by_staff_id')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('client_id')->on('clients');
            $table->foreign('property_id')->references('property_id')->on('properties');
            $table->foreign('agreement_id')->references('agreement_id')->on('agreements');
            $table->foreign('created_by_staff_id')->references('staff_id')->on('staff');
            $table->index('status', 'idx_invoices_status');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `invoices` ADD CONSTRAINT `chk_invoices_total_positive` CHECK (`total_amount` >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
