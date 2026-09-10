<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_contracts', function (Blueprint $table) {
            $table->id('contract_id');
            $table->unsignedBigInteger('staff_id');
            $table->enum('contract_type', ['permanent', 'fixed_term', 'probation', 'part_time']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->enum('status', ['active', 'expired', 'terminated'])->default('active');
            $table->timestamps();

            $table->foreign('staff_id')->references('staff_id')->on('staff')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_contracts');
    }
};
