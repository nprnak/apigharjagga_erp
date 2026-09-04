<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyVerification extends Model
{
    protected $primaryKey = 'verification_id';

    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'verifier_staff_id',
        'approver_staff_id',
        'verification_date',
        'approved_date',
        'result',
        'gps_coordinates_recorded',
        'mis_entry_completed',
        'mobile_app_verification_completed',
    ];

    protected $casts = [
        'verification_date' => 'date',
        'approved_date' => 'date',
        'gps_coordinates_recorded' => 'boolean',
        'mis_entry_completed' => 'boolean',
        'mobile_app_verification_completed' => 'boolean',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'verifier_staff_id', 'staff_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'approver_staff_id', 'staff_id');
    }
}
