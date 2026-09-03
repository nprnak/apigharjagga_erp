<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Sets up both role "spaces" this app uses (see App\Models\User for why
     * they're split across two Spatie guards):
     *
     *  - "web" guard   — the legacy "admin"/"user" roles + custom, hand-named
     *                    permissions used by the marketplace site.
     *  - "admin" guard — Filament Shield's auto-generated resource/page/
     *                    widget permissions, scoped to the admin panel.
     *
     * This is intentionally re-runnable (findOrCreate/syncPermissions) so it
     * is safe to include in the normal DatabaseSeeder pipeline.
     *
     * To add more "web" guard roles/permissions later (without touching
     * code), use `php artisan tinker`:
     *
     *   Permission::findOrCreate('manage listings', 'web');
     *   Role::findOrCreate('manager', 'web')->givePermissionTo(['manage listings']);
     *   $user->assignRole($manager);
     *
     * To add more "admin" guard (Filament Shield) permissions, regenerate
     * after adding a new Resource/Page/Widget:
     *
     *   php artisan shield:generate --all --panel=admin
     *
     * ...then assign the new permissions to a role from the Roles screen in
     * the admin panel (Shield ships a full CRUD UI for this), or via tinker.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->seedWebGuardRoles();
        $this->seedAdminGuardShieldRoles();
        $this->seedStaffGuardRoles();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Legacy "web" guard roles: gate access to the two Filament panels and
     * the marketplace's own custom permission set.
     */
    protected function seedWebGuardRoles(): void
    {
        $permissions = [
            // Admin-facing permissions
            'access admin panel',
            'manage users',
            'manage properties',
            'approve kyc',
            'reject kyc',
            'view analytics',
            'manage clients',
            'manage complaints',
            'manage agreements',
            'manage valuation requests',

            // User-facing permissions
            'access user panel',
            'submit kyc',
            'manage own properties',
            'submit inquiries',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $adminRole = Role::findOrCreate('admin', 'web');
        $adminRole->syncPermissions(Permission::where('guard_name', 'web')->get());

        $userRole = Role::findOrCreate('user', 'web');
        $userRole->syncPermissions([
            'access user panel',
            'submit kyc',
            'manage own properties',
            'submit inquiries',
        ]);
    }

    /**
     * "admin" guard roles: drive per-resource authorization inside the admin
     * panel via Filament Shield's generated permissions/policies.
     *
     *  - "admin" gets every Shield permission EXCEPT the Site Settings page,
     *    which is reserved for "super_admin" only.
     *  - "super_admin" doesn't need explicit permissions: Shield configures
     *    a Gate::before() bypass for it (config/filament-shield.php ->
     *    super_admin), so it always passes every permission check.
     */
    protected function seedAdminGuardShieldRoles(): void
    {
        // Idempotent: (re-)discovers permissions/policies for every
        // Resource/Page/Widget registered on the admin panel. Safe to run
        // on every seed/deploy.
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
            '--no-interaction' => true,
        ]);

        $superAdminRoleName = config('filament-shield.super_admin.name', 'super_admin');

        $adminGuardRole = Role::findOrCreate('admin', 'admin');
        $adminGuardRole->syncPermissions(
            Permission::where('guard_name', 'admin')
                ->where('name', '!=', 'View:SiteSettings')
                ->get()
        );

        Role::findOrCreate($superAdminRoleName, 'admin');
    }

    /**
     * Limited-access staff roles (admin guard): they can sign into the admin
     * panel but only see the Property listing and the Site Inspection
     * workflow — nothing else (no Users, Clients, KYC, Site Settings, etc.).
     *
     *  - "site_inspection_engineer": can view properties, and create/view/
     *    update Site Inspection reports (fills the Annex-D checklist, then
     *    submits it to a valuation officer or admin).
     *  - "valuation_officer": can view properties, and view/update Site
     *    Inspection reports (reviews what engineers submit).
     *
     * Both roles are scoped further at the query level in
     * SiteInspectionResource::getEloquentQuery().
     */
    protected function seedStaffGuardRoles(): void
    {
        $propertyReadOnly = Permission::where('guard_name', 'admin')
            ->whereIn('name', ['ViewAny:Property', 'View:Property'])
            ->get();

        $siteInspectionEngineerPermissions = Permission::where('guard_name', 'admin')
            ->whereIn('name', ['ViewAny:SiteInspection', 'View:SiteInspection', 'Create:SiteInspection', 'Update:SiteInspection'])
            ->get();

        $valuationOfficerPermissions = Permission::where('guard_name', 'admin')
            ->whereIn('name', ['ViewAny:SiteInspection', 'View:SiteInspection', 'Update:SiteInspection'])
            ->get();

        $engineerRole = Role::findOrCreate('site_inspection_engineer', 'admin');
        $engineerRole->syncPermissions($propertyReadOnly->merge($siteInspectionEngineerPermissions));

        $valuationOfficerRole = Role::findOrCreate('valuation_officer', 'admin');
        $valuationOfficerRole->syncPermissions($propertyReadOnly->merge($valuationOfficerPermissions));
    }
}
