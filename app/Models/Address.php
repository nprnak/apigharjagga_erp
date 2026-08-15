<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $address_id
 * @property string|null $province
 * @property string|null $district
 * @property string|null $municipality
 * @property int|null $ward_no
 * @property string|null $tole
 * @property string|null $house_no
 * @property string|null $google_map_link
 * @property float|null $latitude
 * @property float|null $longitude
 */
class Address extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'address_id';

    protected $fillable = [
        'province', 'district', 'municipality', 'ward_no',
        'tole', 'house_no', 'google_map_link', 'latitude', 'longitude',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];
}
