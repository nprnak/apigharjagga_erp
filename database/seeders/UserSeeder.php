<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seeds one login account for every configured user level.
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
                'name' => 'API GharJagga Manager',
                'email' => 'manager@apigharjagga.com',
                'password' => 'Manager@123',
                'role' => 'admin',
                'staff_role' => 'Manager',
            ],
            [
                'name' => 'API GharJagga Engineer',
                'email' => 'engineer@apigharjagga.com',
                'password' => 'Engineer@123',
                'role' => 'admin',
                'staff_role' => 'Engineer',
            ],
            [
                'name' => 'API GharJagga Survey Officer',
                'email' => 'survey@apigharjagga.com',
                'password' => 'Survey@123',
                'role' => 'admin',
                'staff_role' => 'Survey Officer',
            ],
            [
                'name' => 'API GharJagga Valuation Officer',
                'email' => 'valuation@apigharjagga.com',
                'password' => 'Valuation@123',
                'role' => 'admin',
                'staff_role' => 'Valuation Officer',
            ],
            [
                'name' => 'API GharJagga Finance',
                'email' => 'finance@apigharjagga.com',
                'password' => 'Finance@123',
                'role' => 'admin',
                'staff_role' => 'Finance',
            ],
            [
                'name' => 'API GharJagga Customer Support',
                'email' => 'support@apigharjagga.com',
                'password' => 'Support@123',
                'role' => 'admin',
                'staff_role' => 'Customer Support',
            ],
            [
                'name' => 'API GharJagga User',
                'email' => 'user@apigharjagga.com',
                'password' => 'User@123',
                'role' => 'user',
                'staff_role' => null,
            ],
        ];

        foreach ($accounts as $account) {
            $roleId = $account['staff_role'] === null
                ? null
                : Role::where('role_name', $account['staff_role'])->value('role_id');

            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($account['password']),
                    'role' => $account['role'],
                    'role_id' => $roleId,
                    'email_verified_at' => now(),
                ],
            );

            $this->command->info("User created: {$account['email']} / {$account['password']}");
        }
    }
}
