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
            // The full Annex-F KYC form and its data now live entirely on the
            // Filament "kyc-verification-page" — this dashboard only needs
            // the status to render its summary badge and CTA.
            'kycStatus' => $kyc?->status,
            'listingCounts' => [
                'pending' => (int) ($counts['pending'] ?? 0),
                'approved' => (int) ($counts['approved'] ?? 0),
                'rejected' => (int) ($counts['rejected'] ?? 0),
            ],
            'properties' => $user->properties()
                ->with(['address:address_id,municipality,district,province', 'photos'])
                ->orderByDesc('property_id')
                ->get()
                ->map(fn ($property) => [
                    'property_id' => $property->property_id,
                    'property_code' => $property->property_code,
                    'property_type' => $property->property_type,
                    'area' => $property->area,
                    'municipality' => $property->address?->municipality,
                    'approval_status' => $property->approval_status,
                    'photos' => $property->photos->map(fn ($photo) => [
                        'photo_id' => $photo->photo_id,
                        'photo_url' => $photo->photo_url,
                        'caption' => $photo->caption,
                    ])->values()->all(),
                    'primary_photo_url' => $property->photos->first()?->photo_url,
                ])
                ->values()
                ->all(),
        ]);
    }
}
