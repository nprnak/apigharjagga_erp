<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceReview extends Model
{
    protected $primaryKey = 'review_id';

    protected $fillable = [
        'staff_id',
        'reviewer_staff_id',
        'review_period',
        'rating',
        'strengths',
        'areas_for_improvement',
        'review_date',
    ];

    protected $casts = [
        'review_date' => 'date',
        'rating' => 'integer',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'reviewer_staff_id', 'staff_id');
    }
}
