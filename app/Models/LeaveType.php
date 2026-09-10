<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    protected $primaryKey = 'leave_type_id';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'default_days_per_year',
    ];

    public function requests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'leave_type_id', 'leave_type_id');
    }
}
