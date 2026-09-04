<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteInspection extends Model
{
    protected $primaryKey = 'inspection_id';

    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'listing_id',
        'inspector_staff_id',
        'inspection_date',
        'distance_from_main_road',
        'nearby_facilities',
        'commercial_potential',
        'residential_suitability',
        'future_development_potential',
        'observation_notes',
        'final_status',
        'prepared_by_staff_id',
        'prepared_date',
        'verified_by_staff_id',
        'verified_date',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'prepared_date' => 'date',
        'verified_date' => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(PropertyListing::class, 'listing_id', 'listing_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'inspector_staff_id', 'staff_id');
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'prepared_by_staff_id', 'staff_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'verified_by_staff_id', 'staff_id');
    }
}
