<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectProgressLog extends Model
{
    protected $primaryKey = 'progress_id';

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'log_date',
        'percent_complete',
        'description',
        'logged_by_staff_id',
    ];

    protected $casts = [
        'log_date' => 'date',
        'percent_complete' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function loggedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'logged_by_staff_id', 'staff_id');
    }
}
