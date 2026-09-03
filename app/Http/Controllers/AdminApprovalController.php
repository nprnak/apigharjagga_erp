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
        return $this->apply($request, 'approved');
    }

    public function reject(Request $request): RedirectResponse
    {
        return $this->apply($request, 'rejected');
    }

    private function apply(Request $request, string $status): RedirectResponse
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

            return back();
        }

        $property = Property::query()->findOrFail($data['id']);

        if ($status === 'approved' && ! $property->hasCompletedSiteInspection()) {
            return back()->with('error', 'This property cannot be approved until its site inspection has been completed and reviewed.');
        }

        $property->update([
            'approval_status' => $status,
        ]);

        return back();
    }
}
