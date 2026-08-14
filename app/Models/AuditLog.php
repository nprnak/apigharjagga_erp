<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $log_id
 * @property int|null $user_id
 * @property string $action
 * @property string $table_name
 * @property int|null $record_id
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string|null $ip_address
 */
class AuditLog extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'log_id';

    protected $fillable = [
        'user_id', 'action', 'table_name', 'record_id',
        'old_values', 'new_values', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
