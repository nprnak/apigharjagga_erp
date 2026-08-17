<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRegistrationRequest;
use App\Models\Address;
use App\Models\Client;
use App\Models\ClientDocument;
use App\Models\ClientOrganization;
use App\Models\ClientOwnerListing;
use App\Models\ClientPropertyRequirement;
use App\Models\ClientServiceRequest;
use App\Models\DocumentType;
use App\Models\ServiceType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ClientRegistrationController extends Controller
{
    private const SERVICE_MAP = [
        'listing'            => 'Property Listing Service',
        'verification'       => 'Property Verification Service',
        'valuation'          => 'Property Valuation Service',
        'digital_marketing'  => 'Digital Marketing Service',
        'consultation'       => 'Property Consultation',
        'documentation'      => 'Documentation Support',
    ];

    private const DOCUMENT_MAP = [
        'citizenship_copy'       => ['Citizenship Copy', 'identity'],
        'ownership_certificate'  => ['Ownership Certificate Copy', 'land'],
        'land_house_documents'   => ['Land/House Documents', 'land'],
        'passport_photo'         => ['Passport Size Photo', 'identity'],
        'authorization_letter'   => ['Authorization Letter', 'identity'],
        'other_documents'        => ['Other Documents', 'other'],
    ];

    public function index(): InertiaResponse
    {
        return Inertia::render('ClientRegistration/ClientRegistrationForm');
    }

    public function store(StoreClientRegistrationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $client = DB::transaction(function () use ($data, $request) {
            $permanent = Address::create([
                'full_address_text' => $data['permanent_address'],
            ]);

            $current = Address::create([
                'full_address_text' => $data['current_address'] ?? $data['permanent_address'],
            ]);

            $signaturePath = $this->storeSignature($request->file('client_signature'), 'signatures/clients');
            $registeredBySig = $this->storeSignature($request->file('registered_by_signature'), 'signatures/staff');
            $approvedBySig = $this->storeSignature($request->file('approved_by_signature'), 'signatures/staff');

            $client = Client::create([
                'client_code'                    => $this->nextClientCode(),
                'client_type'                    => $data['client_type'],
                'client_type_other'              => $data['client_type_other'] ?? null,
                'full_name'                      => $data['full_name'],
                'father_mother_name'             => $data['father_mother_name'] ?? null,
                'spouse_name'                    => $data['spouse_name'] ?? null,
                'citizenship_no'                 => $data['citizenship_no'] ?? null,
                'nationality'                    => $data['nationality'] ?? 'Nepali',
                'date_of_birth'                  => $data['date_of_birth'] ?? null,
                'gender'                         => $data['gender'] ?? null,
                'occupation'                     => $data['occupation'] ?? null,
                'mobile_no'                      => $data['mobile_no'],
                'alt_contact_no'                 => $data['alt_contact_no'] ?? null,
                'email'                          => $data['email'] ?? null,
                'permanent_address_id'           => $permanent->address_id,
                'current_address_id'             => $current->address_id,
                'mobile_app_user_id'             => $data['mobile_app_user_id'] ?? null,
                'registration_date'              => $data['registration_date'],
                'mis_entry_status'               => $data['mis_entry_status'] ?? 'pending',
                'is_active'                      => true,
                'signature_name'                 => $data['client_signature_name'] ?? $data['full_name'],
                'signature_path'                 => $signaturePath,
                'signature_date'                 => $data['client_signature_date'] ?? null,
                'registered_by_name'             => $data['registered_by_name'] ?? null,
                'registered_by_designation'      => $data['registered_by_designation'] ?? null,
                'registered_by_signature_path'   => $registeredBySig,
                'registered_by_date'             => $data['registered_by_date'] ?? null,
                'approved_by_name'               => $data['approved_by_name'] ?? null,
                'approved_by_designation'        => $data['approved_by_designation'] ?? null,
                'approved_by_signature_path'     => $approvedBySig,
                'approved_by_date'               => $data['approved_by_date'] ?? null,
            ]);

            if (! empty($data['organization_name'])) {
                $officeAddress = null;
                if (! empty($data['office_address'])) {
                    $officeAddress = Address::create([
                        'full_address_text' => $data['office_address'],
                    ]);
                }

                ClientOrganization::create([
                    'client_id'          => $client->client_id,
                    'organization_name'  => $data['organization_name'],
                    'registration_no'    => $data['registration_no'] ?? null,
                    'pan_vat_no'         => $data['pan_vat_no'] ?? null,
                    'authorized_person'  => $data['authorized_person'] ?? null,
                    'designation'        => $data['designation'] ?? null,
                    'office_address_id'  => $officeAddress?->address_id,
                ]);
            }

            if (! empty($data['req_purpose']) || ! empty($data['req_property_type']) || ! empty($data['req_preferred_location'])) {
                ClientPropertyRequirement::create([
                    'client_id'          => $client->client_id,
                    'purpose'            => $data['req_purpose'] ?? null,
                    'property_type'      => $data['req_property_type'] ?? null,
                    'preferred_location' => $data['req_preferred_location'] ?? null,
                    'required_area'      => $data['req_required_area'] ?? null,
                    'estimated_budget'   => $data['req_estimated_budget'] ?? null,
                    'purchase_timeline'  => $data['req_purchase_timeline'] ?? null,
                ]);
            }

            if (! empty($data['available_for']) || ! empty($data['property_location']) || ! empty($data['kitta_no'])) {
                ClientOwnerListing::create([
                    'client_id'          => $client->client_id,
                    'available_for'      => $data['available_for'] ?? [],
                    'property_location'  => $data['property_location'] ?? null,
                    'kitta_no'           => $data['kitta_no'] ?? null,
                    'land_area'          => $data['land_area'] ?? null,
                    'building_details'   => $data['building_details'] ?? null,
                    'expected_price'     => $data['expected_price'] ?? null,
                ]);
            }

            foreach ($data['requested_services'] as $key) {
                $type = ServiceType::firstOrCreate(
                    ['service_name' => self::SERVICE_MAP[$key]],
                    ['is_active' => true]
                );
                ClientServiceRequest::create([
                    'client_id'       => $client->client_id,
                    'service_type_id' => $type->service_type_id,
                ]);
            }

            foreach (($data['document_status'] ?? []) as $key => $status) {
                if (! isset(self::DOCUMENT_MAP[$key]) || ! in_array($status, ['submitted', 'pending'], true)) {
                    continue;
                }
                [$docName, $category] = self::DOCUMENT_MAP[$key];
                $docType = DocumentType::firstOrCreate(
                    ['doc_name' => $docName],
                    ['category' => $category]
                );
                ClientDocument::create([
                    'client_id'   => $client->client_id,
                    'doc_type_id' => $docType->doc_type_id,
                    'status'      => $status,
                    'file_ref'    => $key === 'other_documents' ? ($data['other_documents_note'] ?? null) : null,
                ]);
            }

            return $client;
        });

        return response()->json([
            'success'     => true,
            'client_id'   => $client->client_id,
            'client_code' => $client->client_code,
            'message'     => 'Client registered successfully!',
        ], 201);
    }

    public function downloadPdf(int $id): Response
    {
        $client = Client::with([
            'permanentAddress',
            'currentAddress',
            'organization.officeAddress',
            'propertyRequirement',
            'ownerListing',
            'serviceRequests.serviceType',
            'documents.documentType',
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.client_registration', compact('client'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("client-registration-{$client->client_code}.pdf");
    }

    private function nextClientCode(): string
    {
        $count = Client::count() + 1;

        return 'CLT-' . date('Ymd') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    private function storeSignature(?UploadedFile $file, string $directory): ?string
    {
        if (! $file) {
            return null;
        }

        return $file->store($directory, 'public');
    }
}
