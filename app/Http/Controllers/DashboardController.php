<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        $user = $request->user();
        $counts = $user->properties()
            ->selectRaw('approval_status, COUNT(*) as aggregate')
            ->groupBy('approval_status')
            ->pluck('aggregate', 'approval_status');

        $kyc = $user->kycVerification;
        $tab = $request->query('tab', 'overview');
        if (! in_array($tab, ['overview', 'kyc', 'listings'], true)) {
            $tab = 'overview';
        }

        return Inertia::render('Dashboard', [
            'tab' => $tab,
            'kycStatus' => $kyc?->status,
            'kyc' => $kyc ? [
                'status' => $kyc->status,
                'id_type' => $kyc->id_type,
                'admin_note' => $kyc->admin_note,
                'submitted_at' => $kyc->submitted_at?->toIso8601String(),
            ] : null,
            'listingCounts' => [
                'pending' => (int) ($counts['pending'] ?? 0),
                'approved' => (int) ($counts['approved'] ?? 0),
                'rejected' => (int) ($counts['rejected'] ?? 0),
            ],
            'properties' => $user->properties()
                ->with('address:address_id,municipality,district,province')
                ->orderByDesc('property_id')
                ->get()
                ->map(fn ($property) => [
                    'property_id' => $property->property_id,
                    'property_code' => $property->property_code,
                    'property_type' => $property->property_type,
                    'area' => $property->area,
                    'municipality' => $property->address?->municipality,
                    'approval_status' => $property->approval_status,
                ])
                ->values()
                ->all(),
        ]);
    }
}
