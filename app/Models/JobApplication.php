<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    protected $primaryKey = 'application_id';

    public $timestamps = false;

    protected $fillable = [
        'job_id',
        'applicant_name',
        'email',
        'phone',
        'resume_path',
        'cover_letter',
        'status',
        'applied_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class, 'job_id', 'job_id');
    }
}
