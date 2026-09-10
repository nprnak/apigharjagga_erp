<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id('testimonial_id');
            $table->string('client_name', 150);
            $table->string('client_role', 150)->nullable()->comment('e.g. "Property Owner, Kathmandu"');
            $table->string('client_photo_path', 255)->nullable();
            $table->unsignedTinyInteger('rating')->default(5)->comment('1-5');
            $table->text('message');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
