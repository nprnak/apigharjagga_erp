<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $request_id
 * @property int $client_id
 * @property int $service_type_id
 * @property string $status
 * @property string|null $notes
 */
class ClientServiceRequest extends Model
{
    protected $primaryKey = 'request_id';

    protected $fillable = [
        'client_id', 'service_type_id', 'status', 'notes', 'assigned_to',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id', 'service_type_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_to', 'staff_id');
    }
}
