<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractors', function (Blueprint $table) {
            $table->id('contractor_id');
            $table->string('name', 150);
            $table->string('contact_person', 150)->nullable();
            $table->string('mobile_no', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('specialization', 150)->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractors');
    }
};
