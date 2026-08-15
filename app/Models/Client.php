<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $primaryKey = 'client_id';

    protected $fillable = [
        'client_code',
        'client_type',
        'full_name',
        'father_mother_name',
        'grandfather_name',
        'citizenship_no',
        'nationality',
        'date_of_birth',
        'gender',
        'occupation',
        'mobile_no',
        'telephone_no',
        'email',
        'permanent_address_id',
        'current_address_id',
        'registration_date',
        'mis_entry_status',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth'     => 'date',
        'registration_date' => 'date',
        'is_active'         => 'boolean',
    ];

    public function permanentAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'permanent_address_id', 'address_id');
    }

    public function currentAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'current_address_id', 'address_id');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_client_id', 'client_id');
    }

    public function propertyListings(): HasMany
    {
        return $this->hasMany(PropertyListing::class, 'applicant_client_id', 'client_id');
    }
}
