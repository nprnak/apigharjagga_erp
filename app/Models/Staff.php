<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $staff_id
 * @property int $role_id
 * @property string $full_name
 * @property string|null $designation
 * @property string|null $mobile_no
 * @property string|null $email
 * @property bool $is_active
 */
class Staff extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'staff_id';

    protected $fillable = [
        'role_id', 'full_name', 'designation', 'mobile_no', 'email', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }
}
