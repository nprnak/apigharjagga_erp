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
     * Runs the lookup/reference table seeders in dependency order, plus the
     * required admin and demo user accounts. All seeders are idempotent
     * (upsert-based) and safe to re-run.
     */
    public function run(): void
    {
        $this->call([
            ProvinceSeeder::class,              // provinces → districts → municipalities → wards (no dependencies)
            RolesSeeder::class,                 // roles (no dependencies)
            DocumentTypesSeeder::class,          // document_types (no dependencies)
            PropertyFeatureTypesSeeder::class,   // property_feature_types (no dependencies)
            ServiceTypesSeeder::class,           // service_types (no dependencies)
            UserSeeder::class,                   // required accounts for every user level
            UserEightPropertySeeder::class,     // sample listing for user 8
        ]);
    }
}
