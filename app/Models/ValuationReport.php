<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $report_id
 * @property int $valuation_id
 * @property string $report_no
 * @property float|null $market_value
 * @property float|null $forced_sale_value
 * @property string $approval_status
 */
class ValuationReport extends Model
{
    protected $primaryKey = 'report_id';

    protected $fillable = [
        'report_no', 'request_id', 'property_id', 'valuation_type',
        'valuated_amount', 'rate_basis', 'valuator_staff_id', 'approved_by_staff_id',
        'approval_status', 'digitally_signed', 'report_file_ref', 'issued_date',
    ];

    protected $casts = [
        'issued_date'      => 'date',
        'valuated_amount'  => 'decimal:2',
        'digitally_signed' => 'boolean',
        'created_at'       => 'datetime',
    ];

    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(fn (ValuationReport $m) => $m->created_at = now());
    }

    public function valuationRequest(): BelongsTo
    {
        return $this->belongsTo(ValuationRequest::class, 'request_id', 'request_id');
    }

    public function valuator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'valuator_staff_id', 'staff_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'approved_by_staff_id', 'staff_id');
    }
}
