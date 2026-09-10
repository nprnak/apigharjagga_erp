<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GalleryAlbum extends Model
{
    protected $primaryKey = 'album_id';

    protected $fillable = [
        'title',
        'description',
        'cover_image_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'cover_image_url',
    ];

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image_path ? '/storage/'.ltrim($this->cover_image_path, '/') : null;
    }

    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class, 'album_id', 'album_id');
    }
}
