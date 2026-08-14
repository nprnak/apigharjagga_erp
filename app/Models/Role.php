<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $role_id
 * @property string $role_name
 */
class Role extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'role_id';

    protected $fillable = ['role_name'];

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class, 'role_id', 'role_id');
    }
}
