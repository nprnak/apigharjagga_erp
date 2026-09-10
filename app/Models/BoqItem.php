<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoqItem extends Model
{
    protected $primaryKey = 'boq_item_id';

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'item_description',
        'unit',
        'quantity',
        'rate',
        'amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}
