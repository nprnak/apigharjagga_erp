<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Runs the four lookup/reference table seeders in dependency order.
     * All seeders are idempotent (upsert-based) and safe to re-run.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,        // Spatie roles/permissions (admin, user) — no dependencies
            ProvinceSeeder::class,              // provinces → districts → municipalities → wards (no dependencies)
            RolesSeeder::class,                 // staff job-title lookup (no dependencies)
            DocumentTypesSeeder::class,          // document_types (no dependencies)
            PropertyFeatureTypesSeeder::class,   // property_feature_types (no dependencies)
            ServiceTypesSeeder::class,           // service_types (no dependencies)
            UserEightPropertySeeder::class,     // sample listing for user 8
            StaffAccountsSeeder::class,          // sample site_inspection_engineer / valuation_officer logins
        ]);
    }
}
