<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreComplaintRequest extends FormRequest
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
            'mobile_no'  => $digits($this->input('mobile_no')),
            'client_code' => $this->filled('client_code')
                ? strtoupper(trim((string) $this->input('client_code')))
                : null,
            'property_code' => $this->filled('property_code')
                ? trim((string) $this->input('property_code'))
                : null,
            'complaint_time' => $this->filled('complaint_time')
                ? substr((string) $this->input('complaint_time'), 0, 5)
                : null,
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
        $fileRules = ['file', 'mimes:jpeg,jpg,png,webp,pdf', 'max:5120'];

        return [
            // 1. Registration
            'complaint_date'          => ['required', 'date', 'before_or_equal:today'],
            'complaint_time'          => ['required', 'date_format:H:i'],
            'received_through'        => ['required', 'in:mobile_app,website,office,email,phone,other'],
            'received_through_other'  => ['nullable', 'required_if:received_through,other', 'string', 'max:100'],
            'received_by_name'        => ['nullable', 'string', 'max:150', ...$nameRule],
            'received_by_designation' => ['nullable', 'string', 'max:100'],
            'received_by_date'        => ['nullable', 'date', 'before_or_equal:today'],
            'received_by_signature'   => ['nullable', ...$imageRules],

            // 2. Customer
            'full_name'            => ['required', 'string', 'min:2', 'max:150', ...$nameRule],
            'client_code'          => ['nullable', 'string', 'max:30', 'exists:clients,client_code'],
            'mobile_no'            => ['required', 'digits:10', 'regex:/^9[0-9]{9}$/'],
            'email'                => ['nullable', 'email:rfc', 'max:150'],
            'address'              => ['required', 'string', 'min:8', 'max:500'],
            'customer_type'        => ['required', 'in:owner,buyer,investor,tenant,other'],
            'customer_type_other'  => ['nullable', 'required_if:customer_type,other', 'string', 'max:100'],

            // 3. Property
            'property_code'     => ['nullable', 'string', 'max:30', 'exists:properties,property_code'],
            'property_location' => ['nullable', 'string', 'max:200'],
            'kitta_no'          => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9\-\/]*$/'],
            'service_reference' => ['nullable', 'string', 'max:200'],
            'service_date'      => ['nullable', 'date', 'before_or_equal:today'],

            // 4. Category
            'category'       => ['required', 'in:property_listing_issue,property_information_incorrect,valuation_related_issue,site_visit_issue,digital_platform_issue,staff_service_behaviour,payment_billing_issue,documentation_issue,other'],
            'category_other' => ['nullable', 'required_if:category,other', 'string', 'max:150'],

            // 5. Description
            'description' => ['required', 'string', 'min:20', 'max:4000'],

            // 6. Evidence
            'attached_evidence'   => ['nullable', 'array'],
            'attached_evidence.*' => ['in:photo,screenshot,agreement_copy,payment_receipt,other'],
            'evidence_other_note' => ['nullable', 'string', 'max:500'],
            'evidence_files'      => ['nullable', 'array'],
            'evidence_files.photo'            => ['nullable', ...$fileRules],
            'evidence_files.screenshot'       => ['nullable', ...$fileRules],
            'evidence_files.agreement_copy'   => ['nullable', ...$fileRules],
            'evidence_files.payment_receipt'  => ['nullable', ...$fileRules],
            'evidence_files.other'            => ['nullable', ...$fileRules],

            // 7. Priority
            'priority' => ['required', 'in:low,medium,high,urgent'],

            // 8. Investigation
            'assigned_department'     => ['nullable', 'string', 'max:100'],
            'assigned_officer_name'   => ['nullable', 'string', 'max:150'],
            'investigation_date'      => ['nullable', 'date', 'before_or_equal:today'],
            'findings'                => ['nullable', 'string', 'max:4000'],
            'corrective_action_taken' => ['nullable', 'string', 'max:4000'],
            'resolution_date'         => ['nullable', 'required_if:status,resolved,closed', 'date', 'before_or_equal:today', 'after_or_equal:complaint_date'],

            // 9. Status
            'status' => ['required', 'in:registered,under_investigation,resolved,closed,pending_customer_response'],

            // 10. Feedback
            'satisfaction_level' => ['nullable', 'required_if:status,resolved,closed', 'in:very_satisfied,satisfied,neutral,dissatisfied'],
            'customer_remarks'   => ['nullable', 'string', 'max:2000'],

            // 11. Declaration
            'declaration_agreed'      => ['required', 'accepted'],
            'customer_signature_name' => ['required', 'string', 'max:150'],
            'customer_signature_date' => ['required', 'date', 'before_or_equal:today'],
            'customer_signature'      => ['required', ...$imageRules],
            'reviewed_by_name'        => ['nullable', 'string', 'max:150'],
            'reviewed_by_designation' => ['nullable', 'string', 'max:100'],
            'reviewed_by_date'        => ['nullable', 'date', 'before_or_equal:today'],
            'reviewed_by_signature'   => ['nullable', ...$imageRules],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (in_array('other', (array) $this->input('attached_evidence', []), true) && ! $this->filled('evidence_other_note')) {
                $validator->errors()->add('evidence_other_note', 'Please specify the other supporting document.');
            }

            $status = $this->input('status');
            if (in_array($status, ['resolved', 'closed'], true)) {
                if (! $this->filled('findings')) {
                    $validator->errors()->add('findings', 'Findings are required when the complaint is resolved or closed.');
                }
                if (! $this->filled('corrective_action_taken')) {
                    $validator->errors()->add('corrective_action_taken', 'Corrective action is required when the complaint is resolved or closed.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'complaint_date.required' => 'Please enter the date of complaint.',
            'complaint_date.before_or_equal' => 'Complaint date cannot be in the future.',
            'complaint_time.required' => 'Please enter the time of complaint.',
            'complaint_time.date_format' => 'Time must be in HH:MM format.',
            'received_through.required' => 'Please select how the complaint was received.',
            'received_through_other.required_if' => 'Please specify the other received-through channel.',
            'full_name.required' => 'Full name is required.',
            'full_name.regex' => 'Name may contain letters, spaces, hyphen, apostrophe and period only.',
            'client_code.exists' => 'No registered client was found with this Client ID.',
            'mobile_no.required' => 'Mobile number is required.',
            'mobile_no.digits' => 'Mobile number must be exactly 10 digits.',
            'mobile_no.regex' => 'Mobile number must start with 9 (e.g. 98XXXXXXXX).',
            'email.email' => 'Please enter a valid email address.',
            'address.required' => 'Address is required.',
            'address.min' => 'Please enter a complete address.',
            'customer_type.required' => 'Please select the customer type.',
            'customer_type_other.required_if' => 'Please specify the customer type.',
            'property_code.exists' => 'No property was found with this Property ID.',
            'kitta_no.regex' => 'Kitta number format is invalid.',
            'category.required' => 'Please select a complaint category.',
            'category_other.required_if' => 'Please specify the other category.',
            'description.required' => 'Please describe the complaint.',
            'description.min' => 'Complaint description must be at least 20 characters.',
            'priority.required' => 'Please select a priority level.',
            'status.required' => 'Please select the complaint status.',
            'satisfaction_level.required_if' => 'Please record customer satisfaction after resolution.',
            'resolution_date.required_if' => 'Resolution date is required when the complaint is resolved or closed.',
            'resolution_date.after_or_equal' => 'Resolution date cannot be before the complaint date.',
            'evidence_other_note.required_if' => 'Please specify the other supporting document.',
            'declaration_agreed.accepted' => 'You must agree to the declaration.',
            'customer_signature.required' => 'Please upload the customer scanned signature.',
            'customer_signature.mimes' => 'Signature must be JPG, PNG or WEBP.',
            'customer_signature.max' => 'Signature image must be under 2 MB.',
        ];
    }
}
