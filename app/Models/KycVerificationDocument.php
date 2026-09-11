<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class KycVerificationDocument extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'kyc_verification_id',
        'doc_type_id',
        'file_ref',
        'status',
        'updated_at',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function kycVerification(): BelongsTo
    {
        return $this->belongsTo(KycVerification::class, 'kyc_verification_id', 'id');
    }

    public function docType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'doc_type_id', 'doc_type_id');
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_ref ? Storage::disk('public')->url($this->file_ref) : null;
    }
}
