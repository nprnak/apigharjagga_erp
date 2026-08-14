<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: roles
     * Purpose: Lookup table for staff roles used across the platform.
     * Dependency order: #1 — no foreign keys.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            // SMALLSERIAL PRIMARY KEY → smallIncrements
            $table->smallIncrements('role_id');

            // Admin, Manager, Engineer, Survey Officer,
            // Valuation Officer, Finance, Customer Support
            $table->string('role_name', 50)->unique();
        });
    }

    /**
     * Reverse the migrations.
     * No circular FK references — plain dropIfExists is safe.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
