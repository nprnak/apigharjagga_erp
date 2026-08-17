<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    protected $primaryKey = 'client_id';

    protected $fillable = [
        'client_code',
        'client_type',
        'client_type_other',
        'full_name',
        'father_mother_name',
        'spouse_name',
        'grandfather_name',
        'citizenship_no',
        'nationality',
        'date_of_birth',
        'gender',
        'occupation',
        'mobile_no',
        'alt_contact_no',
        'telephone_no',
        'email',
        'permanent_address_id',
        'current_address_id',
        'mobile_app_user_id',
        'registration_date',
        'registered_by',
        'mis_entry_status',
        'is_active',
        'signature_name',
        'signature_path',
        'signature_date',
        'registered_by_name',
        'registered_by_designation',
        'registered_by_signature_path',
        'registered_by_date',
        'approved_by_name',
        'approved_by_designation',
        'approved_by_signature_path',
        'approved_by_date',
    ];

    protected $casts = [
        'date_of_birth'     => 'date',
        'registration_date' => 'date',
        'signature_date'    => 'date',
        'registered_by_date'=> 'date',
        'approved_by_date'  => 'date',
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

    public function organization(): HasOne
    {
        return $this->hasOne(ClientOrganization::class, 'client_id', 'client_id');
    }

    public function propertyRequirement(): HasOne
    {
        return $this->hasOne(ClientPropertyRequirement::class, 'client_id', 'client_id');
    }

    public function ownerListing(): HasOne
    {
        return $this->hasOne(ClientOwnerListing::class, 'client_id', 'client_id');
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ClientServiceRequest::class, 'client_id', 'client_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClientDocument::class, 'client_id', 'client_id');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_client_id', 'client_id');
    }

    public function propertyListings(): HasMany
    {
        return $this->hasMany(PropertyListing::class, 'applicant_client_id', 'client_id');
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'client_id', 'client_id');
    }
}
