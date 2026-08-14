<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $valuation_id
 * @property int $property_id
 * @property int $client_id
 * @property string $valuation_type
 * @property string $status
 */
class ValuationRequest extends Model
{
    protected $primaryKey = 'request_id';

    protected $fillable = [
        'request_code', 'client_id', 'property_id', 'purpose_of_valuation',
        'requested_valuation_type', 'preferred_visit_date', 'preferred_visit_time',
        'site_contact_person_name', 'site_contact_mobile', 'assigned_valuator_staff_id',
        'field_visit_date', 'application_received_date', 'status', 'remarks',
    ];

    protected $casts = [
        'preferred_visit_date'        => 'date',
        'field_visit_date'            => 'date',
        'application_received_date'   => 'date',
        'created_at'                  => 'datetime',
    ];

    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(fn (ValuationRequest $m) => $m->created_at = now());
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function assignedValuator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_valuator_staff_id', 'staff_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ValuationReport::class, 'request_id', 'request_id');
    }
}
