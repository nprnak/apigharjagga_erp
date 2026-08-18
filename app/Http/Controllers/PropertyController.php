<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyStoreRequest;
use App\Models\Address;
use App\Models\Client;
use App\Models\PropertyListing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    public function store(PropertyStoreRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->kycVerification?->status !== 'approved') {
            return redirect()
                ->route('dashboard', ['tab' => 'listings'])
                ->withErrors([
                    'kyc' => 'Complete KYC verification before adding a listing.',
                ]);
        }

        $data = $request->validated();

        DB::transaction(function () use ($user, $data) {
            $address = Address::create([
                'province' => $data['province'] ?? null,
                'district' => $data['district'] ?? null,
                'municipality' => $data['municipality'] ?? null,
                'ward_no' => $data['ward_no'] ?? null,
                'tole_locality' => $data['tole_locality'] ?? null,
            ]);

            $client = Client::query()->firstOrCreate(
                ['mobile_app_user_id' => (string) $user->id],
                [
                    'client_code' => 'CLT-U' . $user->id,
                    'client_type' => 'owner',
                    'full_name' => $user->name,
                    'email' => $user->email,
                    'mobile_no' => '0000000000',
                    'current_address_id' => $address->address_id,
                    'registration_date' => now()->toDateString(),
                    'mis_entry_status' => 'pending',
                    'is_active' => true,
                ],
            );

            $property = $user->properties()->create([
                'property_code' => 'PROP-' . strtoupper(Str::random(8)),
                'owner_client_id' => $client->client_id,
                'user_id' => $user->id,
                'ownership_role' => $data['ownership_role'] ?? null,
                'property_type' => $data['property_type'],
                'address_id' => $address->address_id,
                'kitta_no' => $data['kitta_no'] ?? null,
                'area' => $data['area'] ?? null,
                'covered_area' => $data['covered_area'] ?? null,
                'no_of_floors' => $data['no_of_floors'] ?? null,
                'year_of_construction' => $data['year_of_construction'] ?? null,
                'facing_direction' => $data['facing_direction'] ?? null,
                'structure_type' => $data['structure_type'] ?? null,
                'parking' => $data['parking'] ?? null,
                'status' => 'draft',
                'approval_status' => 'pending',
            ]);

            $sequence = PropertyListing::query()->count() + 1;

            PropertyListing::query()->create([
                'application_no' => 'AGJ-' . date('Ymd') . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
                'property_id' => $property->property_id,
                'applicant_client_id' => $client->client_id,
                'purpose_of_listing' => $data['purpose_of_listing'],
                'expected_selling_price' => $data['expected_selling_price'] ?? null,
                'rental_amount' => $data['rental_amount'] ?? null,
                'listing_status' => 'approved',
            ]);
        });

        return redirect()->route('dashboard', ['tab' => 'listings']);
    }
}
