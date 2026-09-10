<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Internal employee directory (Valuer, Survey Officer, Manager, ...), distinct
 * from the `users` login table. Staff are assignable to complaints and
 * valuation requests without needing a system login.
 */
class Staff extends Model
{
    protected $primaryKey = 'staff_id';

    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'employee_code',
        'full_name',
        'designation',
        'department',
        'employment_type',
        'date_of_joining',
        'basic_salary',
        'date_of_birth',
        'gender',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'mobile_no',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'date_of_joining' => 'date',
        'date_of_birth' => 'date',
        'basic_salary' => 'decimal:2',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function assignedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'assigned_officer_staff_id', 'staff_id');
    }

    public function assignedValuationRequests(): HasMany
    {
        return $this->hasMany(ValuationRequest::class, 'assigned_valuator_staff_id', 'staff_id');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'staff_id', 'staff_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'staff_id', 'staff_id');
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class, 'staff_id', 'staff_id');
    }

    public function performanceReviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class, 'staff_id', 'staff_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StaffDocument::class, 'staff_id', 'staff_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(StaffContract::class, 'staff_id', 'staff_id');
    }
}
