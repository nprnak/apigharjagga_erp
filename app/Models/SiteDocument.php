<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteDocument extends Model
{
    protected $primaryKey = 'document_id';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'category',
        'file_path',
        'is_public',
        'uploaded_at',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'uploaded_at' => 'datetime',
    ];

    protected $appends = [
        'file_url',
    ];

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? '/storage/'.ltrim($this->file_path, '/') : null;
    }
}
