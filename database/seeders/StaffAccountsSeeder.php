<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Sample login accounts for the two limited-access admin-panel staff roles
 * (see RolePermissionSeeder::seedStaffGuardRoles()). Safe to re-run —
 * updateOrCreate + idempotent role assignment.
 */
class StaffAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $this->makeStaff(
            email: config('app.site_inspection_engineer_email', 'engineer@apigharjagga.com'),
            name: 'Site Inspection Engineer',
            password: config('app.site_inspection_engineer_password', 'password'),
            role: 'site_inspection_engineer',
        );

        $this->makeStaff(
            email: config('app.valuation_officer_email', 'valuation@apigharjagga.com'),
            name: 'Valuation Officer',
            password: config('app.valuation_officer_password', 'password'),
            role: 'valuation_officer',
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function makeStaff(string $email, string $name, string $password, string $role): void
    {
        // Deliberately don't touch the legacy `role` column here — it's a
        // web-guard-only enum('user','admin') and these staff roles live
        // solely under the "admin" guard (unrelated to User::booted()'s
        // web-guard sync). New rows fall back to its DB default ('user'),
        // which just means they can also sign into the public/user side as
        // an ordinary buyer/seller — harmless, and not restricted by this
        // role.
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ],
        );

        $staffRole = Role::findOrCreate($role, 'admin');

        if (! $user->hasRole($staffRole)) {
            $user->assignRole($staffRole);
        }

        $this->command?->info("{$role} ready: {$email}");
    }
}
