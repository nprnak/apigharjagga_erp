<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyFeatureType extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'feature_id';

    protected $fillable = [
        'feature_name',
    ];
}
