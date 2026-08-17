<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientOrganization extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'org_id';

    protected $fillable = [
        'client_id',
        'organization_name',
        'registration_no',
        'pan_vat_no',
        'authorized_person',
        'designation',
        'office_address_id',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function officeAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'office_address_id', 'address_id');
    }
}
