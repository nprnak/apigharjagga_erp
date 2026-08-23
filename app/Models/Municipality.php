<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipality extends Model
{
    protected $fillable = [
        'district_id',
        'name',
        'name_np',
        'type',
        'total_wards',
    ];

    /**
     * Municipality type constants.
     */
    public const TYPE_METROPOLITAN      = 'metropolitan_city';
    public const TYPE_SUB_METROPOLITAN  = 'sub_metropolitan_city';
    public const TYPE_MUNICIPALITY      = 'municipality';
    public const TYPE_RURAL             = 'rural_municipality';

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class);
    }

    /**
     * Human-readable label for the municipality type.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_METROPOLITAN     => 'Metropolitan City',
            self::TYPE_SUB_METROPOLITAN => 'Sub-Metropolitan City',
            self::TYPE_MUNICIPALITY     => 'Municipality',
            self::TYPE_RURAL            => 'Rural Municipality',
            default                     => ucfirst($this->type),
        };
    }
}
