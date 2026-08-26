<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $full_name
 * @property string|null $father_mother_name
 * @property string|null $spouse_name
 * @property string|null $citizenship_no
 * @property string|null $date_of_birth
 * @property string|null $gender
 * @property string|null $nationality
 * @property string|null $occupation
 * @property string|null $mobile_no
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
 * @property string $id_type
 * @property string $status
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
        'citizenship_no',
        'date_of_birth',
        'gender',
        'nationality',
        'occupation',
        'mobile_no',
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
        'id_type',
        'status',
        'admin_note',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
        'date_of_birth' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
