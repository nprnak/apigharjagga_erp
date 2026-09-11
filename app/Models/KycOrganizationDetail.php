<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycOrganizationDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'kyc_verification_id',
        'organization_name',
        'registration_no',
        'pan_vat_no',
        'authorized_person',
        'designation',
        'office_address',
    ];

    public function kycVerification(): BelongsTo
    {
        return $this->belongsTo(KycVerification::class, 'kyc_verification_id', 'id');
    }
}
