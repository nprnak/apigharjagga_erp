<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $primaryKey = 'attendance_id';

    public $timestamps = false;

    protected $fillable = [
        'staff_id',
        'attendance_date',
        'status',
        'check_in',
        'check_out',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }
}
