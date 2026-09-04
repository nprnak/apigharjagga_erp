<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property int|null $role_id
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'role', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->role === 'admin';
        }

        return true;
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

    public function staffRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    /**
     * Dynamic permission check for admin-panel resources. An admin with no
     * staff role assigned keeps full (legacy) access; once a staff role is
     * assigned, access is driven entirely by that role's permissions list.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->role !== 'admin') {
            return false;
        }

        if ($this->staffRole === null) {
            return true;
        }

        return $this->staffRole->hasPermission($permission);
    }

    /**
     * A super admin (Admin role, or an admin with no staff role assigned)
     * is the only one allowed to manage roles and their permissions.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin' && ($this->staffRole === null || $this->staffRole->hasPermission('*'));
    }
}
