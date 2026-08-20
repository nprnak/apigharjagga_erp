<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table: property_inquiries
     * Buyer/tenant leads submitted from the public marketplace property detail page.
     */
    public function up(): void
    {
        Schema::create('property_inquiries', function (Blueprint $table) {
            $table->bigIncrements('inquiry_id');

            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('listing_id')->nullable();

            $table->string('name', 150);
            $table->string('phone', 20);
            $table->string('email', 150)->nullable();
            $table->text('message')->nullable();

            $table->enum('status', ['new', 'contacted', 'closed'])->default('new');
            $table->text('admin_note')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('property_id')
                ->references('property_id')
                ->on('properties')
                ->cascadeOnDelete();

            $table->foreign('listing_id')
                ->references('listing_id')
                ->on('property_listings')
                ->nullOnDelete();

            $table->index('status', 'idx_inquiries_status');
            $table->index('property_id', 'idx_inquiries_property');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_inquiries');
    }
};
