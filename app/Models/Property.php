<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $property_id
 * @property string $property_code
 * @property int $owner_client_id
 * @property string $property_type
 * @property string $status
 */
class Property extends Model
{
    protected $primaryKey = 'property_id';

    public $timestamps = false;

    protected $fillable = [
        'property_code', 'owner_client_id', 'ownership_role', 'property_type',
        'address_id', 'kitta_no', 'area', 'map_sheet_no', 'ownership_type',
        'ownership_certificate_no', 'road_access', 'road_width', 'facing_direction',
        'year_of_construction', 'no_of_floors', 'covered_area', 'structure_type',
        'roof_type', 'parking', 'water_supply', 'electricity', 'internet',
        'drainage', 'building_permit_no', 'current_building_condition', 'status',
    ];

    protected $casts = [
        'year_of_construction' => 'integer',
        'no_of_floors'         => 'integer',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Property $property) {
            $property->created_at = now();
            $property->updated_at = now();
        });

        static::updating(function (Property $property) {
            $property->updated_at = now();
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'owner_client_id', 'client_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id', 'address_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class, 'property_id', 'property_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PropertyDocument::class, 'property_id', 'property_id');
    }

    public function valuationRequests(): HasMany
    {
        return $this->hasMany(ValuationRequest::class, 'property_id', 'property_id');
    }
}
