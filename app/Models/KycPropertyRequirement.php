<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycPropertyRequirement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'kyc_verification_id',
        'purpose',
        'property_type',
        'preferred_location',
        'required_area',
        'estimated_budget',
        'purchase_timeline',
        'updated_at',
    ];

    protected $casts = [
        'estimated_budget' => 'decimal:2',
        'updated_at' => 'datetime',
    ];

    public function kycVerification(): BelongsTo
    {
        return $this->belongsTo(KycVerification::class, 'kyc_verification_id', 'id');
    }
}
