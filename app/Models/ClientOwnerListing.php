<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientOwnerListing extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'available_for',
        'property_location',
        'kitta_no',
        'land_area',
        'building_details',
        'expected_price',
    ];

    protected $casts = [
        'available_for'  => 'array',
        'expected_price' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }
}
