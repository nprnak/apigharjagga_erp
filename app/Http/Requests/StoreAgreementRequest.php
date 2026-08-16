<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAgreementRequest extends FormRequest
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
            'seller_contact_no' => $digits($this->input('seller_contact_no')),
            'buyer_contact_no'  => $digits($this->input('buyer_contact_no')),
            'ward_no'           => $digits($this->input('ward_no')),
        ]);

        foreach (['seller_declaration_agreed', 'buyer_declaration_agreed'] as $field) {
            $value = $this->input($field);
            if (in_array($value, [true, 1, '1', 'true', 'on', 'yes'], true)) {
                $this->merge([$field => '1']);
            }
        }
    }

    public function rules(): array
    {
        $nameRule = ['regex:/^[\p{L}][\p{L}\s.\-\']*$/u'];
        $citizenshipRule = ['regex:/^[A-Za-z0-9][A-Za-z0-9\-\/]*$/'];
        $locationRule = ['regex:/^[\p{L}][\p{L}\s.\-]*$/u'];
        $imageRules = ['image', 'mimes:jpeg,jpg,png,webp', 'max:2048'];

        return [
            // ── First Party (Seller) ──────────────────────────────────────
            'seller_full_name'          => ['required', 'string', 'min:2', 'max:150', ...$nameRule],
            'seller_father_mother_name' => ['nullable', 'string', 'min:2', 'max:150', ...$nameRule],
            'seller_citizenship_no'     => ['required', 'string', 'max:50', ...$citizenshipRule],
            'seller_permanent_address'  => ['required', 'string', 'min:8', 'max:500'],
            'seller_contact_no'         => ['required', 'digits:10', 'regex:/^9[0-9]{9}$/'],

            // ── Second Party (Buyer) ──────────────────────────────────────
            'buyer_full_name'          => ['required', 'string', 'min:2', 'max:150', ...$nameRule],
            'buyer_father_mother_name' => ['nullable', 'string', 'min:2', 'max:150', ...$nameRule],
            'buyer_citizenship_no'     => ['required', 'string', 'max:50', ...$citizenshipRule, 'different:seller_citizenship_no'],
            'buyer_permanent_address'  => ['required', 'string', 'min:8', 'max:500'],
            'buyer_contact_no'         => ['required', 'digits:10', 'regex:/^9[0-9]{9}$/'],

            // ── Property Details ───────────────────────────────────────────
            'property_type'     => ['required', 'in:land,house'],
            'district'          => ['required', 'string', 'min:2', 'max:100', ...$locationRule],
            'municipality'      => ['required', 'string', 'min:2', 'max:150', ...$locationRule],
            'ward_no'           => ['required', 'integer', 'min:1', 'max:33'],
            'kitta_no'          => ['required', 'string', 'min:1', 'max:50', 'regex:/^[A-Za-z0-9\-\/]+$/'],
            'area'              => ['required', 'string', 'min:1', 'max:100'],
            'house_description' => ['nullable', 'string', 'max:500'],
            'boundary_east'     => ['nullable', 'required_if:property_type,land', 'string', 'min:2', 'max:150'],
            'boundary_west'     => ['nullable', 'required_if:property_type,land', 'string', 'min:2', 'max:150'],
            'boundary_north'    => ['nullable', 'required_if:property_type,land', 'string', 'min:2', 'max:150'],
            'boundary_south'    => ['nullable', 'required_if:property_type,land', 'string', 'min:2', 'max:150'],

            // ── Purchase Price ─────────────────────────────────────────────
            'total_price'       => ['required', 'numeric', 'min:1', 'max:999999999999.99'],
            'total_price_words' => ['required', 'string', 'min:5', 'max:255', 'regex:/^[\p{L}][\p{L}\s,\-]*$/u'],

            // ── Payment Terms ──────────────────────────────────────────────
            'advance_payment'    => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'balance_payment'    => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'final_payment_date' => ['nullable', 'date', 'after_or_equal:agreement_date'],

            // ── Declarations ───────────────────────────────────────────────
            'seller_declaration_agreed' => ['required', 'accepted'],
            'buyer_declaration_agreed'  => ['required', 'accepted'],

            // ── Scanned signatures ─────────────────────────────────────────
            'seller_signature'      => ['required', ...$imageRules],
            'buyer_signature'       => ['required', ...$imageRules],
            'seller_signature_date' => ['required', 'date', 'before_or_equal:today'],
            'buyer_signature_date'  => ['required', 'date', 'before_or_equal:today'],

            // ── Witnesses ──────────────────────────────────────────────────
            'witness1_name'            => ['nullable', 'required_with:witness1_citizenship_no,witness1_signature', 'string', 'min:2', 'max:150', ...$nameRule],
            'witness1_citizenship_no'  => ['nullable', 'required_with:witness1_name', 'string', 'max:50', ...$citizenshipRule],
            'witness1_signature'       => ['nullable', 'required_with:witness1_name', ...$imageRules],
            'witness2_name'            => ['nullable', 'required_with:witness2_citizenship_no,witness2_signature', 'string', 'min:2', 'max:150', ...$nameRule],
            'witness2_citizenship_no'  => ['nullable', 'required_with:witness2_name', 'string', 'max:50', ...$citizenshipRule],
            'witness2_signature'       => ['nullable', 'required_with:witness2_name', ...$imageRules],

            // ── Final provisions ───────────────────────────────────────────
            'place'          => ['required', 'string', 'min:2', 'max:150', ...$locationRule],
            'agreement_date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $total = $this->input('total_price');
            $advance = $this->input('advance_payment');
            $balance = $this->input('balance_payment');

            if ($total === null || $total === '') {
                return;
            }

            $total = (float) $total;

            if ($advance !== null && $advance !== '' && (float) $advance > $total) {
                $validator->errors()->add(
                    'advance_payment',
                    'Advance payment cannot be greater than the total purchase price.'
                );
            }

            if ($balance !== null && $balance !== '' && (float) $balance > $total) {
                $validator->errors()->add(
                    'balance_payment',
                    'Balance payment cannot be greater than the total purchase price.'
                );
            }

            if (
                $advance !== null && $advance !== ''
                && $balance !== null && $balance !== ''
                && abs(((float) $advance + (float) $balance) - $total) > 0.01
            ) {
                $validator->errors()->add(
                    'balance_payment',
                    'Advance plus balance must equal the total purchase price.'
                );
            }

            $sellerName = mb_strtolower(trim((string) $this->input('seller_full_name')));
            $buyerName = mb_strtolower(trim((string) $this->input('buyer_full_name')));
            $sellerCitizen = mb_strtolower(trim((string) $this->input('seller_citizenship_no')));
            $buyerCitizen = mb_strtolower(trim((string) $this->input('buyer_citizenship_no')));

            if ($sellerCitizen !== '' && $sellerCitizen === $buyerCitizen) {
                $validator->errors()->add(
                    'buyer_citizenship_no',
                    'Buyer citizenship number must be different from the seller.'
                );
            }

            if ($sellerName !== '' && $sellerName === $buyerName && $sellerCitizen === $buyerCitizen) {
                $validator->errors()->add(
                    'buyer_full_name',
                    'Seller and buyer cannot be the same person.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'seller_full_name.regex' => "Seller's name may contain letters, spaces, hyphen, apostrophe and period only.",
            'buyer_full_name.regex'  => "Buyer's name may contain letters, spaces, hyphen, apostrophe and period only.",
            'seller_father_mother_name.regex' => "Father's/Mother's name format is invalid.",
            'buyer_father_mother_name.regex'  => "Father's/Mother's name format is invalid.",

            'seller_citizenship_no.regex' => "Seller's citizenship number format is invalid.",
            'buyer_citizenship_no.regex'  => "Buyer's citizenship number format is invalid.",
            'buyer_citizenship_no.different' => 'Buyer citizenship number must be different from the seller.',

            'seller_contact_no.digits' => 'Mobile number must be exactly 10 digits.',
            'seller_contact_no.regex'  => "Seller's mobile number must start with 9 (e.g. 98XXXXXXXX).",
            'buyer_contact_no.digits'  => 'Mobile number must be exactly 10 digits.',
            'buyer_contact_no.regex'   => "Buyer's mobile number must start with 9 (e.g. 98XXXXXXXX).",

            'district.regex'     => 'District may contain letters and spaces only.',
            'municipality.regex' => 'Municipality may contain letters and spaces only.',
            'place.regex'        => 'Place may contain letters and spaces only.',
            'ward_no.min'        => 'Ward number must be between 1 and 33.',
            'ward_no.max'        => 'Ward number must be between 1 and 33.',
            'kitta_no.regex'     => 'Kitta number may contain letters, digits, hyphen and slash only.',

            'boundary_east.required_if'  => 'Please specify the eastern boundary for land.',
            'boundary_west.required_if'  => 'Please specify the western boundary for land.',
            'boundary_north.required_if' => 'Please specify the northern boundary for land.',
            'boundary_south.required_if' => 'Please specify the southern boundary for land.',

            'total_price.min'            => 'Total purchase price must be greater than zero.',
            'total_price_words.regex'    => 'Amount in words may contain letters, spaces, commas and hyphens only.',
            'advance_payment.min'        => 'Advance payment cannot be negative.',
            'balance_payment.min'        => 'Balance payment cannot be negative.',
            'final_payment_date.after_or_equal' => 'Final payment date cannot be before the agreement date.',

            'seller_declaration_agreed.accepted' => 'The Seller must agree to the declaration.',
            'buyer_declaration_agreed.accepted'  => 'The Buyer must agree to the declaration.',

            'seller_signature.required' => "Please upload the seller's scanned signature.",
            'seller_signature.image'    => "Seller's signature must be an image file.",
            'seller_signature.mimes'    => "Seller's signature must be JPG, PNG or WEBP.",
            'seller_signature.max'      => "Seller's signature must be under 2 MB.",
            'buyer_signature.required'  => "Please upload the buyer's scanned signature.",
            'buyer_signature.image'     => "Buyer's signature must be an image file.",
            'buyer_signature.mimes'     => "Buyer's signature must be JPG, PNG or WEBP.",
            'buyer_signature.max'       => "Buyer's signature must be under 2 MB.",

            'seller_signature_date.before_or_equal' => "Seller's signature date cannot be in the future.",
            'buyer_signature_date.before_or_equal'  => "Buyer's signature date cannot be in the future.",
            'agreement_date.before_or_equal'        => 'Agreement date cannot be in the future.',

            'witness1_name.required_with'           => "Witness 1 name is required when other witness 1 details are provided.",
            'witness1_citizenship_no.required_with' => "Witness 1 citizenship number is required when a name is provided.",
            'witness1_signature.required_with'      => "Please upload Witness 1's scanned signature.",
            'witness2_name.required_with'           => "Witness 2 name is required when other witness 2 details are provided.",
            'witness2_citizenship_no.required_with' => "Witness 2 citizenship number is required when a name is provided.",
            'witness2_signature.required_with'      => "Please upload Witness 2's scanned signature.",
        ];
    }
}
