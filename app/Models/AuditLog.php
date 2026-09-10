<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Read-only audit trail row for a change to a regulated entity
 * (ownership, price, status, ...). Rows are written by application code
 * at the point of change, never edited or deleted through the UI.
 */
class AuditLog extends Model
{
    protected $primaryKey = 'log_id';

    public $timestamps = false;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'action',
        'performed_by_staff_id',
        'performed_at',
        'old_value',
        'new_value',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'performed_by_staff_id', 'staff_id');
    }
}
