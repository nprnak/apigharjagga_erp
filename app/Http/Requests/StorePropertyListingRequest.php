<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Section 1: Applicant Details ──────────────────────────────
            'full_name_en'     => ['required', 'string', 'max:150'],
            'full_name_np'     => ['nullable', 'string', 'max:150'],
            'citizenship_no'   => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9\-\/]+$/'],
            'date_of_birth'    => ['nullable', 'date', 'before:today'],
            'father_name'      => ['nullable', 'string', 'max:150'],
            'grandfather_name' => ['nullable', 'string', 'max:150'],
            'permanent_address'=> ['nullable', 'string', 'max:500'],
            'current_address'  => ['nullable', 'string', 'max:500'],
            // Nepal mobile: exactly 10 digits starting with 9
            'mobile_no'        => ['required', 'digits:10', 'regex:/^9[0-9]{9}$/'],
            // Landline: 7–10 digits, optional area code
            'telephone_no'     => ['nullable', 'digits_between:7,10'],
            'email'            => ['nullable', 'email:rfc,dns', 'max:150'],
            'occupation'       => ['nullable', 'string', 'max:100'],

            // ── Section 2: Property Owner Details ────────────────────────
            'ownership_role' => ['required', 'in:self,family_member,authorized_representative,company'],

            // ── Section 3: Property Details ───────────────────────────────
            'property_type'       => ['required', 'in:land,house,apartment,commercial_building,office_space,industrial_property,agricultural_land,other'],
            'property_type_other' => ['nullable', 'required_if:property_type,other', 'string', 'max:100'],
            'province'            => ['required', 'string', 'max:100'],
            'district'            => ['required', 'string', 'max:100'],
            'municipality'        => ['required', 'string', 'max:150'],
            'ward_no'             => ['required', 'string', 'max:10', 'regex:/^[0-9]+$/'],
            'tole'                => ['nullable', 'string', 'max:150'],
            // GPS: optional, must be valid lat,lng
            'gps_location'        => ['nullable', 'regex:/^-?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*-?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/'],
            // Land info
            'kitta_no'            => ['nullable', 'string', 'max:50'],
            'area'                => ['nullable', 'string', 'max:100'],
            'map_sheet_no'        => ['nullable', 'string', 'max:50'],
            'ownership_type'      => ['nullable', 'string', 'max:50'],
            'road_access'         => ['nullable', 'string', 'max:20'],
            'road_width'          => ['nullable', 'string', 'max:50'],
            'facing_direction'    => ['nullable', 'string', 'max:30'],
            // Building details
            'year_of_construction'=> ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'no_of_floors'        => ['nullable', 'integer', 'min:1', 'max:200'],
            'covered_area'        => ['nullable', 'string', 'max:100'],
            'structure_type'      => ['nullable', 'in:RCC,Load Bearing,Steel,Other'],
            'roof_type'           => ['nullable', 'string', 'max:50'],
            'parking'             => ['nullable', 'string', 'max:50'],
            'water_supply'        => ['nullable', 'string', 'max:50'],
            'electricity'         => ['nullable', 'string', 'max:50'],
            'internet'            => ['nullable', 'string', 'max:50'],
            'drainage'            => ['nullable', 'string', 'max:50'],

            // ── Section 4: Purpose of Listing ─────────────────────────────
            'purpose_of_listing' => ['required', 'in:sale,rent,lease,exchange,investment,other'],
            'purpose_other'      => ['nullable', 'required_if:purpose_of_listing,other', 'string', 'max:150'],

            // ── Section 5: Expected Price ──────────────────────────────────
            // Prices: numeric, no negative values, max 14 digits with 2 decimal places
            'expected_selling_price'   => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'negotiable'               => ['nullable', 'in:yes,no'],
            'minimum_acceptable_price' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'rental_amount'            => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],

            // ── Section 6: Documents ──────────────────────────────────────
            'submitted_documents' => ['nullable', 'array'],
            'submitted_documents.*'=> ['string'],
            'other_documents'     => ['nullable', 'string', 'max:500'],

            // ── Section 7: Features ───────────────────────────────────────
            'property_features'   => ['nullable', 'array'],
            'property_features.*' => ['string'],
            'other_features'      => ['nullable', 'string', 'max:500'],

            // ── Section 8: Declaration ────────────────────────────────────
            'declaration_agreed' => ['required', 'accepted'],

            // ── Section 9: Signatures ─────────────────────────────────────
            'applicant_name'      => ['required', 'string', 'max:150'],
            'applicant_date'      => ['nullable', 'date', 'before_or_equal:today'],
            'applicant_signature' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile_no.digits'         => 'Mobile number must be exactly 10 digits.',
            'mobile_no.regex'          => 'Mobile number must start with 9 (e.g. 98XXXXXXXX).',
            'telephone_no.digits_between' => 'Telephone number must be between 7 and 10 digits.',
            'citizenship_no.regex'     => 'Citizenship number format is invalid.',
            'ward_no.regex'            => 'Ward number must contain digits only.',
            'gps_location.regex'       => 'GPS must be in format: latitude, longitude (e.g. 27.7172, 85.3240).',
            'expected_selling_price.min'   => 'Selling price cannot be negative.',
            'minimum_acceptable_price.min' => 'Minimum price cannot be negative.',
            'rental_amount.min'            => 'Rental amount cannot be negative.',
            'year_of_construction.min' => 'Construction year cannot be before 1900.',
            'year_of_construction.max' => 'Construction year cannot be in the future.',
            'no_of_floors.min'         => 'Number of floors must be at least 1.',
            'declaration_agreed.accepted'  => 'You must agree to the declaration to submit.',
            'purpose_other.required_if'    => 'Please specify the purpose when "Other" is selected.',
            'property_type_other.required_if' => 'Please specify the property type when "Other" is selected.',
            'applicant_signature.required'    => 'Please upload a scanned signature image.',
            'applicant_signature.image'       => 'Signature must be an image file.',
            'applicant_signature.mimes'       => 'Signature must be JPG, PNG or WEBP.',
            'applicant_signature.max'         => 'Signature image must be under 2 MB.',
        ];
    }
}
