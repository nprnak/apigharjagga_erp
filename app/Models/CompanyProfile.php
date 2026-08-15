<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanyProfile extends Model
{
    protected $table = 'company_profile';

    protected $primaryKey = 'company_id';

    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'company_name',
        'registration_no',
        'broker_licence_no',
        'land_survey_licence_no',
        'pan_vat_no',
        'registered_office_id',
        'contact_no',
        'email',
        'website',
        'tagline',
        'logo_path',
        'licence_expiry_date',
        'is_active',
    ];

    protected $casts = [
        'is_active'           => 'boolean',
        'licence_expiry_date' => 'date',
    ];

    /** Full public URL for the logo, or null if not set. */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::url($this->logo_path) : null;
    }

    /** Retrieve the single company profile row, or a default stub. */
    public static function getSingleton(): self
    {
        return self::first() ?? new self([
            'company_name' => config('app.name', 'Api Ghar Jagga'),
        ]);
    }
}
