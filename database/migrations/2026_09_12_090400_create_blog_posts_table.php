<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id('post_id');
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->string('excerpt', 300)->nullable();
            $table->longText('content');
            $table->string('cover_image_path', 255)->nullable();
            $table->string('category', 100)->nullable();
            $table->unsignedBigInteger('author_staff_id')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('author_staff_id')->references('staff_id')->on('staff');
            $table->index('is_published', 'idx_blog_posts_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
