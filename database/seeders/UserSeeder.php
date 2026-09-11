<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seeds one login account per staff role (org-chart structure — see
     * RolesSeeder) plus one non-staff demo account, so every role can be
     * logged into and verified directly.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'API GharJagga Admin',
                'email' => 'admin@apigharjagga.com',
                'password' => 'Admin@123',
                'role' => 'admin',
                'staff_role' => 'Admin',
            ],
            [
                'name' => 'API GharJagga Managing Director',
                'email' => 'md@apigharjagga.com',
                'password' => 'ManagingDirector@123',
                'role' => 'admin',
                'staff_role' => 'Managing Director',
            ],
            [
                'name' => 'API GharJagga General Manager',
                'email' => 'manager@apigharjagga.com',
                'password' => 'Manager@123',
                'role' => 'admin',
                'staff_role' => 'General Manager',
            ],
            [
                'name' => 'API GharJagga Finance Manager',
                'email' => 'finance@apigharjagga.com',
                'password' => 'Finance@123',
                'role' => 'admin',
                'staff_role' => 'Finance Manager',
            ],
            [
                'name' => 'API GharJagga Marketing Manager',
                'email' => 'marketing@apigharjagga.com',
                'password' => 'Marketing@123',
                'role' => 'admin',
                'staff_role' => 'Marketing Manager',
            ],
            [
                'name' => 'API GharJagga Technical Manager',
                'email' => 'technical@apigharjagga.com',
                'password' => 'Technical@123',
                'role' => 'admin',
                'staff_role' => 'Technical Manager',
            ],
            [
                'name' => 'API GharJagga Site Engineer',
                'email' => 'engineer@apigharjagga.com',
                'password' => 'Engineer@123',
                'role' => 'admin',
                'staff_role' => 'Site Engineer / Survey Officer',
            ],
            [
                'name' => 'API GharJagga Valuation Officer',
                'email' => 'valuation@apigharjagga.com',
                'password' => 'Valuation@123',
                'role' => 'admin',
                'staff_role' => 'Valuation Officer',
            ],
            [
                'name' => 'API GharJagga Survey Coordinator',
                'email' => 'survey@apigharjagga.com',
                'password' => 'Survey@123',
                'role' => 'admin',
                'staff_role' => 'Survey Coordinator',
            ],
            [
                'name' => 'API GharJagga Sales Representative',
                'email' => 'sales@apigharjagga.com',
                'password' => 'Sales@123',
                'role' => 'admin',
                'staff_role' => 'Sales/Brokerage Representative',
            ],
            [
                'name' => 'API GharJagga Customer Support',
                'email' => 'support@apigharjagga.com',
                'password' => 'Support@123',
                'role' => 'admin',
                'staff_role' => 'Customer Support Officer',
            ],
            [
                'name' => 'API GharJagga Receptionist',
                'email' => 'reception@apigharjagga.com',
                'password' => 'Reception@123',
                'role' => 'admin',
                'staff_role' => 'Receptionist / Front Desk Officer',
            ],
            [
                'name' => 'API GharJagga Document Officer',
                'email' => 'documents@apigharjagga.com',
                'password' => 'Documents@123',
                'role' => 'admin',
                'staff_role' => 'Document Officer / Legal Coordinator',
            ],
            [
                'name' => 'API GharJagga IT Support',
                'email' => 'itsupport@apigharjagga.com',
                'password' => 'ItSupport@123',
                'role' => 'admin',
                'staff_role' => 'IT Support / System Administrator',
            ],
            [
                'name' => 'API GharJagga KYC Approver',
                'email' => 'kycapprover@apigharjagga.com',
                'password' => 'KycApprover@123',
                'role' => 'admin',
                'staff_role' => 'KYC Approver',
            ],
            [
                'name' => 'API GharJagga Owner Demo',
                'email' => 'user@apigharjagga.com',
                'password' => 'User@123',
                'role' => 'user',
                'staff_role' => null,
                'client_type' => 'owner',
            ],
            [
                'name' => 'API GharJagga Buyer Demo',
                'email' => 'buyer@apigharjagga.com',
                'password' => 'Buyer@123',
                'role' => 'user',
                'staff_role' => null,
                'client_type' => 'buyer',
            ],
            [
                'name' => 'API GharJagga Investor Demo',
                'email' => 'investor@apigharjagga.com',
                'password' => 'Investor@123',
                'role' => 'user',
                'staff_role' => null,
                'client_type' => 'investor',
            ],
            [
                'name' => 'API GharJagga Tenant Demo',
                'email' => 'tenant@apigharjagga.com',
                'password' => 'Tenant@123',
                'role' => 'user',
                'staff_role' => null,
                'client_type' => 'tenant',
            ],
            [
                'name' => 'API GharJagga Agent Demo',
                'email' => 'agent@apigharjagga.com',
                'password' => 'Agent@123',
                'role' => 'user',
                'staff_role' => null,
                'client_type' => 'agent',
            ],
        ];

        foreach ($accounts as $account) {
            if ($account['staff_role'] === null) {
                $roleId = null;
            } else {
                $roleId = Role::where('role_name', $account['staff_role'])->value('role_id');

                // A null role_id on an 'admin' account falls back to full
                // (super admin) access — see User::hasPermission(). A typo'd
                // or renamed $staff_role here must fail loudly instead of
                // silently over-granting access.
                if ($roleId === null) {
                    throw new \RuntimeException("UserSeeder: no role named '{$account['staff_role']}' exists — check RolesSeeder.");
                }
            }

            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($account['password']),
                    'role' => $account['role'],
                    'role_id' => $roleId,
                    'client_type' => $account['client_type'] ?? null,
                    'email_verified_at' => now(),
                ],
            );

            $this->command->info("User created: {$account['email']} / {$account['password']}");
        }
    }
}
