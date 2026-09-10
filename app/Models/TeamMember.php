<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $primaryKey = 'member_id';

    protected $fillable = [
        'name',
        'designation',
        'department',
        'photo_path',
        'bio',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'photo_url',
    ];

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? '/storage/'.ltrim($this->photo_path, '/') : null;
    }
}
