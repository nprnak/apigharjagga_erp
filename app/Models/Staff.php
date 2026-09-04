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
        'full_name',
        'designation',
        'mobile_no',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
}
