<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Budget extends Model
{
    protected $primaryKey = 'budget_id';

    protected $fillable = [
        'account_id',
        'fiscal_year',
        'period_type',
        'period_label',
        'allocated_amount',
        'notes',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'account_id', 'account_id');
    }

    /**
     * What has actually posted against this budget's account within its
     * period. Annual budgets with no period_label cover the whole fiscal
     * year; monthly budgets are compared against just that month.
     * Assumes `fiscal_year` is a plain 4-digit Gregorian year (e.g. "2026").
     */
    public function actualAmount(): float
    {
        if ($this->period_type === 'monthly' && $this->period_label) {
            $from = $this->period_label.'-01';
            $to = Carbon::parse($from)->endOfMonth()->toDateString();
        } else {
            $from = $this->fiscal_year.'-01-01';
            $to = $this->fiscal_year.'-12-31';
        }

        return abs($this->account->movementBetween($from, $to));
    }
}
