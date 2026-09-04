<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceOrder extends Model
{
    protected $primaryKey = 'order_id';

    public $timestamps = false;

    protected $fillable = [
        'order_no',
        'client_id',
        'property_id',
        'order_date',
        'status',
    ];

    protected $casts = [
        'order_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function completionCertificate(): HasOne
    {
        return $this->hasOne(ServiceCompletionCertificate::class, 'service_order_id', 'order_id');
    }
}
