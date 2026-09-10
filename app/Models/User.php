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
 * @property string|null $client_type
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'role', 'role_id', 'client_type'])]
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

    /**
     * Which portal experience a 'user'-role account gets in the customer
     * User panel. Kept separate from Client::client_type — see the
     * add_client_type_to_users_table migration for why.
     */
    public static function clientTypeOptions(): array
    {
        return [
            'owner' => 'Property Owner',
            'buyer' => 'Buyer',
            'investor' => 'Investor',
            'tenant' => 'Tenant',
            'agent' => 'Agent / Representative',
        ];
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'user_id');
    }

    /**
     * The Annex-F Client record (created by staff at a counter, or matched
     * from a public intake form) that corresponds to this web login, found
     * by matching this user's verified KYC citizenship number against
     * `clients.citizenship_no`. There is no direct FK between the two
     * tables — see the add_client_type_to_users_table migration for why —
     * so a Buyer/Owner's own agreements and payment receipts (which hang
     * off `Client`, not `User`) can only be found this way. Returns null
     * until KYC is submitted with a citizenship number that matches an
     * existing Client record.
     */
    public function resolvedClient(): ?Client
    {
        $citizenshipNo = $this->kycVerification?->citizenship_no;

        if (! $citizenshipNo) {
            return null;
        }

        return Client::where('citizenship_no', $citizenshipNo)->first();
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
