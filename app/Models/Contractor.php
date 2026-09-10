<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contractor extends Model
{
    protected $primaryKey = 'contractor_id';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'contact_person',
        'mobile_no',
        'email',
        'specialization',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function projectAssignments(): HasMany
    {
        return $this->hasMany(ProjectContractor::class, 'contractor_id', 'contractor_id');
    }
}
