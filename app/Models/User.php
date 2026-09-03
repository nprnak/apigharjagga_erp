<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * "admin" guard roles that grant limited (non-super_admin, non-legacy-
     * admin) access into the admin panel — staff with a narrow slice of the
     * dashboard rather than full admin rights. See RolePermissionSeeder::
     * seedStaffGuardRoles() for what each role can actually see/do.
     */
    public const ADMIN_STAFF_ROLES = [
        'site_inspection_engineer',
        'valuation_officer',
    ];

    /**
     * Deliberately NOT overridden.
     *
     * This app has two role "spaces" that share the same User model but
     * live under different Spatie guards:
     *  - "web"   — the legacy admin/user roles + custom permissions used by
     *              the marketplace site, LoginRequest, and canAccessPanel().
     *  - "admin" — Filament Shield's auto-generated resource/page/widget
     *              permissions (ViewAny:Property, View:SiteSettings, etc.)
     *              plus Shield's own "super_admin" role, scoped to the
     *              admin panel (which authenticates on the "admin" guard).
     *
     * Shield relies on the ambient default guard (which Filament flips via
     * `Auth::shouldUse('admin')` for the duration of every `/admin/*`
     * request) to resolve which guard's permissions to check. Every call
     * site in this app that touches the "web" guard's roles explicitly
     * passes `'web'` as the guard argument instead of relying on the
     * default, so this class intentionally leaves guard resolution dynamic.
     */
    protected static function booted(): void
    {
        // Spatie's roles/permissions tables are the real source of truth for
        // access control. The `role` column is kept only as a simple,
        // queryable/legacy field (used for filters, admin-notification
        // lookups, etc.), so whenever it changes we mirror it into a real
        // Spatie role assignment under the "web" guard. This means `role`
        // and the web-guard Spatie role never drift apart, no matter which
        // code path (seeder, Filament form, registration) set it.
        //
        // This deliberately avoids syncRoles(), which detaches ALL of the
        // user's roles regardless of guard — that would wipe out any
        // Filament Shield roles (e.g. "super_admin") stored under the
        // "admin" guard on every single save of this model.
        static::saved(function (User $user) {
            if (! $user->role || ! in_array($user->role, ['admin', 'user'], true)) {
                return;
            }

            // findOrCreate makes this self-healing: it works even before
            // RolePermissionSeeder has run (fresh installs, test DBs), and
            // is a no-op once the role already exists.
            $role = \Spatie\Permission\Models\Role::findOrCreate($user->role, 'web');

            $user->roles
                ->where('guard_name', 'web')
                ->whereIn('name', ['admin', 'user'])
                ->reject(fn ($existing) => $existing->is($role))
                ->each(fn ($existing) => $user->removeRole($existing));

            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }

            // Admins additionally need the "admin" role under the "admin"
            // guard, which is what Filament Shield's generated resource/
            // page/widget permissions are actually checked against inside
            // the admin panel (see RolePermissionSeeder::seedAdminGuardShieldRoles).
            if ($user->role === 'admin') {
                $adminGuardRole = \Spatie\Permission\Models\Role::findOrCreate('admin', 'admin');

                if (! $user->hasRole($adminGuardRole)) {
                    $user->assignRole($adminGuardRole);
                }
            } else {
                $adminGuardAdminRole = \Spatie\Permission\Models\Role::where('name', 'admin')
                    ->where('guard_name', 'admin')
                    ->first();

                if ($adminGuardAdminRole && $user->hasRole($adminGuardAdminRole)) {
                    $user->removeRole($adminGuardAdminRole);
                }
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Each panel is restricted to its matching role only, so an admin
        // can never open the user dashboard and a normal user can never
        // open the admin dashboard. Backed by Spatie's dynamic role/
        // permission system rather than a hardcoded property check.
        //
        // "super_admin" (Filament Shield's role, under the "admin" guard)
        // is also allowed into the admin panel, on top of the legacy
        // "admin" (web guard) role.
        return match ($panel->getId()) {
            'admin' => $this->hasRole('admin', 'web')
                || $this->hasRole('super_admin', 'admin')
                || $this->hasRole(self::ADMIN_STAFF_ROLES, 'admin'),
            'user' => $this->hasRole('user', 'web'),
            default => false,
        };
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin', 'web') || $this->hasRole('super_admin', 'admin');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function kycVerification(): HasOne
    {
        return $this->hasOne(KycVerification::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'user_id');
    }
}
