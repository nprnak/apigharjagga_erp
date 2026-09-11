<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Property extends Model
{
    protected $primaryKey = 'property_id';

    /**
     * Annex A §3 "Building Details" only applies to these property types —
     * land and agricultural land have no structure to describe. Used by
     * the self-service wizard and the printable record to decide whether
     * to show/require that section.
     */
    public const BUILDING_TYPES = ['house', 'apartment', 'commercial_building', 'office_space', 'industrial_property'];

    protected $fillable = [
        'property_code',
        'owner_client_id',
        'user_id',
        'ownership_role',
        'owner_full_name',
        'owner_citizenship_no',
        'owner_relation',
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
        'is_listed',
    ];

    protected $casts = [
        'year_of_construction' => 'integer',
        'no_of_floors' => 'integer',
        'is_listed' => 'boolean',
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

    public function inquiries(): HasMany
    {
        return $this->hasMany(PropertyInquiry::class, 'property_id', 'property_id');
    }

    public function valuationRequests(): HasMany
    {
        return $this->hasMany(ValuationRequest::class, 'property_id', 'property_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PropertyDocument::class, 'property_id', 'property_id');
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(
            PropertyFeatureType::class,
            'property_features',
            'property_id',
            'feature_id',
            'property_id',
            'feature_id',
        );
    }

    public function isBuildingType(): bool
    {
        return in_array($this->property_type, self::BUILDING_TYPES, true);
    }
}
