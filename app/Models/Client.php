<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $client_id
 * @property string $client_code
 * @property string $client_type
 * @property string $full_name
 * @property string|null $mobile_no
 * @property string|null $email
 * @property string $mis_entry_status
 * @property bool $is_active
 */
class Client extends Model
{
    protected $primaryKey = 'client_id';

    protected $fillable = [
        'client_code', 'client_type', 'full_name', 'father_mother_name',
        'spouse_name', 'grandfather_name', 'citizenship_no', 'nationality',
        'date_of_birth', 'gender', 'occupation', 'mobile_no', 'alt_contact_no',
        'telephone_no', 'email', 'permanent_address_id', 'current_address_id',
        'mobile_app_user_id', 'registration_date', 'registered_by',
        'mis_entry_status', 'is_active',
    ];

    protected $casts = [
        'date_of_birth'     => 'date',
        'registration_date' => 'date',
        'is_active'         => 'boolean',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (Client $client) {
            $client->created_at = now();
            $client->updated_at = now();
        });

        static::updating(function (Client $client) {
            $client->updated_at = now();
        });
    }

    public function permanentAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'permanent_address_id', 'address_id');
    }

    public function currentAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'current_address_id', 'address_id');
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'registered_by', 'staff_id');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_client_id', 'client_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClientDocument::class, 'client_id', 'client_id');
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ClientServiceRequest::class, 'client_id', 'client_id');
    }
}
