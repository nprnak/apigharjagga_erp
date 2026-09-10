<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id('leave_type_id');
            $table->string('name', 50)->unique();
            $table->unsignedSmallInteger('default_days_per_year')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
