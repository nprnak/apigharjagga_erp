<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: staff
     * Dependency order: #2 — references roles(role_id).
     */
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('staff_id');

            // role_id references roles.role_id which is a SMALLSERIAL (unsigned SMALLINT)
            $table->unsignedSmallInteger('role_id');

            $table->string('full_name', 150);
            $table->string('designation', 100)->nullable();
            $table->string('mobile_no', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->boolean('is_active')->default(true);

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('created_at')->useCurrent();

            // Foreign key: roles.role_id is smallIncrements (unsigned SMALLINT)
            $table->foreign('role_id')
                  ->references('role_id')
                  ->on('roles');
            // ON DELETE: RESTRICT (default) — schema.sql does not specify CASCADE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
