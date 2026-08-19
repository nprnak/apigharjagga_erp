<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KycStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $status = $user->kycVerification?->status;

        return ! in_array($status, ['pending', 'approved'], true);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id_type' => ['required', 'string', 'in:citizenship,national_id,passport,driving_license'],
            'id_document' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'selfie_photo' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ];
    }
}
