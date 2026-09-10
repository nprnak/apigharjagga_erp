<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Expands the original 7 generic staff roles into the full org-chart
     * role structure, renaming roles that map 1:1 onto a job title and
     * merging 'Engineer' into 'Site Engineer / Survey Officer' (the org
     * chart treats these as one field role). Renaming/merging by row
     * update — rather than delete + reseed — preserves every existing
     * `users.role_id` assignment. RolesSeeder fills in the permission
     * sets (including the newly added roles) immediately after.
     */
    public function up(): void
    {
        $renames = [
            'Manager' => 'General Manager',
            'Finance' => 'Finance Manager',
            'Customer Support' => 'Customer Support Officer',
            'Survey Officer' => 'Site Engineer / Survey Officer',
        ];

        foreach ($renames as $from => $to) {
            $existingTarget = DB::table('roles')->where('role_name', $to)->first();
            $source = DB::table('roles')->where('role_name', $from)->first();

            if (! $source) {
                continue;
            }

            if ($existingTarget) {
                // Target already exists (e.g. re-run): move users over, drop the duplicate source row.
                DB::table('users')->where('role_id', $source->role_id)->update(['role_id' => $existingTarget->role_id]);
                DB::table('roles')->where('role_id', $source->role_id)->delete();
            } else {
                DB::table('roles')->where('role_id', $source->role_id)->update(['role_name' => $to]);
            }
        }

        // 'Engineer' merges into 'Site Engineer / Survey Officer' rather than being renamed to it.
        $engineer = DB::table('roles')->where('role_name', 'Engineer')->first();
        $siteEngineer = DB::table('roles')->where('role_name', 'Site Engineer / Survey Officer')->first();

        if ($engineer && $siteEngineer) {
            DB::table('users')->where('role_id', $engineer->role_id)->update(['role_id' => $siteEngineer->role_id]);
            DB::table('roles')->where('role_id', $engineer->role_id)->delete();
        }
    }

    public function down(): void
    {
        // Renames/merges are not reversed: the original role names carried
        // coarser, already-superseded permission sets that RolesSeeder no
        // longer writes, so recreating the rows would leave them empty.
    }
};
