<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id('member_id');
            $table->string('name', 150);
            $table->string('designation', 150)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->text('bio')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
