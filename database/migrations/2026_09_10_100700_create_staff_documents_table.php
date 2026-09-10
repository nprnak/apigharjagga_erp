<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_documents', function (Blueprint $table) {
            $table->id('document_id');
            $table->unsignedBigInteger('staff_id');
            $table->string('document_name', 150);
            $table->string('file_path', 255);
            $table->timestamp('uploaded_at')->useCurrent();

            $table->foreign('staff_id')->references('staff_id')->on('staff')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_documents');
    }
};
