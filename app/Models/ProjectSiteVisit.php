<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectSiteVisit extends Model
{
    protected $primaryKey = 'site_visit_id';

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'visit_date',
        'visited_by_staff_id',
        'notes',
        'photo_paths',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'photo_paths' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function visitedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'visited_by_staff_id', 'staff_id');
    }
}
