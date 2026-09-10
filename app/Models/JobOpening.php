<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOpening extends Model
{
    protected $primaryKey = 'job_id';

    protected $fillable = [
        'title',
        'department',
        'location',
        'employment_type',
        'description',
        'requirements',
        'posted_date',
        'closing_date',
        'is_active',
    ];

    protected $casts = [
        'posted_date' => 'date',
        'closing_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_id', 'job_id');
    }
}
