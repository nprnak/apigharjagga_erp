<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectContractor extends Model
{
    protected $primaryKey = 'project_contractor_id';

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'contractor_id',
        'work_scope',
        'contract_amount',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'contract_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class, 'contractor_id', 'contractor_id');
    }
}
