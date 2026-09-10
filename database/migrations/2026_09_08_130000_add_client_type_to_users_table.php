<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drives which experience a logged-in web account sees in the customer
     * User panel (Owner / Buyer / Investor / Tenant / Agent). Deliberately
     * separate from `clients.client_type`: a `Client` is a staff-entered
     * business record (Annex-F) that may never have a web login at all —
     * `properties` already tracks `owner_client_id` and `user_id`
     * independently for the same reason. This column answers a narrower
     * question: which portal dashboard does this account get.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('client_type', ['owner', 'buyer', 'investor', 'tenant', 'agent'])
                ->nullable()
                ->after('role');
        });

        // Existing accounts predate this field. The User panel's only
        // built-in resources today (My Properties, My Valuation Requests)
        // are owner-shaped, so backfill to 'owner' rather than leaving
        // existing users with no portal at all. New signups choose explicitly.
        DB::table('users')->where('role', 'user')->whereNull('client_type')->update(['client_type' => 'owner']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('client_type');
        });
    }
};
