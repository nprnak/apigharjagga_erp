# RBAC Implementation Documentation

This document explains the Role-Based Access Control (RBAC) system implemented in this
project using [`spatie/laravel-permission`](https://spatie.be/docs/laravel-permission) +
[Filament Shield](https://filamentphp.com/plugins/bezhansalleh-shield), what changed to
implement it, the bugs that were found/fixed along the way, and — most importantly —
**how to add new roles and permissions dynamically, mostly without touching code.**

---

## 1. Goal

- Normal users (`role = user`) can only access the **user dashboard** (`/dashboard`,
  Filament "user" panel). Buyers/sellers register their own account, verify KYC, and
  list properties.
- Admins (`role = admin`) can only access the **admin dashboard** (`/admin`, Filament
  "admin" panel).
- Neither role can access the other's dashboard.
- A **super admin** role exists that can do everything an admin can, plus access a
  **Site Settings** page inside the admin panel that regular admins are explicitly
  denied.
- Roles/permissions should be **dynamic** — new roles and permissions can be introduced
  later, ideally without code changes/deploys at all.

---

## 2. Two role "spaces", one `User` model

This app shares a single `User` model/table across **two Laravel auth guards**:

- `web` — used by Breeze (marketplace login/register) **and** the Filament **user**
  panel (`/dashboard`).
- `admin` — used only by the Filament **admin** panel (`/admin`), with its own session
  identity so an admin and a normal user can be logged in simultaneously in the same
  browser.

Spatie's roles/permissions are namespaced by **guard**, and this app deliberately keeps
two separate, non-overlapping role spaces:

| Guard | Who manages it | What it's for |
|---|---|---|
| `web` | Hand-written (`RolePermissionSeeder`) | The legacy `admin`/`user` roles + a small custom permission list (`manage users`, `submit kyc`, ...). Drives `canAccessPanel()`, the public `/login` guard, `StatsOverview`, `KycVerificationObserver`. |
| `admin` | **Filament Shield** (auto-generated) | One permission per Filament resource action inside the admin panel (`ViewAny:Property`, `Update:Client`, `View:SiteSettings`, ...), plus Shield's own `super_admin` role, plus a mirrored `admin` role. Drives per-resource/page authorization *inside* the admin panel via Laravel policies. |

Filament flips Laravel's "default" auth guard to whatever `->authGuard(...)` a panel
declares for the duration of that panel's requests (`Auth::shouldUse('admin')` while
inside `/admin/*`). Because of that, **every place in this codebase that checks the
`web`-guard roles passes `'web'` explicitly** (`hasRole('admin', 'web')`,
`User::role('user', 'web')`, etc.) instead of relying on the ambient default guard —
that's what makes the two guards safe to mix on one model. See `app/Models/User.php`
for the full explanation in code.

---

## 3. What was installed / changed

### 3.1 Package installation

```bash
composer require spatie/laravel-permission
composer require bezhansalleh/filament-shield
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan shield:install admin --no-interaction
```

This published:

- `config/permission.php`
- `config/filament-shield.php`
- `database/migrations/2026_09_02_161304_create_permission_tables.php`
- Registered `BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()` on
  `AdminPanelProvider` (this is what gives the admin panel its **Roles** management
  screen, under `/admin/shield/roles`).

### 3.2 Table name collision fix

The project already had its own `roles` table (an unrelated staff job-title lookup
table used by `staff.role_id`, seeded by `RolesSeeder`). Spatie's default table name is
also `roles`, so it was renamed in `config/permission.php` to avoid a collision:

```php
// config/permission.php
'table_names' => [
    'roles' => 'permission_roles', // renamed from 'roles' to avoid collision
    'permissions' => 'permissions',
    'model_has_permissions' => 'model_has_permissions',
    'model_has_roles' => 'model_has_roles',
    'role_has_permissions' => 'role_has_permissions',
],
```

| Table | Purpose |
|---|---|
| `permission_roles` | Roles (`admin`/`web`, `user`/`web`, `admin`/`admin`, `super_admin`/`admin`, ...) — **renamed** from the package default `roles` |
| `permissions` | Individual permissions, one row per (name, guard) pair |
| `model_has_roles` | Pivot: which users have which roles |
| `model_has_permissions` | Pivot: direct user→permission grants (bypassing roles) |
| `role_has_permissions` | Pivot: which permissions belong to which role |

### 3.3 `app/Models/User.php`

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, HasRoles, Notifiable;

    // NOTE: getDefaultGuardName() is intentionally NOT overridden — see §2.
    // Every call site that needs the "web" guard's roles passes it explicitly.

    protected static function booted(): void
    {
        static::saved(function (User $user) {
            if (! $user->role || ! in_array($user->role, ['admin', 'user'], true)) {
                return;
            }

            // Keep the legacy `role` column in sync with the real "web" guard role.
            $role = \Spatie\Permission\Models\Role::findOrCreate($user->role, 'web');

            $user->roles
                ->where('guard_name', 'web')
                ->whereIn('name', ['admin', 'user'])
                ->reject(fn ($existing) => $existing->is($role))
                ->each(fn ($existing) => $user->removeRole($existing));

            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }

            // Admins also need the "admin" role under the "admin" guard, which is
            // what Filament Shield's generated permissions are checked against
            // *inside* the admin panel.
            if ($user->role === 'admin') {
                $adminGuardRole = \Spatie\Permission\Models\Role::findOrCreate('admin', 'admin');
                if (! $user->hasRole($adminGuardRole)) {
                    $user->assignRole($adminGuardRole);
                }
            } else {
                $adminGuardAdminRole = \Spatie\Permission\Models\Role::where('name', 'admin')
                    ->where('guard_name', 'admin')->first();
                if ($adminGuardAdminRole && $user->hasRole($adminGuardAdminRole)) {
                    $user->removeRole($adminGuardAdminRole);
                }
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->hasRole('admin', 'web') || $this->hasRole('super_admin', 'admin'),
            'user' => $this->hasRole('user', 'web'),
            default => false,
        };
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin', 'web') || $this->hasRole('super_admin', 'admin');
    }
}
```

**Why `getDefaultGuardName()` is *not* overridden (important, changed from an earlier
version of this doc):** An earlier iteration of this RBAC system hardcoded
`getDefaultGuardName()` to always return `'web'`, to work around Filament's
`Auth::shouldUse('admin')` behavior (see §10 gotcha #1 for the original bug). That fix
is incompatible with Filament Shield, which *relies on* the ambient default guard
switching to `admin` inside the admin panel to resolve its own generated permissions
(`ViewAny:User`, `View:SiteSettings`, ...) under the correct guard. The override was
therefore removed, and every call site that touches the `web`-guard roles now passes
`'web'` explicitly instead (see the table in §2 and §3.8).

**Why the `booted()` hook avoids `syncRoles()`:** `syncRoles()` detaches **all** of a
user's roles regardless of guard before reassigning. Since admins also carry an
`admin`-guard role, calling `syncRoles()` on every save would silently wipe out that
role (and any `super_admin` assignment) every time a user record is saved anywhere in
the app. The hook instead only touches `web`-guard `admin`/`user` roles, and separately
manages the mirrored `admin`-guard role.

**Why `canAccessPanel()` is the actual security boundary:** it's what Filament calls to
decide whether a logged-in user may open a given panel. Each panel requires the exact
matching role (or `super_admin` for the admin panel) — an admin does not have the
`user` role and is denied `/dashboard`; a normal user does not have the `admin` role and
is denied `/admin`.

### 3.4 `database/seeders/RolePermissionSeeder.php`

Sets up **both** role spaces (idempotent, safe to re-run):

```php
public function run(): void
{
    $this->seedWebGuardRoles();          // admin/user roles + custom permissions ("web")
    $this->seedAdminGuardShieldRoles();  // Shield-generated permissions ("admin")
}

protected function seedAdminGuardShieldRoles(): void
{
    Artisan::call('shield:generate', ['--all' => true, '--panel' => 'admin', '--no-interaction' => true]);

    $adminGuardRole = Role::findOrCreate('admin', 'admin');
    $adminGuardRole->syncPermissions(
        Permission::where('guard_name', 'admin')
            ->where('name', '!=', 'View:SiteSettings') // <-- admins are excluded from Site Settings
            ->get()
    );

    Role::findOrCreate(config('filament-shield.super_admin.name', 'super_admin'), 'admin');
}
```

This is wired into `database/seeders/DatabaseSeeder.php` (runs first, no dependencies)
and is also called at the top of `AdminSeeder`, `AdminUserSeeder`, and
`SuperAdminSeeder`, so both role spaces always exist before any account is created.

### 3.5 `database/seeders/SuperAdminSeeder.php` (new)

Creates (or updates) the default super admin account and grants it the `super_admin`
role under the `admin` guard:

```bash
php artisan db:seed --class=SuperAdminSeeder
```

```php
$superAdmin = User::updateOrCreate(
    ['email' => 'superadmin@apigharjagga.com'],
    ['name' => 'Super Admin', 'password' => Hash::make('password'), 'role' => 'admin', ...],
);

// 'admin' (web guard) comes for free from the `role` column via User::booted().
// 'super_admin' (admin guard) is Shield's own role and is assigned directly:
$superAdmin->assignRole(Role::findOrCreate('super_admin', 'admin'));
```

**Change the seeded email/password before using this in a real environment.** The
account gets `role = admin` so it also has full legacy-admin capabilities — a super
admin is a strict superset of admin, not a separate track.

### 3.6 Site Settings page

- `app/Models/SiteSetting.php` — a simple `key`/`value` store
  (`SiteSetting::get('site_name')`, `SiteSetting::setMany([...])`) backed by
  `site_settings` table, cached via `Cache::rememberForever('site_settings', ...)`.
- `app/Filament/Pages/SiteSettings.php` — a Filament page under a "Site Settings" nav
  group, using Filament Shield's `HasPageShield` trait:

  ```php
  use BezhanSalleh\FilamentShield\Traits\HasPageShield;

  class SiteSettings extends Page
  {
      use HasPageShield; // auto-implements canAccess() by checking can('View:SiteSettings')
      ...
  }
  ```

  `HasPageShield` is what makes this page (and its nav item) invisible/inaccessible to
  anyone who doesn't have the `View:SiteSettings` permission — which, per
  `RolePermissionSeeder`, is only `super_admin`.

### 3.7 `app/Policies/*` (generated by Shield) + `RolePolicy` registration

`php artisan shield:generate --all --panel=admin` generated one policy per admin-panel
resource model (`UserPolicy`, `PropertyPolicy`, `ClientPolicy`, `PropertyInquiryPolicy`,
`KycVerificationPolicy`) plus `RolePolicy` for Shield's own Roles screen. Each policy
method just forwards to a Shield permission, e.g.:

```php
public function viewAny(AuthUser $authUser): bool
{
    return $authUser->can('ViewAny:User');
}
```

The first five are auto-discovered by Laravel's standard `App\Models\X` →
`App\Policies\XPolicy` convention. `RolePolicy` guards `Spatie\Permission\Models\Role`,
which lives outside `app/Models`, so it's registered manually in
`AppServiceProvider::boot()`:

```php
Gate::policy(Role::class, RolePolicy::class);
```

**If you add a new Filament Resource/Page/Widget later**, re-run
`php artisan shield:generate --all --panel=admin` (or just re-run
`RolePermissionSeeder`, which calls it for you) to generate its permissions/policy, then
grant the new permission to whichever role(s) should have it via the Roles screen.

### 3.8 Other call sites migrated for the dual-guard model

- `app/Http/Requests/Auth/LoginRequest.php` — blocks `admin` (web guard) **and**
  `super_admin` (admin guard) accounts from the public `/login` form.
- `app/Filament/Widgets/StatsOverview.php` — `User::role('user', 'web')->count()`.
- `app/Observers/KycVerificationObserver.php` — `User::role('admin', 'web')`.
- `routes/web.php` — `role:admin,web` (was previously, incorrectly, `role:admin,admin`
  — the `admin` role has always lived under the `web` guard for this check).
- `app/Http/Controllers/Auth/RegisteredUserController.php` — new sign-ups explicitly
  get `'role' => 'user'`.
- `app/Filament/Resources/UserResource.php` — unchanged; still edits the simple `role`
  column, which `User::booted()` keeps in sync with both guards automatically.

### 3.9 Removed

- `app/Http/Middleware/EnsureUserIsAdmin.php` — replaced by Spatie's `RoleMiddleware`.

---

## 4. Current role/permission matrix

### `web` guard (legacy/custom — `RolePermissionSeeder::seedWebGuardRoles`)

| Permission | admin | user |
|---|:---:|:---:|
| access admin panel / manage users / manage properties / approve kyc / reject kyc / view analytics / manage clients / manage complaints / manage agreements / manage valuation requests | ✅ | |
| access user panel / submit kyc / manage own properties / submit inquiries | | ✅ |

### `admin` guard (Filament Shield — `RolePermissionSeeder::seedAdminGuardShieldRoles` / `seedStaffGuardRoles`)

| Permission | admin | super_admin | site_inspection_engineer | valuation_officer |
|---|:---:|:---:|:---:|:---:|
| Every generated `{Verb}:{Resource}` permission (`ViewAny:User`, `Update:Property`, `Delete:Client`, `ViewAny:Role`, `View:StatsOverview`, ...) | ✅ | ✅ | ❌ | ❌ |
| `View:SiteSettings` | ❌ | ✅ | ❌ | ❌ |
| `ViewAny:Property`, `View:Property` (read-only — no Create/Update/Delete) | ✅ | ✅ | ✅ | ✅ |
| `ViewAny:SiteInspection`, `View:SiteInspection` | ✅ | ✅ | ✅ | ✅ |
| `Create:SiteInspection` | ✅ | ✅ | ✅ | ❌ |
| `Update:SiteInspection` | ✅ | ✅ | ✅ | ✅ |

### Panel access

| | Admin panel (`/admin`) | User panel (`/dashboard`) |
|---|:---:|:---:|
| `user` role | ❌ denied | ✅ allowed |
| `admin` role | ✅ allowed | ❌ denied |
| `super_admin` role | ✅ allowed (+ Site Settings) | ❌ denied |
| `site_inspection_engineer` / `valuation_officer` roles | ✅ allowed (Properties list + Site Inspections only) | ✅ allowed (legacy `role` column defaults to `user`) |

---

## 5. How to add a new role dynamically

### The easy way (recommended): the admin panel UI

Filament Shield ships a full **Roles** CRUD screen at `/admin/shield/roles` (visible to
anyone with the `Role` permissions — `admin` and `super_admin` both have them by
default). From there staff can:

- Create a brand-new role (choose its guard — almost always `admin`, to control
  behavior *inside* the admin panel).
- Tick which of the generated resource/page/widget permissions it should have.
- Assign it to users from the `UserResource`... (see note below — the built-in `role`
  column dropdown only knows `admin`/`user`; extra `admin`-guard roles are assigned via
  Shield's Roles screen "Users" or via `$user->assignRole(...)`, see below.)

This covers "dynamic permission" for anything that shows up as a Filament
resource/page/widget — no code or deploys needed.

### The code way: `php artisan tinker`

```php
use Spatie\Permission\Models\Role;
use App\Models\User;

// A role for the "admin" guard (controls admin-panel resource permissions)
$manager = Role::findOrCreate('manager', 'admin');
$manager->givePermissionTo(['ViewAny:Property', 'Update:Property']);

$user = User::find(42);
$user->assignRole($manager);

// A role for the "web" guard (the legacy custom permission list)
$editor = Role::findOrCreate('editor', 'web');
$editor->givePermissionTo(['manage properties']);
```

### Making a new role open a panel

`canAccessPanel()` in `app/Models/User.php` only recognizes `admin`/`super_admin` (for
the admin panel) and `user` (for the user panel). To let e.g. a `manager` role also open
the admin panel:

```php
'admin' => $this->hasRole('admin', 'web') || $this->hasRole('super_admin', 'admin') || $this->hasRole('manager', 'admin'),
```

### Making the legacy `role` column recognize a new role

The `role` column is a 2-value enum (`user`, `admin`) kept only for legacy
display/filtering. A brand-new role like `manager` or `super_admin` does **not** need
to go through this column at all — assign it directly with `$user->assignRole(...)`
(exactly how `SuperAdminSeeder` does it). Only widen the enum/`booted()` check if you
specifically want the new role to also show up in the `UserResource` table's "role"
column/filter dropdown.

---

## 6. How to add a new permission dynamically

### For anything inside the admin panel (a Filament Resource/Page/Widget)

Just build the Resource/Page/Widget normally, then run:

```bash
php artisan shield:generate --all --panel=admin
```

This creates the `ViewAny:X` / `View:X` / `Create:X` / ... permissions (and a policy,
if the model doesn't already have one) automatically — nothing to name by hand. Then
grant the new permissions to a role from `/admin/shield/roles`.

For a **custom Page** that isn't tied to a model (like `SiteSettings`), add
`use BezhanSalleh\FilamentShield\Traits\HasPageShield;` to the page class *before*
running `shield:generate` — that's what makes Shield discover it and generate a
`View:{PageName}` permission for it.

### For anything else (custom, hand-named permission)

```php
use Spatie\Permission\Models\Permission;

Permission::findOrCreate('publish properties', 'web'); // or 'admin', depending on context
Role::findByName('admin', 'web')->givePermissionTo('publish properties');
```

Check it the normal Laravel way: `$user->can('publish properties')`,
`$this->authorize(...)`, `@can(...)`, or `Route::middleware('permission:...')`.

### Recommended: keep it repeatable via the seeder

Add new `web`-guard permissions to `RolePermissionSeeder::seedWebGuardRoles()`'s
`$permissions` array (and assign them in `syncPermissions([...])`). Admin-guard/Shield
permissions regenerate themselves automatically every time the seeder runs
(`shield:generate --all` is idempotent), so nothing to hand-maintain there beyond the
`View:SiteSettings` exclusion list.

### Cache note

Spatie caches the permission↔role mapping for performance. `RolePermissionSeeder`
already calls `forgetCachedPermissions()`. If you ever edit `role_has_permissions`
directly via raw SQL (not recommended), clear it manually:

```bash
php artisan permission:cache-reset
```

---

## 7. Practical checklist for common changes

| I want to... | Do this |
|---|---|
| Add a permission for a new admin-panel Resource/Page/Widget | `php artisan shield:generate --all --panel=admin`, then grant it via `/admin/shield/roles` |
| Add a brand-new role | `/admin/shield/roles` → New, **or** `Role::findOrCreate('name', 'admin'\|'web')` |
| Add a brand-new custom (non-Shield) permission | `Permission::findOrCreate('name', 'web')`, attach to role(s) |
| Give a user an existing role | `$user->assignRole($role)` |
| Remove a role from a user | `$user->removeRole($role)` |
| Check a role in code | `$user->hasRole('roleName', 'web'\|'admin')` — **always pass the guard** |
| Check a Shield permission in code | `$user->can('ViewAny:User')` (only meaningful with the "admin" guard active, i.e. inside `/admin/*`) |
| Check a custom permission in code | `$user->can('permission name')` (web guard) |
| Protect a route by role | `Route::middleware('role:roleName,web')` |
| Let a new role open the admin panel | edit `canAccessPanel()` match arm for `'admin'` |
| Let a new role open the user panel | edit `canAccessPanel()` match arm for `'user'` |
| Gate a new custom Filament Page by permission | add `HasPageShield` trait, run `shield:generate` |

**Always pass the guard explicitly** (`'web'` or `'admin'`) when calling `hasRole()` /
`User::role(...)` — see §2/§3.3 for why this app never relies on the ambient default
guard.

---

## 8. Adding new staff/admin roles with limited dashboard access

This is the concrete recipe for adding narrow, staff-facing admin roles — e.g.
**Property Consultant**, **Valuation Officer**, **Site Engineer** — the kind that
should see and touch only *part* of the admin panel, not "manage everything" like
`admin`/`super_admin`.

### 8.1 Which guard?

**Always `admin`.** These are all admin-panel staff, not marketplace users, so they
belong entirely in the `admin`-guard role space (§2) — the one driven by Filament
Shield's generated permissions. Never put a new staff role under the `web` guard:
`web` is reserved for the two hardcoded roles (`admin`, `user`) that the legacy `role`
column and `canAccessPanel()` already understand (§3.3); mixing new roles into it buys
you nothing and means touching both systems instead of just Shield's.

### 8.2 Step-by-step: a role that maps to an existing Resource (e.g. Property Consultant)

Say Property Consultant should only work with property listings and inquiries — nothing
else (no Users, no Clients, no Site Settings).

1. Make sure Shield's permissions are up to date (safe/idempotent to re-run any time):
   ```bash
   php artisan shield:generate --all --panel=admin
   ```
2. Create the role and grant it **only** the permissions it needs — via
   `/admin/shield/roles` → "New Role" (tick just the rows you want), or in
   `php artisan tinker`:
   ```php
   use Spatie\Permission\Models\Role;

   $role = Role::findOrCreate('property_consultant', 'admin');
   $role->syncPermissions([
       'ViewAny:Property', 'View:Property', 'Create:Property', 'Update:Property',
       // deliberately NOT granted: 'Delete:Property', 'DeleteAny:Property', ...
       'ViewAny:PropertyInquiry', 'View:PropertyInquiry', 'Update:PropertyInquiry',
   ]);
   ```
3. Let this role open the admin panel in the first place — `canAccessPanel()` only
   recognizes `admin`/`super_admin` by default, so extend it (see §8.5 for a tidier
   pattern once you have several roles):
   ```php
   // app/Models/User.php
   'admin' => $this->hasRole('admin', 'web')
       || $this->hasRole('super_admin', 'admin')
       || $this->hasRole('property_consultant', 'admin'),
   ```
4. Assign it to a user:
   ```php
   $user = User::find(99);
   $user->assignRole($role); // passing the Role instance avoids any guard ambiguity
   ```
5. Done — no other code needed. Log that user into `/admin/login` and the sidebar will
   only show **Properties** and **Inquiries & Leads**. **Users & KYC**, **Clients**,
   and **Site Settings** are all invisible, and typing their URLs directly returns 403,
   because Filament checks the resource's Policy (`canViewAny()`, generated in §3.7)
   before registering a nav item or allowing access at all — that's the whole point of
   letting Shield generate real permissions/policies instead of hand-rolling checks.

### 8.3 Step-by-step: a role for a feature that has no admin Resource yet (Valuation Officer, Site Engineer)

There's no `ValuationRequestResource` or `SiteInspectionResource` under
`app/Filament/Resources` yet (even though the underlying `valuation_requests` /
`site_inspections` tables exist) — Filament Shield can only generate `ViewAny:X` /
`View:X` / ... permissions for something that's actually registered as a Filament
Resource/Page/Widget. To give Valuation Officer / Site Engineer the same kind of
per-action granularity as Property Consultant, build the Resource first:

```bash
php artisan make:filament-resource ValuationRequest --panel=admin
php artisan make:filament-resource SiteInspection --panel=admin
php artisan shield:generate --all --panel=admin   # now generates ViewAny:ValuationRequest, etc.
```

Then follow the exact same recipe as §8.2:

```php
$valuationOfficer = Role::findOrCreate('valuation_officer', 'admin');
$valuationOfficer->syncPermissions([
    'ViewAny:ValuationRequest', 'View:ValuationRequest', 'Update:ValuationRequest',
]);

$siteEngineer = Role::findOrCreate('site_engineer', 'admin');
$siteEngineer->syncPermissions([
    'ViewAny:SiteInspection', 'View:SiteInspection', 'Create:SiteInspection', 'Update:SiteInspection',
]);
```

**If you don't want to build a full Resource yet**, you can fall back to the
hand-named-permission approach this app already uses for a few things (§6, "For
anything else") — e.g. the `'manage valuation requests'` permission under the `web`
guard already exists and can gate a plain controller/route right now. The trade-off:
that's a single all-or-nothing permission per feature (no separate view/create/
update/delete granularity), and it does **not** automatically show/hide admin-panel
nav items the way real Shield permissions do. Prefer building the Resource + Shield
permissions above whenever the feature should live inside `/admin` with proper,
dynamically-manageable, per-action permissions.

### 8.4 Limiting *which records*, not just *which actions*

Everything above controls which **resources/actions** a role can see at all (e.g.
Property Consultant can edit Properties, but never delete them). If a role also needs
row-level scoping — e.g. a Site Engineer should only see inspections **assigned to
them**, not everyone's — that's an additional layer Shield doesn't provide out of the
box. Add it directly in the Resource:

```php
// app/Filament/Resources/SiteInspectionResource.php
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();

    if (auth()->user()->hasRole('site_engineer', 'admin') && ! auth()->user()->hasRole('super_admin', 'admin')) {
        $query->where('engineer_id', auth()->id());
    }

    return $query;
}
```

### 8.5 Keeping `canAccessPanel()` tidy as you add more roles

Once you have three or more staff roles, list them once instead of adding a new `||`
every time:

```php
// app/Models/User.php
private const ADMIN_STAFF_ROLES = ['property_consultant', 'valuation_officer', 'site_engineer'];

public function canAccessPanel(Panel $panel): bool
{
    return match ($panel->getId()) {
        'admin' => $this->hasRole('admin', 'web')
            || $this->hasAnyRole([...self::ADMIN_STAFF_ROLES, 'super_admin'], 'admin'),
        'user' => $this->hasRole('user', 'web'),
        default => false,
    };
}
```

### 8.6 Quick reference: what permissions exist right now

Run this any time (after `shield:generate`, if you just added a Resource) to see
exactly what's available to assign to a role:

```bash
php artisan tinker --execute="foreach (Spatie\Permission\Models\Permission::where('guard_name','admin')->pluck('name') as \$p) { echo \$p . PHP_EOL; }"
```

### 8.7 Summary table

| Situation | Guard | How permissions are named | How to gate the admin panel | How to hide nav items |
|---|---|---|---|---|
| Role maps to an existing admin Resource (Property, Client, KYC, Inquiry, User) | `admin` | Auto: `shield:generate` | Add `hasRole('roleName', 'admin')` to `canAccessPanel()` | Automatic (Policy-driven) |
| Role maps to a *future* admin Resource (Valuation, Site Inspection, ...) | `admin` | Build the Resource, then `shield:generate` | Same as above | Automatic once the Resource + policy exist |
| Role needs a single all-or-nothing custom permission, not tied to any Resource | `web` (matches this app's existing custom permission list) | Hand-named, e.g. `'manage valuation requests'` | N/A — gate a route/controller with `permission:...` middleware | Not applicable (no Filament nav involved) |
| Role should only see *some rows*, not all | (whichever above) | (whichever above) | (whichever above) | Add row-scoping in `getEloquentQuery()` (§8.4) |

### 8.8 Case study (implemented): Site Inspection Engineer & Valuation Officer

This follows the "feature has no admin Resource yet" recipe from §8.3, end-to-end, for the Annex-D
Site Inspection Checklist workflow.

**What was added:**

- **Migration** `add_workflow_fields_to_site_inspections_table` — extends the pre-existing
  `site_inspections` table (which already had the Annex-D §4/§7/§8 columns from the original schema
  import) with: `inspector_user_id` / `reviewed_by_user_id` (real `users` FKs — the table's original
  `*_staff_id` columns point at an unrelated legacy `staff` lookup table and are left untouched/unused),
  the §1 general-info snapshot fields (`property_owner_name`, `contact_number`, `property_location`,
  `municipality`, `ward_no`, `inspector_designation`), the fixed-length §2/§3/§5/§6 checklists as JSON
  (`land_checklist`, `building_checklist`, `photo_checklist`, `documents_checklist` — stored as JSON
  rather than using the normalized `site_inspection_items`/`site_inspection_documents` tables, since each
  checklist is a small fixed set of items, not an open-ended list), and the workflow columns
  (`status`, `submitted_to`, `submitted_at`, `reviewed_at`, `review_notes`).
- **`app/Models/SiteInspection.php`** — defines the fixed item lists as constants
  (`LAND_ITEMS`, `BUILDING_ITEMS`, `PHOTO_ITEMS`, `DOCUMENT_ITEMS`) so the form, the infolist, and
  `defaultChecklists()` all stay in sync with a single source of truth.
- **`app/Filament/Resources/SiteInspections/*`** — the Resource, form, table, and infolist. The form
  renders each checklist item as a Yes/No radio + remarks input (land/building) or a toggle
  (photos/documents), generated programmatically from the constants above instead of being hand-written
  9+10+7+6 times.
- **Submit workflow** — an engineer fills the checklist while `status = draft`, then uses the
  "Send to Valuation Officer" / "Send to Admin" action (table row action or edit-page header action),
  which sets `status = submitted`, `submitted_to`, `submitted_at`, and fires a Filament **database
  notification** (bell icon) to every user with the target role
  (`SiteInspectionResource::notifyRecipients()`). A valuation officer or admin then uses "Mark Reviewed"
  to set `status = reviewed`.
- **Row-level scoping** (`SiteInspectionResource::getEloquentQuery()`): engineers only see inspections
  they personally created (`inspector_user_id = auth()->id()`); valuation officers only see
  non-draft reports (i.e. things actually submitted for review); admins/super\_admins see everything.
- **Permissions** (`RolePermissionSeeder::seedStaffGuardRoles()`, `admin` guard):
  - `site_inspection_engineer`: `ViewAny:Property`, `View:Property` (read-only property list) +
    `ViewAny:SiteInspection`, `View:SiteInspection`, `Create:SiteInspection`, `Update:SiteInspection`.
  - `valuation_officer`: the same Property read-only pair + `ViewAny:SiteInspection`,
    `View:SiteInspection`, `Update:SiteInspection` (no `Create` — they review, they don't originate
    reports).
  - Neither role gets `Create/Update/Delete:Property`, so the Properties list is genuinely read-only
    for them, and neither gets any permission on User/Client/KYC/Inquiry/SiteSettings resources, so
    those nav items never render for them (Filament hides nav items whose `viewAny` policy check fails).
- **`User::canAccessPanel()`** — now also allows in anyone holding a role listed in the new
  `User::ADMIN_STAFF_ROLES` constant (`['site_inspection_engineer', 'valuation_officer']`), so adding
  another narrow staff role later is a one-line constant change instead of editing the match arm again.
- **`database/seeders/StaffAccountsSeeder.php`** (new) — creates sample
  `engineer@apigharjagga.com` / `valuation@apigharjagga.com` logins (password `password` by default,
  overridable via `.env`) for testing; wired into `DatabaseSeeder`.
- **`AdminPanelProvider`** — added a "Site Inspections" navigation group and enabled
  `->databaseNotifications()` so the submit-to-review notifications actually show up in the admin panel.

---

## 9. Files touched by this implementation

```
composer.json / composer.lock                  — spatie/laravel-permission, bezhansalleh/filament-shield
config/permission.php                          — published + 'roles' table renamed to 'permission_roles'
config/filament-shield.php                     — published Shield config (super_admin role name, etc.)
database/migrations/2026_09_02_161304_create_permission_tables.php  — Spatie tables
database/migrations/2026_09_03_055108_create_site_settings_table.php — key/value settings store
database/seeders/RolePermissionSeeder.php      — web-guard roles + admin-guard Shield roles/permissions
database/seeders/SuperAdminSeeder.php          — creates/updates the super admin account
database/seeders/DatabaseSeeder.php            — calls RolePermissionSeeder first
database/seeders/AdminSeeder.php / AdminUserSeeder.php — call RolePermissionSeeder, set role=admin
app/Models/User.php                            — HasRoles trait, dual-guard role sync hook, canAccessPanel(), isAdmin()
app/Models/SiteSetting.php                     — new: key/value settings model
app/Filament/Pages/SiteSettings.php            — new: Site Settings admin page (HasPageShield)
resources/views/filament/pages/site-settings.blade.php — new
app/Policies/*Policy.php                       — generated by `shield:generate` (User/Property/Client/PropertyInquiry/KycVerification/Role)
app/Providers/AppServiceProvider.php           — registers RolePolicy via Gate::policy()
app/Providers/Filament/AdminPanelProvider.php  — registers FilamentShieldPlugin, "Site Settings" nav group
app/Http/Requests/Auth/LoginRequest.php        — blocks admin (web) AND super_admin (admin) from public login
app/Http/Controllers/Auth/RegisteredUserController.php — new users get role=user explicitly
app/Http/Middleware/EnsureUserIsAdmin.php      — deleted (replaced by Spatie RoleMiddleware)
bootstrap/app.php                              — registers role/permission/role_or_permission middleware aliases
routes/web.php                                 — admin routes use role:admin,web middleware (guard fix)
app/Observers/KycVerificationObserver.php      — admin email lookup uses User::role('admin', 'web')
app/Filament/Widgets/StatsOverview.php         — "Total Users" stat uses User::role('user', 'web')
```

---

## 10. Known gotchas (already fixed, documented for future reference)

1. **Filament flips the "default" guard while inside a panel.** Any Spatie call
   without an explicit guard resolves against whatever `config('auth.defaults.guard')`
   currently is — which Filament changes per-panel via `Auth::shouldUse()`. This app
   does **not** paper over that with a hardcoded `getDefaultGuardName()` override
   (an earlier version of this doc described one — it was removed because it broke
   Filament Shield, which depends on that dynamic behavior). Instead, every call site
   that needs the `web`-guard roles passes `'web'` explicitly. If you add a new call
   site that checks `admin`/`user`/`super_admin`, always pass the guard.
2. **Two different `roles` tables exist in this database.** `roles` (staff job-title
   lookup, unrelated) and `permission_roles` (Spatie, RBAC). Don't confuse them —
   always use the `Spatie\Permission\Models\Role` model, never raw queries against
   `roles`, when working with user permissions.
3. **The `role` column is legacy, not authoritative.** `canAccessPanel()` and all
   security checks use Spatie's `hasRole()`/`can()`. The column is only kept for
   convenience (Filament table/filter display, a couple of simple lookup queries) and
   is auto-synced from Spatie assignments — don't add new authorization logic that
   reads `$user->role` directly; use `$user->hasRole()` / `$user->can()` instead.
4. **`syncRoles()` is guard-blind.** It detaches *every* role a model has, regardless
   of guard, before reassigning. Since admins now carry roles under two guards
   (`admin`/web and `admin`/admin, plus optionally `super_admin`/admin), never call
   `syncRoles()` on `User` directly — use `assignRole()`/`removeRole()` for a specific
   guard's role instead (see `User::booted()` for the pattern).
5. **An admin created/edited before the `admin`-guard role sync was added** won't have
   the `admin`/`admin`-guard role until their record is saved again (the `booted()`
   hook is what assigns it). This was backfilled once for existing accounts via
   `User::where('role', 'admin')->each->touch()`; if you ever bulk-import admin users
   via raw SQL (bypassing Eloquent), remember to also assign that role manually or
   trigger a save.
