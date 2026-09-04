<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyHandoverCertificate extends Model
{
    protected $primaryKey = 'handover_id';

    public $timestamps = false;

    protected $fillable = [
        'certificate_no',
        'handover_date',
        'place',
        'owner_client_id',
        'company_rep_staff_id',
        'property_id',
        'purpose',
        'possession_status',
        'keys_status',
        'ownership_docs_status',
        'tax_docs_status',
        'utility_docs_status',
        'land_boundary_condition',
        'building_structure_condition',
        'electrical_condition',
        'water_supply_condition',
        'sanitation_condition',
        'furniture_equipment_status',
    ];

    protected $casts = [
        'handover_date' => 'date',
    ];

    public function ownerClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'owner_client_id', 'client_id');
    }

    public function companyRep(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'company_rep_staff_id', 'staff_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }
}
