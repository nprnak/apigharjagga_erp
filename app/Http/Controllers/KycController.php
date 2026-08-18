<?php

namespace App\Http\Controllers;

use App\Http\Requests\KycStoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    public function store(KycStoreRequest $request): RedirectResponse
    {
        $user = $request->user();
        $existing = $user->kycVerification;
        $path = $request->file('id_document')->store('kyc', 'local');

        if ($existing) {
            if ($existing->id_document_path) {
                Storage::disk('local')->delete($existing->id_document_path);
            }

            $existing->update([
                'id_document_path' => $path,
                'id_type' => $request->validated('id_type'),
                'status' => 'pending',
                'admin_note' => null,
                'submitted_at' => now(),
                'reviewed_at' => null,
            ]);
        } else {
            $user->kycVerification()->create([
                'id_document_path' => $path,
                'id_type' => $request->validated('id_type'),
                'status' => 'pending',
                'submitted_at' => now(),
            ]);
        }

        return redirect()->route('dashboard', ['tab' => 'kyc']);
    }
}
