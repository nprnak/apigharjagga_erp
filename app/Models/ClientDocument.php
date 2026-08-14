<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $doc_id
 * @property int $client_id
 * @property int $doc_type_id
 * @property string $file_path
 * @property string|null $description
 */
class ClientDocument extends Model
{
    protected $primaryKey = 'doc_id';

    protected $fillable = ['client_id', 'doc_type_id', 'file_path', 'description', 'uploaded_by'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function docType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'doc_type_id', 'doc_type_id');
    }
}
