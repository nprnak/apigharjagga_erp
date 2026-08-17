<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientDocument extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'client_doc_id';

    protected $fillable = [
        'client_id',
        'doc_type_id',
        'status',
        'file_ref',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'doc_type_id', 'doc_type_id');
    }
}
