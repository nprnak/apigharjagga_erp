<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyPhoto extends Model
{
    protected $primaryKey = 'photo_id';

    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'source_type',
        'source_id',
        'photo_type',
        'file_ref',
        'caption',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    protected $appends = [
        'photo_url',
    ];

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->file_ref) {
            return null;
        }

        if (str_starts_with($this->file_ref, 'http://') || str_starts_with($this->file_ref, 'https://')) {
            return $this->file_ref;
        }

        return '/storage/' . ltrim($this->file_ref, '/');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }
}
