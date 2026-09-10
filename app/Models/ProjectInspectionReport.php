<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectInspectionReport extends Model
{
    protected $primaryKey = 'inspection_id';

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'inspection_date',
        'inspected_by_staff_id',
        'findings',
        'result',
    ];

    protected $casts = [
        'inspection_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function inspectedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'inspected_by_staff_id', 'staff_id');
    }
}
