<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ValuationRequestController extends Controller
{
    /**
     * Display Annex-C form.
     */
    public function create()
    {
        return Inertia::render('AnnexC');
    }

    /**
     * Save Annex-C valuation request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Applicant
            'full_name' => ['required', 'string', 'max:150'],
            'father_mother_name' => ['nullable', 'string', 'max:150'],
            'citizenship_no' => ['required', 'string', 'max:50'],
            'mobile_no' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],

            // Permanent address
            'permanent_province' => ['nullable', 'string', 'max:100'],
            'permanent_district' => ['nullable', 'string', 'max:100'],
            'permanent_municipality' => ['nullable', 'string', 'max:150'],
            'permanent_ward_no' => ['nullable', 'string', 'max:10'],
            'permanent_tole' => ['nullable', 'string', 'max:150'],
            'permanent_full_address' => ['nullable', 'string'],

            // Current address
            'current_province' => ['nullable', 'string', 'max:100'],
            'current_district' => ['nullable', 'string', 'max:100'],
            'current_municipality' => ['nullable', 'string', 'max:150'],
            'current_ward_no' => ['nullable', 'string', 'max:10'],
            'current_tole' => ['nullable', 'string', 'max:150'],
            'current_full_address' => ['nullable', 'string'],

            // Property
            'property_type' => [
                'required',
                'in:land,house,apartment,commercial_building,office_space,industrial_property,agricultural_land,other'
            ],

            'province' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'municipality' => ['required', 'string', 'max:150'],
            'ward_no' => ['required', 'string', 'max:10'],
            'tole_locality' => ['nullable', 'string', 'max:150'],
            'kitta_no' => ['nullable', 'string', 'max:50'],
            'area' => ['nullable', 'string', 'max:100'],
            'map_sheet_no' => ['nullable', 'string', 'max:50'],
            'ownership_type' => ['nullable', 'string', 'max:50'],
            'ownership_certificate_no' => ['nullable', 'string', 'max:50'],

            // Building
            'year_of_construction' => ['nullable', 'integer'],
            'covered_area' => ['nullable', 'string', 'max:100'],
            'no_of_floors' => ['nullable', 'integer'],
            'structure_type' => ['nullable', 'string', 'max:50'],
            'building_permit_no' => ['nullable', 'string', 'max:50'],
            'current_building_condition' => [
                'nullable',
                'in:excellent,good,fair,poor'
            ],
            'road_access' => ['nullable', 'string', 'max:20'],
            'road_width' => ['nullable', 'string', 'max:50'],
            'facing_direction' => ['nullable', 'string', 'max:30'],

            // Valuation
            'purpose_of_valuation' => [
                'required',
                'in:bank_loan_mortgage,buying_selling,insurance,legal,investment_decision,other'
            ],

            'requested_valuation_type' => [
                'required',
                'in:market_value,forced_sale_value,government_value_reference,rental_value'
            ],

            'preferred_visit_date' => ['nullable', 'date'],
            'preferred_visit_time' => ['nullable', 'string', 'max:20'],
            'site_contact_person_name' => ['nullable', 'string', 'max:150'],
            'site_contact_mobile' => ['nullable', 'string', 'max:20'],

            'remarks' => ['nullable', 'string'],

            // Documents
            'documents' => ['nullable', 'array'],

            // Declaration
            'declaration_agreed' => ['accepted'],
            'signature_name' => ['nullable', 'string', 'max:150'],
            'signature_date' => ['nullable', 'date'],
        ]);

        $result = DB::transaction(function () use ($validated) {

            /*
             * ----------------------------------------------------
             * 1. PERMANENT ADDRESS
             * ----------------------------------------------------
             */

            $permanentAddressId = DB::table('addresses')->insertGetId([
                'province' => $validated['permanent_province'] ?? null,
                'district' => $validated['permanent_district'] ?? null,
                'municipality' => $validated['permanent_municipality'] ?? null,
                'ward_no' => $validated['permanent_ward_no'] ?? null,
                'tole_locality' => $validated['permanent_tole'] ?? null,
                'full_address_text' => $validated['permanent_full_address'] ?? null,
            ]);


            /*
             * ----------------------------------------------------
             * 2. CURRENT ADDRESS
             * ----------------------------------------------------
             */

            $currentAddressId = DB::table('addresses')->insertGetId([
                'province' => $validated['current_province'] ?? null,
                'district' => $validated['current_district'] ?? null,
                'municipality' => $validated['current_municipality'] ?? null,
                'ward_no' => $validated['current_ward_no'] ?? null,
                'tole_locality' => $validated['current_tole'] ?? null,
                'full_address_text' => $validated['current_full_address'] ?? null,
            ]);


            /*
             * ----------------------------------------------------
             * 3. CLIENT
             * ----------------------------------------------------
             */

            $client = DB::table('clients')
                ->where('citizenship_no', $validated['citizenship_no'])
                ->first();

            if ($client) {

                DB::table('clients')
                    ->where('client_id', $client->client_id)
                    ->update([
                        'full_name' => $validated['full_name'],
                        'father_mother_name' => $validated['father_mother_name'] ?? null,
                        'mobile_no' => $validated['mobile_no'],
                        'email' => $validated['email'] ?? null,
                        'permanent_address_id' => $permanentAddressId,
                        'current_address_id' => $currentAddressId,
                        'updated_at' => now(),
                    ]);

                $clientId = $client->client_id;

            } else {

                $clientId = DB::table('clients')->insertGetId([
                    'client_code' => 'CL-' . strtoupper(Str::random(8)),
                    'client_type' => 'owner',
                    'full_name' => $validated['full_name'],
                    'father_mother_name' => $validated['father_mother_name'] ?? null,
                    'citizenship_no' => $validated['citizenship_no'],
                    'nationality' => 'Nepali',
                    'mobile_no' => $validated['mobile_no'],
                    'email' => $validated['email'] ?? null,
                    'permanent_address_id' => $permanentAddressId,
                    'current_address_id' => $currentAddressId,
                    'registration_date' => now()->toDateString(),
                    'mis_entry_status' => 'pending',
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }


            /*
             * ----------------------------------------------------
             * 4. PROPERTY ADDRESS
             * ----------------------------------------------------
             */

            $propertyAddressId = DB::table('addresses')->insertGetId([
                'province' => $validated['province'],
                'district' => $validated['district'],
                'municipality' => $validated['municipality'],
                'ward_no' => $validated['ward_no'],
                'tole_locality' => $validated['tole_locality'] ?? null,
            ]);


            /*
             * ----------------------------------------------------
             * 5. PROPERTY
             * ----------------------------------------------------
             */

            $propertyId = DB::table('properties')->insertGetId([
                'property_code' => 'PROP-' . strtoupper(Str::random(8)),
                'owner_client_id' => $clientId,
                'ownership_role' => 'self',
                'property_type' => $validated['property_type'],
                'address_id' => $propertyAddressId,
                'kitta_no' => $validated['kitta_no'] ?? null,
                'area' => $validated['area'] ?? null,
                'map_sheet_no' => $validated['map_sheet_no'] ?? null,
                'ownership_type' => $validated['ownership_type'] ?? null,
                'ownership_certificate_no' =>
                    $validated['ownership_certificate_no'] ?? null,

                'road_access' => $validated['road_access'] ?? null,
                'road_width' => $validated['road_width'] ?? null,
                'facing_direction' => $validated['facing_direction'] ?? null,

                'year_of_construction' =>
                    $validated['year_of_construction'] ?? null,

                'no_of_floors' =>
                    $validated['no_of_floors'] ?? null,

                'covered_area' =>
                    $validated['covered_area'] ?? null,

                'structure_type' =>
                    $validated['structure_type'] ?? null,

                'building_permit_no' =>
                    $validated['building_permit_no'] ?? null,

                'current_building_condition' =>
                    $validated['current_building_condition'] ?? null,

                'status' => 'under_valuation',

                'created_at' => now(),
                'updated_at' => now(),
            ]);


            /*
             * ----------------------------------------------------
             * 6. VALUATION REQUEST
             * ----------------------------------------------------
             */

            $requestCode = 'VAL-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(Str::random(6));

            $requestId = DB::table('valuation_requests')->insertGetId([
                'request_code' => $requestCode,

                'client_id' => $clientId,

                'property_id' => $propertyId,

                'purpose_of_valuation' =>
                    $validated['purpose_of_valuation'],

                'requested_valuation_type' =>
                    $validated['requested_valuation_type'],

                'preferred_visit_date' =>
                    $validated['preferred_visit_date'] ?? null,

                'preferred_visit_time' =>
                    $validated['preferred_visit_time'] ?? null,

                'site_contact_person_name' =>
                    $validated['site_contact_person_name'] ?? null,

                'site_contact_mobile' =>
                    $validated['site_contact_mobile'] ?? null,

                'application_received_date' =>
                    now()->toDateString(),

                'status' => 'received',

                'remarks' =>
                    $validated['remarks'] ?? null,

                'created_at' => now(),
            ]);


            /*
             * ----------------------------------------------------
             * 7. DOCUMENT CHECKLIST
             * ----------------------------------------------------
             */

            $documentMap = [
                'land_ownership_certificate'
                    => 'Land Ownership Certificate',

                'citizenship_certificate'
                    => 'Citizenship Copy',

                'land_revenue_receipt'
                    => 'Land Revenue Receipt',

                'land_map_trace_map'
                    => 'Blueprint',

                'building_approval_certificate'
                    => 'Building Approval Certificate',

                'tax_clearance_certificate'
                    => 'Tax Clearance',

                'other_documents'
                    => 'Other',
            ];

            $documents = $validated['documents'] ?? [];

            foreach ($documentMap as $key => $documentName) {

                $documentType = DB::table('document_types')
                    ->where('doc_name', $documentName)
                    ->first();

                if ($documentType) {

                    DB::table('valuation_request_documents')->insert([
                        'request_id' => $requestId,
                        'doc_type_id' => $documentType->doc_type_id,
                        'is_available' =>
                            !empty($documents[$key]) ? 1 : 0,
                    ]);
                }
            }


            return [
                'request_id' => $requestId,
                'request_code' => $requestCode,
                'client_id' => $clientId,
                'property_id' => $propertyId,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'मूल्याङ्कन अनुरोध सफलतापूर्वक सुरक्षित भयो।',
            'data' => $result,
        ], 201);
    }
}