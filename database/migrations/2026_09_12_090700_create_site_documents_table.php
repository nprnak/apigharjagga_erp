<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Public "Download Documents" library — brochures, forms, policies —
     * distinct from the client/staff-facing document_types checklist used
     * by KYC and valuation intake.
     */
    public function up(): void
    {
        Schema::create('site_documents', function (Blueprint $table) {
            $table->id('document_id');
            $table->string('title', 200);
            $table->string('category', 100)->nullable();
            $table->string('file_path', 255);
            $table->boolean('is_public')->default(true);
            $table->timestamp('uploaded_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_documents');
    }
};
