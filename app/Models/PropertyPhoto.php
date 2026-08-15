<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $photo_id
 * @property int $property_id
 * @property string $file_path
 * @property string|null $caption
 * @property bool $is_primary
 */
class PropertyPhoto extends Model
{
    protected $primaryKey = 'photo_id';

    protected $fillable = ['property_id', 'file_path', 'caption', 'is_primary', 'uploaded_by'];

    protected $casts = ['is_primary' => 'boolean'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }
}
