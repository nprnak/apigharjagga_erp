<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Independent "show on public site" switch. Deliberately separate from
     * approval_status/status: an admin may approve a property but still want
     * to temporarily hide it from the marketplace (and vice versa, keep it
     * flagged listed while still reviewing other fields).
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('is_listed')->default(false)->after('approval_status');
        });

        // Backfill: properties that were already publicly visible under the
        // old (approval_status/status-only) rule stay visible after this change.
        \DB::table('properties')
            ->where(function ($query) {
                $query->whereIn('approval_status', ['approved', 'pending'])
                    ->orWhereIn('status', ['listed', 'draft']);
            })
            ->update(['is_listed' => true]);
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('is_listed');
        });
    }
};
