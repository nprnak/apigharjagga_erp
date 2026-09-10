<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $primaryKey = 'testimonial_id';

    protected $fillable = [
        'client_name',
        'client_role',
        'client_photo_path',
        'rating',
        'message',
        'is_featured',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'client_photo_url',
    ];

    public function getClientPhotoUrlAttribute(): ?string
    {
        return $this->client_photo_path ? '/storage/'.ltrim($this->client_photo_path, '/') : null;
    }
}
