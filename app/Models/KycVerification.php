<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $id_document_path
 * @property string $id_type
 * @property string $status
 * @property string|null $admin_note
 * @property Carbon|null $submitted_at
 * @property Carbon|null $reviewed_at
 */
class KycVerification extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'id_document_path',
        'id_type',
        'status',
        'admin_note',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
