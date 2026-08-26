<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ward extends Model
{
    protected $fillable = [
        'municipality_id',
        'ward_number',
    ];

    protected $casts = [
        'ward_number' => 'integer',
    ];

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Display label, e.g. "Ward 5".
     */
    public function getLabelAttribute(): string
    {
        return 'Ward ' . $this->ward_number;
    }
}
