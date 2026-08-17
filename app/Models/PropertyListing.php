<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyListing extends Model
{
    protected $primaryKey = 'listing_id';

    protected $fillable = [
        'application_no',
        'property_id',
        'applicant_client_id',
        'purpose_of_listing',
        'expected_selling_price',
        'negotiable',
        'minimum_acceptable_price',
        'rental_amount',
        'effective_date',
        'date_received',
        'assigned_officer_id',
        'inspection_required',
        'inspection_date',
        'valuation_required',
        'photographs_received',
        'gis_location_verified',
        'legal_verification_status',
        'listing_status',
        'remarks',
        'received_by_staff_id',
        'applicant_signature_path',
    ];

    protected $casts = [
        'expected_selling_price'   => 'decimal:2',
        'minimum_acceptable_price' => 'decimal:2',
        'rental_amount'            => 'decimal:2',
        'negotiable'               => 'boolean',
        'inspection_required'      => 'boolean',
        'valuation_required'       => 'boolean',
        'photographs_received'     => 'boolean',
        'gis_location_verified'    => 'boolean',
        'effective_date'           => 'date',
        'date_received'            => 'date',
        'inspection_date'          => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'applicant_client_id', 'client_id');
    }
}
