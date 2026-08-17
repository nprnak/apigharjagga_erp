<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientPropertyRequirement extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'requirement_id';

    protected $fillable = [
        'client_id',
        'purpose',
        'property_type',
        'preferred_location',
        'required_area',
        'estimated_budget',
        'purchase_timeline',
    ];

    protected $casts = [
        'estimated_budget' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }
}
