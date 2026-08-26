<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@apigharjagga.com'],
            [
                'name'              => 'API GharJagga Admin',
                'email'             => 'admin@apigharjagga.com',
                'password'          => Hash::make('Admin@123'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created: admin@apigharjagga.com / Admin@123');
    }
}
