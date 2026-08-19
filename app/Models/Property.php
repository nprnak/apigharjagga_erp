<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Property extends Model
{
    protected $primaryKey = 'property_id';

    protected $fillable = [
        'property_code',
        'owner_client_id',
        'user_id',
        'ownership_role',
        'property_type',
        'address_id',
        'kitta_no',
        'area',
        'map_sheet_no',
        'ownership_type',
        'ownership_certificate_no',
        'road_access',
        'road_width',
        'facing_direction',
        'year_of_construction',
        'no_of_floors',
        'covered_area',
        'structure_type',
        'roof_type',
        'parking',
        'water_supply',
        'electricity',
        'internet',
        'drainage',
        'building_permit_no',
        'current_building_condition',
        'status',
        'approval_status',
    ];

    protected $casts = [
        'year_of_construction' => 'integer',
        'no_of_floors'         => 'integer',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'owner_client_id', 'client_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id', 'address_id');
    }

    public function listing(): HasOne
    {
        return $this->hasOne(PropertyListing::class, 'property_id', 'property_id');
    }

    public function listings(): HasMany
    {
        return $this->hasMany(PropertyListing::class, 'property_id', 'property_id');
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class, 'property_id', 'property_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class, 'property_id', 'property_id');
    }
}
