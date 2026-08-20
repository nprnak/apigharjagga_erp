<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        foreach ([
            'ownership_role',
            'kitta_no',
            'area',
            'covered_area',
            'no_of_floors',
            'year_of_construction',
            'facing_direction',
            'structure_type',
            'parking',
            'province',
            'district',
            'municipality',
            'ward_no',
            'tole_locality',
            'expected_selling_price',
            'rental_amount',
        ] as $key) {
            if ($this->input($key) === '') {
                $this->merge([$key => null]);
            }
        }
    }
    public function rules(): array
    {
        return [
            'property_type' => ['required', 'in:land,house,apartment,commercial_building,office_space,industrial_property,agricultural_land,other'],
            'ownership_role' => ['nullable', 'in:self,family_member,authorized_representative,company'],
            'kitta_no' => ['nullable', 'string', 'max:50'],
            'area' => ['nullable', 'string', 'max:100'],
            'covered_area' => ['nullable', 'string', 'max:100'],
            'no_of_floors' => ['nullable', 'integer', 'min:0', 'max:200'],
            'year_of_construction' => ['nullable', 'integer', 'min:1800', 'max:2100'],
            'facing_direction' => ['nullable', 'string', 'max:30'],
            'structure_type' => ['nullable', 'string', 'max:50'],
            'parking' => ['nullable', 'string', 'max:50'],
            'province' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'municipality' => ['nullable', 'string', 'max:150'],
            'ward_no' => ['nullable', 'string', 'max:10'],
            'tole_locality' => ['nullable', 'string', 'max:150'],
            'purpose_of_listing' => ['required', 'in:sale,rent,lease,exchange,investment,other'],
            'expected_selling_price' => ['nullable', 'numeric', 'min:0'],
            'rental_amount' => ['nullable', 'numeric', 'min:0'],
            'photos' => ['nullable', 'array', 'max:12'],
            'photos.*' => ['file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:20480'],
        ];
    }
}
