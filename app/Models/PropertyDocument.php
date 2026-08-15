<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $doc_id
 * @property int $property_id
 * @property int $doc_type_id
 * @property string $file_path
 * @property string|null $description
 */
class PropertyDocument extends Model
{
    protected $primaryKey = 'doc_id';

    protected $fillable = ['property_id', 'doc_type_id', 'file_path', 'description', 'uploaded_by'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function docType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'doc_type_id', 'doc_type_id');
    }
}
