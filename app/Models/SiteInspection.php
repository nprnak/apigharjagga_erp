<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Annex-D (AGJ-FRM-004) Site Inspection Checklist.
 *
 * Filled in by a "site_inspection_engineer" for a specific Property, then
 * submitted for review to either a "valuation_officer" or an "admin"/
 * "super_admin". See app/Filament/Resources/SiteInspectionResource.php for
 * the workflow (draft -> submitted -> reviewed).
 */
class SiteInspection extends Model
{
    protected $primaryKey = 'inspection_id';

    /**
     * §2. Land Site Inspection Checklist — fixed 9 items, key => label.
     */
    public const LAND_ITEMS = [
        'ownership_documents_verified' => 'Ownership documents verified',
        'land_boundary_identified' => 'Land boundary identified',
        'land_area_verified' => 'Land area verified',
        'road_access_available' => 'Road access available',
        'road_width_measured' => 'Road width measured',
        'land_use_verified' => 'Land use verified',
        'surrounding_development_assessed' => 'Surrounding development assessed',
        'drainage_condition_checked' => 'Drainage condition checked',
        'risk_factors_identified' => 'Risk factors identified',
    ];

    /**
     * §3. Building / House Inspection Checklist — fixed 10 items.
     */
    public const BUILDING_ITEMS = [
        'building_structure_inspected' => 'Building structure inspected',
        'foundation_condition_checked' => 'Foundation condition checked',
        'rcc_steel_structure_verified' => 'RCC/Steel structure verified',
        'number_of_floors_verified' => 'Number of floors verified',
        'built_up_area_measured' => 'Built-up area measured',
        'wall_condition_checked' => 'Wall condition checked',
        'roof_condition_checked' => 'Roof condition checked',
        'electrical_system_inspected' => 'Electrical system inspected',
        'plumbing_system_inspected' => 'Plumbing system inspected',
        'overall_building_condition_assessed' => 'Overall building condition assessed',
    ];

    /**
     * §5. Photo Documentation Checklist — fixed 7 items.
     */
    public const PHOTO_ITEMS = [
        'front_view' => 'Front View',
        'rear_view' => 'Rear View',
        'side_view' => 'Side View',
        'road_access_photo' => 'Road Access Photo',
        'boundary_photo' => 'Boundary Photo',
        'interior_photo' => 'Interior Photo',
        'surrounding_area_photo' => 'Surrounding Area Photo',
    ];

    /**
     * §6. Documents Verified — fixed 6 items.
     */
    public const DOCUMENT_ITEMS = [
        'lalpurja' => 'Lalpurja / Ownership Certificate',
        'citizenship_certificate' => 'Citizenship Certificate',
        'tax_clearance' => 'Tax Clearance',
        'building_approval' => 'Building Approval',
        'utility_bills' => 'Utility Bills',
        'other_documents' => 'Other Documents',
    ];

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_REVIEWED = 'reviewed';

    protected $fillable = [
        'property_id',
        'listing_id',
        'inspector_user_id',
        'inspector_designation',
        'inspection_date',
        'property_owner_name',
        'contact_number',
        'property_location',
        'municipality',
        'ward_no',
        'distance_from_main_road',
        'nearby_facilities',
        'commercial_potential',
        'residential_suitability',
        'future_development_potential',
        'observation_notes',
        'land_checklist',
        'building_checklist',
        'photo_checklist',
        'documents_checklist',
        'final_status',
        'status',
        'submitted_to',
        'submitted_at',
        'reviewed_by_user_id',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'land_checklist' => 'array',
        'building_checklist' => 'array',
        'photo_checklist' => 'array',
        'documents_checklist' => 'array',
    ];

    /**
     * Blank checklist shape for a brand-new inspection: every item
     * unverified/unchecked with no remarks, ready for the engineer to fill.
     */
    public static function defaultChecklists(): array
    {
        return [
            'land_checklist' => array_fill_keys(array_keys(self::LAND_ITEMS), ['verified' => null, 'remarks' => null]),
            'building_checklist' => array_fill_keys(array_keys(self::BUILDING_ITEMS), ['verified' => null, 'remarks' => null]),
            'photo_checklist' => array_fill_keys(array_keys(self::PHOTO_ITEMS), false),
            'documents_checklist' => array_fill_keys(array_keys(self::DOCUMENT_ITEMS), false),
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(PropertyListing::class, 'listing_id', 'listing_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
