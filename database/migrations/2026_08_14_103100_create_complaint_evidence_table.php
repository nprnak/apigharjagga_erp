<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: complaint_evidence
     * Purpose: Evidence files attached to a complaint.
     * Dependency order: #32 — references complaints(complaint_id) [CASCADE].
     */
    public function up(): void
    {
        Schema::create('complaint_evidence', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY — original PK column is "id"
            $table->bigIncrements('id');

            $table->unsignedBigInteger('complaint_id');

            // CHECK (evidence_type IN (...)) → enum; nullable
            $table->enum('evidence_type', [
                'photo',
                'screenshot',
                'agreement_copy',
                'payment_receipt',
                'other',
            ])->nullable();

            $table->text('file_ref');

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('complaint_id')
                  ->references('complaint_id')
                  ->on('complaints')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_evidence');
    }
};
