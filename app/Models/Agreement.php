<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agreement extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'agreement_id';

    protected $fillable = [
        'agreement_type',
        'property_id',
        'house_description',
        'boundary_east',
        'boundary_west',
        'boundary_north',
        'boundary_south',
        'agreement_date',
        'place',
        'total_price',
        'total_price_words',
        'advance_payment',
        'balance_payment',
        'final_payment_date',
        'commission_rate_percent',
        'commission_fixed_amount',
        'agreement_period_months',
        'termination_notice_days',
        'status',
        'governing_law',
        'seller_signature_path',
        'buyer_signature_path',
    ];

    protected $casts = [
        'agreement_date'          => 'date',
        'final_payment_date'      => 'date',
        'total_price'             => 'decimal:2',
        'advance_payment'         => 'decimal:2',
        'balance_payment'         => 'decimal:2',
        'commission_rate_percent' => 'decimal:2',
        'commission_fixed_amount' => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function parties(): HasMany
    {
        return $this->hasMany(AgreementParty::class, 'agreement_id', 'agreement_id');
    }

    public function witnesses(): HasMany
    {
        return $this->hasMany(AgreementWitness::class, 'agreement_id', 'agreement_id');
    }

    public function seller(): ?AgreementParty
    {
        return $this->parties->firstWhere('party_role', 'seller');
    }

    public function buyer(): ?AgreementParty
    {
        return $this->parties->firstWhere('party_role', 'buyer');
    }
}
