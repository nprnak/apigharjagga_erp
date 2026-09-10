<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets an Agent-type web login manage an Owner's properties. Scoped to
     * owner_client_id (not the owner's own user_id) because an owner may
     * have no web login at all — Client is the only ownership reference
     * guaranteed to exist regardless of how the owner was registered.
     */
    public function up(): void
    {
        Schema::create('power_of_attorneys', function (Blueprint $table) {
            $table->id('poa_id');
            $table->unsignedBigInteger('agent_user_id');
            $table->unsignedBigInteger('owner_client_id');
            $table->string('document_path', 255);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('verified_by_staff_id')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('agent_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('owner_client_id')->references('client_id')->on('clients');
            $table->foreign('verified_by_staff_id')->references('staff_id')->on('staff');
            $table->unique(['agent_user_id', 'owner_client_id'], 'uniq_poa_agent_owner');
            $table->index('status', 'idx_poa_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('power_of_attorneys');
    }
};
