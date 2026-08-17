<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintEvidence extends Model
{
    public $timestamps = false;

    protected $table = 'complaint_evidence';

    protected $fillable = [
        'complaint_id',
        'evidence_type',
        'file_ref',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class, 'complaint_id', 'complaint_id');
    }
}
