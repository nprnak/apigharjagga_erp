<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read float|null $boq_items_sum_amount Present only when loaded via withSum('boqItems', 'amount')
 * @property-read float|null $vouchers_sum_amount Present only when loaded via the aliased withSum used in ProjectReports
 */
class Project extends Model
{
    protected $primaryKey = 'project_id';

    protected $fillable = [
        'project_code',
        'project_name',
        'client_id',
        'property_id',
        'assigned_engineer_staff_id',
        'status',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'description',
        'completion_certificate_path',
        'created_by_staff_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function assignedEngineer(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_engineer_staff_id', 'staff_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by_staff_id', 'staff_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class, 'project_id', 'project_id');
    }

    public function siteVisits(): HasMany
    {
        return $this->hasMany(ProjectSiteVisit::class, 'project_id', 'project_id');
    }

    public function progressLogs(): HasMany
    {
        return $this->hasMany(ProjectProgressLog::class, 'project_id', 'project_id');
    }

    public function boqItems(): HasMany
    {
        return $this->hasMany(BoqItem::class, 'project_id', 'project_id');
    }

    public function contractorAssignments(): HasMany
    {
        return $this->hasMany(ProjectContractor::class, 'project_id', 'project_id');
    }

    public function materialRecords(): HasMany
    {
        return $this->hasMany(MaterialRecord::class, 'project_id', 'project_id');
    }

    public function inspectionReports(): HasMany
    {
        return $this->hasMany(ProjectInspectionReport::class, 'project_id', 'project_id');
    }

    public function paymentVouchers(): HasMany
    {
        return $this->hasMany(PaymentVoucher::class, 'project_id', 'project_id');
    }
}
