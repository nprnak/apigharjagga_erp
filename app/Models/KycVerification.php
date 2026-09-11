<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $full_name
 * @property string|null $father_mother_name
 * @property string|null $spouse_name
 * @property string|null $grandfather_name
 * @property string|null $citizenship_no
 * @property string|null $date_of_birth
 * @property string|null $gender
 * @property string|null $nationality
 * @property string|null $occupation
 * @property string|null $mobile_no
 * @property string|null $alt_contact_no
 * @property string|null $telephone_no
 * @property string|null $email
 * @property string|null $permanent_province
 * @property string|null $permanent_district
 * @property string|null $permanent_municipality
 * @property string|null $permanent_ward_no
 * @property string|null $permanent_tole
 * @property string|null $current_province
 * @property string|null $current_district
 * @property string|null $current_municipality
 * @property string|null $current_ward_no
 * @property string|null $current_tole
 * @property string $id_document_path
 * @property string|null $selfie_photo_path
 * @property string|null $signature_path
 * @property Carbon|null $signature_date
 * @property string $id_type
 * @property string $status
 * @property int|null $verified_by_staff_id
 * @property Carbon|null $verified_at
 * @property int|null $approved_by_staff_id
 * @property Carbon|null $approved_at
 * @property string|null $admin_note
 * @property Carbon|null $submitted_at
 * @property Carbon|null $reviewed_at
 */
class KycVerification extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'full_name',
        'father_mother_name',
        'spouse_name',
        'grandfather_name',
        'citizenship_no',
        'date_of_birth',
        'gender',
        'nationality',
        'occupation',
        'mobile_no',
        'alt_contact_no',
        'telephone_no',
        'email',
        'permanent_province',
        'permanent_district',
        'permanent_municipality',
        'permanent_ward_no',
        'permanent_tole',
        'current_province',
        'current_district',
        'current_municipality',
        'current_ward_no',
        'current_tole',
        'id_document_path',
        'selfie_photo_path',
        'signature_path',
        'signature_date',
        'id_type',
        'status',
        'verified_by_staff_id',
        'verified_at',
        'approved_by_staff_id',
        'approved_at',
        'admin_note',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'date_of_birth' => 'date',
        'signature_date' => 'datetime',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'verified_by_staff_id', 'staff_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'approved_by_staff_id', 'staff_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(KycVerificationDocument::class, 'kyc_verification_id', 'id');
    }

    /**
     * Annex F §4 — only present when the applicant registered on behalf of
     * an organization; a HasOne so it's easy to check "is this applicable"
     * via `$kyc->organization`.
     */
    public function organization(): HasOne
    {
        return $this->hasOne(KycOrganizationDetail::class, 'kyc_verification_id', 'id');
    }

    /**
     * Annex F §5 — the applicant's property search profile (Buyer/Investor/Tenant).
     */
    public function propertyRequirement(): HasOne
    {
        return $this->hasOne(KycPropertyRequirement::class, 'kyc_verification_id', 'id');
    }

    /**
     * Annex F §7 — the services the applicant has requested.
     */
    public function serviceRequests(): HasMany
    {
        return $this->hasMany(KycServiceRequest::class, 'kyc_verification_id', 'id');
    }

    /**
     * The fixed Annex-F document checklist every applicant must satisfy.
     * Kept as a single source of truth so the Wizard step, the admin
     * resource, and the PDF export all list the same required documents.
     */
    public static function requiredDocumentTypeNames(): array
    {
        return ['Citizenship Copy', 'Passport Size Photo', 'Proof of Current Address'];
    }
}
