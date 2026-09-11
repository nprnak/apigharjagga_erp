<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycServiceRequest extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'kyc_verification_id',
        'service_type_id',
        'requested_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
    ];

    public function kycVerification(): BelongsTo
    {
        return $this->belongsTo(KycVerification::class, 'kyc_verification_id', 'id');
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id', 'service_type_id');
    }
}
