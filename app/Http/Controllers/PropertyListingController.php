<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyListingRequest;
use App\Models\Address;
use App\Models\Client;
use App\Models\Property;
use App\Models\PropertyListing;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PropertyListingController extends Controller
{
    /**
     * Show the property listing application form.
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('PropertyListing/PropertyListingForm');
    }

    /**
     * Store a new property listing application.
     * Creates: addresses, clients, properties, property_listings — all in one transaction.
     */
    public function store(StorePropertyListingRequest $request): JsonResponse
    {
        $data = $request->validated();

        $listing = DB::transaction(function () use ($data, $request) {

            // 1. Create permanent address
            $permanentAddress = Address::create([
                'full_address_text' => $data['permanent_address'] ?? null,
            ]);

            // 2. Create current address (if different)
            $currentAddress = Address::create([
                'province'          => $data['province'],
                'district'          => $data['district'],
                'municipality'      => $data['municipality'],
                'ward_no'           => $data['ward_no'],
                'tole_locality'     => $data['tole'] ?? null,
                'full_address_text' => $data['current_address'] ?? null,
                'gps_lat'           => $this->parseGpsLat($data['gps_location'] ?? null),
                'gps_lng'           => $this->parseGpsLng($data['gps_location'] ?? null),
            ]);

            // 3. Create client (applicant)
            $client = Client::create([
                'client_code'        => 'CLT-' . strtoupper(Str::random(8)),
                'client_type'        => 'owner',
                'full_name'          => $data['full_name_en'],
                'grandfather_name'   => $data['grandfather_name'] ?? null,
                'father_mother_name' => $data['father_name'] ?? null,
                'citizenship_no'     => $data['citizenship_no'],
                'date_of_birth'      => $data['date_of_birth'] ?? null,
                'occupation'         => $data['occupation'] ?? null,
                'mobile_no'          => $data['mobile_no'],
                'telephone_no'       => $data['telephone_no'] ?? null,
                'email'              => $data['email'] ?? null,
                'permanent_address_id' => $permanentAddress->address_id,
                'current_address_id'   => $currentAddress->address_id,
                'registration_date'    => now()->toDateString(),
                'mis_entry_status'     => 'pending',
                'is_active'            => true,
            ]);

            // 4. Create property
            $property = Property::create([
                'property_code'       => 'PROP-' . strtoupper(Str::random(8)),
                'owner_client_id'     => $client->client_id,
                'ownership_role'      => $data['ownership_role'],
                'property_type'       => $data['property_type'],
                'address_id'          => $currentAddress->address_id,
                'kitta_no'            => $data['kitta_no'] ?? null,
                'area'                => $data['area'] ?? null,
                'map_sheet_no'        => $data['map_sheet_no'] ?? null,
                'ownership_type'      => $data['ownership_type'] ?? null,
                'road_access'         => $data['road_access'] ?? null,
                'road_width'          => $data['road_width'] ?? null,
                'facing_direction'    => $data['facing_direction'] ?? null,
                'year_of_construction'=> $data['year_of_construction'] ?? null,
                'no_of_floors'        => $data['no_of_floors'] ?? null,
                'covered_area'        => $data['covered_area'] ?? null,
                'structure_type'      => $data['structure_type'] ?? null,
                'roof_type'           => $data['roof_type'] ?? null,
                'parking'             => $data['parking'] ?? null,
                'water_supply'        => $data['water_supply'] ?? null,
                'electricity'         => $data['electricity'] ?? null,
                'internet'            => $data['internet'] ?? null,
                'drainage'            => $data['drainage'] ?? null,
                'status'              => 'draft',
            ]);

            // 5. Generate unique application number
            $applicationNo = 'AGJ-' . date('Ymd') . '-' . str_pad(
                PropertyListing::count() + 1,
                4, '0', STR_PAD_LEFT
            );

            $signaturePath = $request->file('applicant_signature')
                ? $request->file('applicant_signature')->store('signatures/listings', 'public')
                : null;

            // 6. Create property listing
            $listing = PropertyListing::create([
                'application_no'           => $applicationNo,
                'property_id'              => $property->property_id,
                'applicant_client_id'      => $client->client_id,
                'purpose_of_listing'       => $data['purpose_of_listing'],
                'expected_selling_price'   => isset($data['expected_selling_price']) && $data['expected_selling_price'] !== '' ? $data['expected_selling_price'] : null,
                'negotiable'               => ($data['negotiable'] ?? 'no') === 'yes',
                'minimum_acceptable_price' => isset($data['minimum_acceptable_price']) && $data['minimum_acceptable_price'] !== '' ? $data['minimum_acceptable_price'] : null,
                'rental_amount'            => isset($data['rental_amount']) && $data['rental_amount'] !== '' ? $data['rental_amount'] : null,
                'date_received'            => now()->toDateString(),
                'listing_status'           => 'pending',
                'legal_verification_status'=> 'pending',
                'inspection_required'      => true,
                'valuation_required'       => false,
                'photographs_received'     => false,
                'gis_location_verified'    => false,
                'remarks'                  => null,
                'applicant_signature_path' => $signaturePath,
            ]);

            return $listing;
        });

        return response()->json([
            'success'        => true,
            'listing_id'     => $listing->listing_id,
            'application_no' => $listing->application_no,
            'message'        => 'Application submitted successfully!',
        ], 201);
    }

    /**
     * Download a PDF of the submitted property listing application.
     */
    public function downloadPdf(int $id): Response
    {
        $listing = PropertyListing::with([
            'property.address',
            'applicant.permanentAddress',
            'applicant.currentAddress',
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.property_listing', compact('listing'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("property-listing-{$listing->application_no}.pdf");
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function parseGpsLat(?string $gps): ?float
    {
        if (! $gps) {
            return null;
        }
        $parts = explode(',', $gps);
        return isset($parts[0]) ? (float) trim($parts[0]) : null;
    }

    private function parseGpsLng(?string $gps): ?float
    {
        if (! $gps) {
            return null;
        }
        $parts = explode(',', $gps);
        return isset($parts[1]) ? (float) trim($parts[1]) : null;
    }
}
