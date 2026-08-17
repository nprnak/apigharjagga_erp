<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complaint extends Model
{
    protected $primaryKey = 'complaint_id';

    protected $fillable = [
        'complaint_code',
        'complaint_date',
        'complaint_time',
        'received_through',
        'received_through_other',
        'received_by_staff_id',
        'received_by_name',
        'received_by_designation',
        'received_by_signature_path',
        'received_by_date',
        'client_id',
        'property_id',
        'property_location',
        'kitta_no',
        'service_reference',
        'service_date',
        'category',
        'category_other',
        'description',
        'priority',
        'assigned_department',
        'assigned_officer_staff_id',
        'assigned_officer_name',
        'investigation_date',
        'findings',
        'corrective_action_taken',
        'resolution_date',
        'status',
        'satisfaction_level',
        'customer_remarks',
        'customer_signature_name',
        'customer_signature_path',
        'customer_signature_date',
        'reviewed_by_name',
        'reviewed_by_designation',
        'reviewed_by_signature_path',
        'reviewed_by_date',
    ];

    protected $casts = [
        'complaint_date'          => 'date',
        'service_date'            => 'date',
        'investigation_date'      => 'date',
        'resolution_date'         => 'date',
        'received_by_date'        => 'date',
        'customer_signature_date' => 'date',
        'reviewed_by_date'        => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(ComplaintEvidence::class, 'complaint_id', 'complaint_id');
    }
}
