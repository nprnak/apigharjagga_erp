<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Admin',
            'Manager',
            'Engineer',
            'Survey Officer',
            'Valuation Officer',
            'Finance',
            'Customer Support',
        ];

        foreach ($roles as $roleName) {
            DB::table('roles')->updateOrInsert(['role_name' => $roleName]);
        }
    }
}
