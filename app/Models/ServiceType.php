<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'service_type_id';

    protected $fillable = [
        'service_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
