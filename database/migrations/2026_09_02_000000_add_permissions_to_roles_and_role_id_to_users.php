<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds dynamic per-role permissions and links users to a staff role,
     * so admin-panel resource access is driven by data instead of hardcoded checks.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('role_name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedSmallInteger('role_id')->nullable()->after('role');
            $table->foreign('role_id')->references('role_id')->on('roles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};
