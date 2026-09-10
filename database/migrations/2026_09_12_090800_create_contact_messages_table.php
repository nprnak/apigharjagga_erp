<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * General "Contact Us" submissions — distinct from property_inquiries,
     * which always requires a specific property_id and serves marketplace
     * buyer/tenant leads rather than general enquiries.
     */
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id('message_id');
            $table->string('name', 150);
            $table->string('email', 150);
            $table->string('phone', 20)->nullable();
            $table->string('subject', 200)->nullable();
            $table->text('message');
            $table->enum('status', ['new', 'contacted', 'closed'])->default('new');
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index('status', 'idx_contact_messages_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
