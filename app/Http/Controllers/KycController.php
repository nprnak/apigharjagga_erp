<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KycController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Identity document
            'id_type'       => ['required', Rule::in(['citizenship', 'national_id', 'passport', 'driving_license'])],
            'id_document'   => ['required', 'file', 'mimes:jpeg,png,webp', 'max:4096'],
            'selfie_photo'  => ['nullable', 'file', 'mimes:jpeg,png,webp', 'max:4096'],

            // Personal details (Annex F)
            'full_name'          => ['required', 'string', 'max:150'],
            'father_mother_name' => ['nullable', 'string', 'max:150'],
            'spouse_name'        => ['nullable', 'string', 'max:150'],
            'citizenship_no'     => ['nullable', 'string', 'max:50'],
            'date_of_birth'      => ['nullable', 'date', 'before:today'],
            'gender'             => ['nullable', Rule::in(['male', 'female', 'other'])],
            'nationality'        => ['nullable', 'string', 'max:50'],
            'occupation'         => ['nullable', 'string', 'max:100'],
            'mobile_no'          => ['nullable', 'string', 'max:20'],
            'email'              => ['nullable', 'email', 'max:150'],

            // Permanent address
            'permanent_province'     => ['nullable', 'string', 'max:100'],
            'permanent_district'     => ['nullable', 'string', 'max:100'],
            'permanent_municipality' => ['nullable', 'string', 'max:100'],
            'permanent_ward_no'      => ['nullable', 'string', 'max:10'],
            'permanent_tole'         => ['nullable', 'string', 'max:100'],

            // Current address
            'current_province'     => ['nullable', 'string', 'max:100'],
            'current_district'     => ['nullable', 'string', 'max:100'],
            'current_municipality' => ['nullable', 'string', 'max:100'],
            'current_ward_no'      => ['nullable', 'string', 'max:10'],
            'current_tole'         => ['nullable', 'string', 'max:100'],
        ]);

        $user    = $request->user();
        $existing = $user->kycVerification;

        // Store the ID document
        $docPath = $request->file('id_document')->store('kyc/documents', 'local');

        // Optionally store selfie
        $selfiePath = null;
        if ($request->hasFile('selfie_photo')) {
            $selfiePath = $request->file('selfie_photo')->store('kyc/selfies', 'local');
        }

        $payload = [
            'id_type'            => $validated['id_type'],
            'id_document_path'   => $docPath,
            'selfie_photo_path'  => $selfiePath,
            'full_name'          => $validated['full_name'],
            'father_mother_name' => $validated['father_mother_name'] ?? null,
            'spouse_name'        => $validated['spouse_name'] ?? null,
            'citizenship_no'     => $validated['citizenship_no'] ?? null,
            'date_of_birth'      => $validated['date_of_birth'] ?? null,
            'gender'             => $validated['gender'] ?? null,
            'nationality'        => $validated['nationality'] ?? 'Nepali',
            'occupation'         => $validated['occupation'] ?? null,
            'mobile_no'          => $validated['mobile_no'] ?? null,
            'email'              => $validated['email'] ?? null,
            'permanent_province'     => $validated['permanent_province'] ?? null,
            'permanent_district'     => $validated['permanent_district'] ?? null,
            'permanent_municipality' => $validated['permanent_municipality'] ?? null,
            'permanent_ward_no'      => $validated['permanent_ward_no'] ?? null,
            'permanent_tole'         => $validated['permanent_tole'] ?? null,
            'current_province'       => $validated['current_province'] ?? null,
            'current_district'       => $validated['current_district'] ?? null,
            'current_municipality'   => $validated['current_municipality'] ?? null,
            'current_ward_no'        => $validated['current_ward_no'] ?? null,
            'current_tole'           => $validated['current_tole'] ?? null,
            'status'       => 'pending',
            'admin_note'   => null,
            'submitted_at' => now(),
            'reviewed_at'  => null,
        ];

        if ($existing) {
            // Delete old files
            if ($existing->id_document_path) {
                Storage::disk('local')->delete($existing->id_document_path);
            }
            if ($existing->selfie_photo_path) {
                Storage::disk('local')->delete($existing->selfie_photo_path);
            }
            $existing->update($payload);
        } else {
            $user->kycVerification()->create($payload);
        }

        return redirect()->route('dashboard', ['tab' => 'kyc']);
    }
}
