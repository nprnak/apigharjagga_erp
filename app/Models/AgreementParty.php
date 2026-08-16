<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgreementParty extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'agreement_id',
        'party_role',
        'client_id',
        'company_id',
        'representative_name',
        'designation',
    ];

    public function agreement(): BelongsTo
    {
        return $this->belongsTo(Agreement::class, 'agreement_id', 'agreement_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }
}
