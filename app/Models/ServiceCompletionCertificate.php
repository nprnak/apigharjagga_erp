<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceCompletionCertificate extends Model
{
    protected $primaryKey = 'certificate_id';

    public $timestamps = false;

    protected $fillable = [
        'certificate_no',
        'issue_date',
        'client_id',
        'property_id',
        'service_order_id',
        'service_start_date',
        'service_completion_date',
        'assigned_officer_staff_id',
        'technical_reviewer_staff_id',
        'final_status',
        'client_acceptance_date',
        'client_remarks',
        'prepared_by_staff_id',
        'verified_by_staff_id',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'service_start_date' => 'date',
        'service_completion_date' => 'date',
        'client_acceptance_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class, 'service_order_id', 'order_id');
    }

    public function assignedOfficer(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_officer_staff_id', 'staff_id');
    }

    public function technicalReviewer(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'technical_reviewer_staff_id', 'staff_id');
    }
}
