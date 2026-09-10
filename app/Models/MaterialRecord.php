<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialRecord extends Model
{
    protected $primaryKey = 'material_id';

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'material_name',
        'quantity',
        'unit',
        'unit_cost',
        'total_cost',
        'supplier',
        'received_date',
        'recorded_by_staff_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'received_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'recorded_by_staff_id', 'staff_id');
    }
}
