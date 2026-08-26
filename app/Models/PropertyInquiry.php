<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyInquiry extends Model
{
    protected $primaryKey = 'inquiry_id';

    protected $fillable = [
        'property_id',
        'listing_id',
        'name',
        'phone',
        'email',
        'message',
        'status',
        'admin_note',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(PropertyListing::class, 'listing_id', 'listing_id');
    }
}
