<?php

namespace App\Http\Controllers;

use App\Models\KycVerification;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminApprovalController extends Controller
{
    public function approve(Request $request): RedirectResponse
    {
        $this->apply($request, 'approved');

        return back();
    }

    public function reject(Request $request): RedirectResponse
    {
        $this->apply($request, 'rejected');

        return back();
    }

    private function apply(Request $request, string $status): void
    {
        $data = $request->validate([
            'type' => ['required', 'in:kyc,listing'],
            'id' => ['required', 'integer'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($data['type'] === 'kyc') {
            $kyc = KycVerification::query()->findOrFail($data['id']);
            $kyc->update([
                'status' => $status,
                'admin_note' => $status === 'rejected' ? ($data['admin_note'] ?? $kyc->admin_note) : null,
                'reviewed_at' => now(),
            ]);

            return;
        }

        $property = Property::query()->findOrFail($data['id']);
        $property->update([
            'approval_status' => $status,
        ]);
    }
}
