<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends Model
{
    protected $primaryKey = 'post_id';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image_path',
        'category',
        'author_staff_id',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $appends = [
        'cover_image_url',
    ];

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image_path ? '/storage/'.ltrim($this->cover_image_path, '/') : null;
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'author_staff_id', 'staff_id');
    }
}
