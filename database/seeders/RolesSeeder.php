<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Each role's permissions list drives which admin-panel resources it
     * can view ("{key}.view") or create/edit/delete ("{key}.manage").
     * '*' grants unrestricted access. Edit this map (or the Role model's
     * data) to change access dynamically without touching resource code.
     */
    public function run(): void
    {
        $roles = [
            'Admin' => ['*'],
            'Manager' => [
                'users.view', 'clients.view', 'clients.manage',
                'properties.view', 'properties.manage',
                'inquiries.view', 'inquiries.manage',
                'kyc.view', 'kyc.manage',
                'staff.view', 'staff.manage',
                'complaints.view', 'complaints.manage',
                'valuations.view', 'valuations.manage',
                'verifications.view', 'verifications.manage',
                'inspections.view', 'inspections.manage',
                'handovers.view', 'handovers.manage',
                'service_orders.view', 'service_orders.manage',
                'completions.view', 'completions.manage',
                'payments.view', 'payments.manage',
            ],
            'Engineer' => ['properties.view', 'properties.manage', 'inspections.view', 'inspections.manage'],
            'Survey Officer' => [
                'properties.view', 'clients.view',
                'valuations.view', 'valuations.manage',
                'inspections.view', 'inspections.manage',
            ],
            'Valuation Officer' => [
                'properties.view', 'clients.view',
                'valuations.view', 'valuations.manage',
                'verifications.view', 'verifications.manage',
            ],
            'Finance' => ['clients.view', 'payments.view', 'payments.manage'],
            'Customer Support' => [
                'inquiries.view', 'inquiries.manage', 'clients.view',
                'complaints.view', 'complaints.manage',
                'handovers.view', 'service_orders.view',
                'completions.view', 'completions.manage',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            DB::table('roles')->updateOrInsert(
                ['role_name' => $roleName],
                ['permissions' => json_encode($permissions)]
            );
        }
    }
}
