<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $digits = fn (?string $value): ?string => $value === null
            ? null
            : preg_replace('/\D+/', '', $value);

        $this->merge([
            'mobile_no'       => $digits($this->input('mobile_no')),
            'alt_contact_no'  => $digits($this->input('alt_contact_no')),
            'citizenship_no'  => $this->filled('citizenship_no') ? trim((string) $this->input('citizenship_no')) : null,
        ]);

        $value = $this->input('declaration_agreed');
        if (in_array($value, [true, 1, '1', 'true', 'on', 'yes'], true)) {
            $this->merge(['declaration_agreed' => '1']);
        }
    }

    public function rules(): array
    {
        $nameRule = ['regex:/^[\p{L}][\p{L}\s.\-\']*$/u'];
        $imageRules = ['image', 'mimes:jpeg,jpg,png,webp', 'max:2048'];

        return [
            // 1. Client Type
            'client_type'       => ['required', 'in:owner,buyer,investor,tenant,agent,other'],
            'client_type_other' => ['nullable', 'required_if:client_type,other', 'string', 'max:100'],

            // 2. Personal
            'full_name'          => ['required', 'string', 'min:2', 'max:150', ...$nameRule],
            'father_mother_name' => ['nullable', 'string', 'min:2', 'max:150', ...$nameRule],
            'spouse_name'        => ['nullable', 'string', 'min:2', 'max:150', ...$nameRule],
            'citizenship_no'     => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9][A-Za-z0-9\-\/]*$/', 'unique:clients,citizenship_no'],
            'nationality'        => ['nullable', 'string', 'max:50'],
            'date_of_birth'      => ['nullable', 'date', 'before:today'],
            'gender'             => ['nullable', 'in:male,female,other'],
            'occupation'         => ['nullable', 'string', 'max:100'],

            // 3. Contact
            'mobile_no'            => ['required', 'digits:10', 'regex:/^9[0-9]{9}$/'],
            'alt_contact_no'       => ['nullable', 'digits:10', 'regex:/^9[0-9]{9}$/'],
            'email'                => ['nullable', 'email:rfc', 'max:150'],
            'permanent_address'    => ['required', 'string', 'min:8', 'max:500'],
            'current_address'      => ['nullable', 'string', 'max:500'],

            // 4. Organization
            'organization_name' => ['nullable', 'string', 'max:200'],
            'registration_no'   => ['nullable', 'string', 'max:50'],
            'pan_vat_no'        => ['nullable', 'string', 'max:50'],
            'authorized_person' => ['nullable', 'string', 'max:150', ...$nameRule],
            'designation'       => ['nullable', 'string', 'max:100'],
            'office_address'    => ['nullable', 'string', 'max:500'],

            // 5. Property requirement (buyer / investor / tenant)
            'req_purpose'            => ['nullable', 'required_if:client_type,buyer,investor,tenant', 'in:purchase,investment,rent'],
            'req_property_type'      => ['nullable', 'in:land,house,apartment,commercial'],
            'req_preferred_location' => ['nullable', 'string', 'max:200'],
            'req_required_area'      => ['nullable', 'string', 'max:100'],
            'req_estimated_budget'   => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'req_purchase_timeline'  => ['nullable', 'string', 'max:100'],

            // 6. Owner listing details
            'available_for'      => ['nullable', 'required_if:client_type,owner', 'array'],
            'available_for.*'    => ['in:sale,rent,lease'],
            'property_location'  => ['nullable', 'string', 'max:200'],
            'kitta_no'           => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9\-\/]*$/'],
            'land_area'          => ['nullable', 'string', 'max:100'],
            'building_details'   => ['nullable', 'string', 'max:500'],
            'expected_price'     => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],

            // 7. Services
            'requested_services'   => ['required', 'array', 'min:1'],
            'requested_services.*' => ['in:listing,verification,valuation,digital_marketing,consultation,documentation'],

            // 8. Documents
            'document_status'                     => ['nullable', 'array'],
            'document_status.citizenship_copy'    => ['nullable', 'in:submitted,pending'],
            'document_status.ownership_certificate'=> ['nullable', 'in:submitted,pending'],
            'document_status.land_house_documents'=> ['nullable', 'in:submitted,pending'],
            'document_status.passport_photo'      => ['nullable', 'in:submitted,pending'],
            'document_status.authorization_letter'=> ['nullable', 'in:submitted,pending'],
            'document_status.other_documents'     => ['nullable', 'in:submitted,pending'],
            'other_documents_note'                => ['nullable', 'string', 'max:500'],

            // 9. Digital registration
            'registration_date'  => ['required', 'date', 'before_or_equal:today'],
            'mobile_app_user_id' => ['nullable', 'string', 'max:100'],
            'mis_entry_status'   => ['nullable', 'in:pending,completed'],
            'registered_by_name' => ['nullable', 'string', 'max:150'],
            'registered_by_designation' => ['nullable', 'string', 'max:100'],
            'registered_by_date' => ['nullable', 'date', 'before_or_equal:today'],
            'registered_by_signature' => ['nullable', ...$imageRules],
            'approved_by_name'   => ['nullable', 'string', 'max:150'],
            'approved_by_designation' => ['nullable', 'string', 'max:100'],
            'approved_by_date'   => ['nullable', 'date', 'before_or_equal:today'],
            'approved_by_signature' => ['nullable', ...$imageRules],

            // 10. Declaration
            'declaration_agreed' => ['required', 'accepted'],
            'client_signature_name' => ['required', 'string', 'max:150'],
            'client_signature_date' => ['required', 'date', 'before_or_equal:today'],
            'client_signature'      => ['required', ...$imageRules],
        ];
    }

    public function messages(): array
    {
        return [
            'client_type.required' => 'Please select a client type.',
            'client_type_other.required_if' => 'Please specify the client type.',
            'full_name.regex' => 'Name may contain letters, spaces, hyphen, apostrophe and period only.',
            'citizenship_no.unique' => 'A client with this citizenship number is already registered.',
            'citizenship_no.regex' => 'Citizenship number format is invalid.',
            'mobile_no.digits' => 'Mobile number must be exactly 10 digits.',
            'mobile_no.regex' => 'Mobile number must start with 9 (e.g. 98XXXXXXXX).',
            'alt_contact_no.digits' => 'Alternate contact must be exactly 10 digits.',
            'alt_contact_no.regex' => 'Alternate contact must start with 9 (e.g. 98XXXXXXXX).',
            'req_purpose.required_if' => 'Purpose is required for buyer, investor or tenant.',
            'available_for.required_if' => 'Please select what the property is available for.',
            'requested_services.required' => 'Please select at least one service.',
            'requested_services.min' => 'Please select at least one service.',
            'declaration_agreed.accepted' => 'You must agree to the declaration.',
            'client_signature.required' => 'Please upload the client scanned signature.',
            'client_signature.mimes' => 'Signature must be JPG, PNG or WEBP.',
            'client_signature.max' => 'Signature image must be under 2 MB.',
        ];
    }
}
