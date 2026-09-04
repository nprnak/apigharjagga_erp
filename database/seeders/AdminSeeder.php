<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRoleId = Role::where('role_name', 'Admin')->value('role_id');

        User::updateOrCreate(
            ['email' => 'admin@apigharjagga.com'],
            [
                'name'              => 'API GharJagga Admin',
                'email'             => 'admin@apigharjagga.com',
                'password'          => Hash::make('Admin@123'),
                'role'              => 'admin',
                'role_id'           => $adminRoleId,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created: admin@apigharjagga.com / Admin@123');
    }
}
