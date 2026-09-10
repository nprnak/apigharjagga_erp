<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentVoucher extends Model
{
    protected $primaryKey = 'voucher_id';

    protected $fillable = [
        'voucher_no',
        'voucher_date',
        'payee_name',
        'purpose',
        'account_id',
        'amount',
        'mode_of_payment',
        'cheque_no',
        'bank_name',
        'cheque_date',
        'status',
        'approved_by_staff_id',
        'created_by_staff_id',
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'cheque_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'account_id', 'account_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'approved_by_staff_id', 'staff_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by_staff_id', 'staff_id');
    }
}
