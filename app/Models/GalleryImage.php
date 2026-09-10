<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryImage extends Model
{
    protected $primaryKey = 'image_id';

    public $timestamps = false;

    protected $fillable = [
        'album_id',
        'image_path',
        'caption',
        'display_order',
    ];

    protected $appends = [
        'image_url',
    ];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? '/storage/'.ltrim($this->image_path, '/') : null;
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(GalleryAlbum::class, 'album_id', 'album_id');
    }
}
