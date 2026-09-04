<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReceipt extends Model
{
    protected $primaryKey = 'receipt_id';

    public $timestamps = false;

    protected $fillable = [
        'receipt_no',
        'receipt_date',
        'client_id',
        'agreement_id',
        'property_id',
        'amount',
        'amount_in_words',
        'purpose',
        'mode_of_payment',
        'cheque_no',
        'bank_name',
        'cheque_date',
        'received_by_staff_id',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'amount' => 'decimal:2',
        'cheque_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function agreement(): BelongsTo
    {
        return $this->belongsTo(Agreement::class, 'agreement_id', 'agreement_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'received_by_staff_id', 'staff_id');
    }
}
