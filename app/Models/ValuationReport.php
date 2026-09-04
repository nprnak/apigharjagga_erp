<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValuationReport extends Model
{
    protected $primaryKey = 'report_id';

    public $timestamps = false;

    protected $fillable = [
        'report_no',
        'request_id',
        'property_id',
        'valuation_type',
        'land_area',
        'land_rate',
        'building_area',
        'building_rate',
        'depreciation_percent',
        'adjustment_amount',
        'valuated_amount',
        'rate_basis',
        'valuator_staff_id',
        'approved_by_staff_id',
        'approval_status',
        'digitally_signed',
        'report_file_ref',
        'issued_date',
    ];

    protected $casts = [
        'land_area' => 'decimal:4',
        'land_rate' => 'decimal:2',
        'building_area' => 'decimal:4',
        'building_rate' => 'decimal:2',
        'depreciation_percent' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'valuated_amount' => 'decimal:2',
        'digitally_signed' => 'boolean',
        'issued_date' => 'date',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(ValuationRequest::class, 'request_id', 'request_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function valuator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'valuator_staff_id', 'staff_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'approved_by_staff_id', 'staff_id');
    }
}
