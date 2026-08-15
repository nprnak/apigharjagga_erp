<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'address_id';

    protected $fillable = [
        'province',
        'district',
        'municipality',
        'ward_no',
        'tole_locality',
        'full_address_text',
        'gps_lat',
        'gps_lng',
        'gps_verified',
    ];

    protected $casts = [
        'gps_lat'      => 'decimal:7',
        'gps_lng'      => 'decimal:7',
        'gps_verified' => 'boolean',
    ];
}
