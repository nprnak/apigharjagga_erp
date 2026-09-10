<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSeeder extends Seeder
{
    /**
     * Creates (or updates) the default super admin account.
     *
     * A super admin has every capability a regular admin has, PLUS access
     * to the admin panel's "Site Settings" page — which is deliberately
     * withheld from the plain "admin" role (see RolePermissionSeeder).
     *
     * Change the email/password below (or override via env) before running
     * this in a real environment; it's safe to re-run since it uses
     * updateOrCreate + role syncing.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $email = config('app.super_admin_email', 'superadmin@apigharjagga.com');
        $password = config('app.super_admin_password', 'password');

        $superAdmin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'role' => 'admin',
            ],
        );

        // "admin" (web guard) is set via the `role` column above and synced
        // automatically by App\Models\User::booted(). "super_admin" (admin
        // guard) is Filament Shield's own role and must be assigned directly.
        $superAdminRoleName = config('filament-shield.super_admin.name', 'super_admin');
        $superAdminRole = Role::findOrCreate($superAdminRoleName, 'admin');

        if (! $superAdmin->hasRole($superAdminRole)) {
            $superAdmin->assignRole($superAdminRole);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command?->info("Super admin ready: {$email}");
    }
}
