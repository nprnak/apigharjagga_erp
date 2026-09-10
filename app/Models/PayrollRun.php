<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRun extends Model
{
    protected $primaryKey = 'payroll_run_id';

    protected $fillable = [
        'period_month',
        'status',
        'finalized_at',
        'created_by_staff_id',
    ];

    protected $casts = [
        'finalized_at' => 'datetime',
    ];

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class, 'payroll_run_id', 'payroll_run_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by_staff_id', 'staff_id');
    }
}
