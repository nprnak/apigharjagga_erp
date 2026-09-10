<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id('image_id');
            $table->unsignedBigInteger('album_id');
            $table->string('image_path', 255);
            $table->string('caption', 255)->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);

            $table->foreign('album_id')->references('album_id')->on('gallery_albums')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
