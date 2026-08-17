<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgreementRequest;
use App\Models\Address;
use App\Models\Agreement;
use App\Models\AgreementParty;
use App\Models\AgreementWitness;
use App\Models\Client;
use App\Models\Property;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AgreementController extends Controller
{
    /**
     * Show the house/land sale-purchase agreement form.
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('Agreement/AgreementForm');
    }

    /**
     * Store a new sale/purchase agreement.
     * Creates: addresses, clients (seller & buyer), property, agreement,
     * agreement parties, and witnesses — all in one transaction.
     */
    public function store(StoreAgreementRequest $request): JsonResponse
    {
        $data = $request->validated();

        $agreement = DB::transaction(function () use ($data, $request) {

            $sellerAddress = Address::create([
                'full_address_text' => $data['seller_permanent_address'],
            ]);

            $seller = Client::firstOrCreate(
                ['citizenship_no' => $data['seller_citizenship_no']],
                [
                    'client_code'          => 'CLT-' . strtoupper(Str::random(8)),
                    'client_type'          => 'owner',
                    'full_name'            => $data['seller_full_name'],
                    'father_mother_name'   => $data['seller_father_mother_name'] ?? null,
                    'mobile_no'            => $data['seller_contact_no'],
                    'permanent_address_id' => $sellerAddress->address_id,
                    'registration_date'    => now()->toDateString(),
                    'mis_entry_status'     => 'pending',
                    'is_active'            => true,
                ]
            );

            $buyerAddress = Address::create([
                'full_address_text' => $data['buyer_permanent_address'],
            ]);

            $buyer = Client::firstOrCreate(
                ['citizenship_no' => $data['buyer_citizenship_no']],
                [
                    'client_code'          => 'CLT-' . strtoupper(Str::random(8)),
                    'client_type'          => 'buyer',
                    'full_name'            => $data['buyer_full_name'],
                    'father_mother_name'   => $data['buyer_father_mother_name'] ?? null,
                    'mobile_no'            => $data['buyer_contact_no'],
                    'permanent_address_id' => $buyerAddress->address_id,
                    'registration_date'    => now()->toDateString(),
                    'mis_entry_status'     => 'pending',
                    'is_active'            => true,
                ]
            );

            $propertyAddress = Address::create([
                'district'     => $data['district'],
                'municipality' => $data['municipality'],
                'ward_no'      => $data['ward_no'],
            ]);

            $property = Property::create([
                'property_code'   => 'PROP-' . strtoupper(Str::random(8)),
                'owner_client_id' => $seller->client_id,
                'ownership_role'  => 'self',
                'property_type'   => $data['property_type'],
                'address_id'      => $propertyAddress->address_id,
                'kitta_no'        => $data['kitta_no'],
                'area'            => $data['area'],
                'status'          => 'under_negotiation',
            ]);

            $sellerSignature = $request->file('seller_signature')
                ? $request->file('seller_signature')->store('signatures/agreements', 'public')
                : null;
            $buyerSignature = $request->file('buyer_signature')
                ? $request->file('buyer_signature')->store('signatures/agreements', 'public')
                : null;

            $agreement = Agreement::create([
                'agreement_type'        => 'sale_purchase',
                'property_id'           => $property->property_id,
                'house_description'     => $data['house_description'] ?? null,
                'boundary_east'         => $data['boundary_east'] ?? null,
                'boundary_west'         => $data['boundary_west'] ?? null,
                'boundary_north'        => $data['boundary_north'] ?? null,
                'boundary_south'        => $data['boundary_south'] ?? null,
                'agreement_date'        => $data['agreement_date'],
                'place'                 => $data['place'],
                'total_price'           => $data['total_price'],
                'total_price_words'     => $data['total_price_words'],
                'advance_payment'       => isset($data['advance_payment']) && $data['advance_payment'] !== '' ? $data['advance_payment'] : null,
                'balance_payment'       => isset($data['balance_payment']) && $data['balance_payment'] !== '' ? $data['balance_payment'] : null,
                'final_payment_date'    => $data['final_payment_date'] ?? null,
                'status'                => 'active',
                'governing_law'         => 'Prevailing laws of Nepal',
                'seller_signature_path' => $sellerSignature,
                'buyer_signature_path'  => $buyerSignature,
            ]);

            AgreementParty::create([
                'agreement_id' => $agreement->agreement_id,
                'party_role'   => 'seller',
                'client_id'    => $seller->client_id,
            ]);

            AgreementParty::create([
                'agreement_id' => $agreement->agreement_id,
                'party_role'   => 'buyer',
                'client_id'    => $buyer->client_id,
            ]);

            if (! empty($data['witness1_name'])) {
                AgreementWitness::create([
                    'agreement_id'   => $agreement->agreement_id,
                    'full_name'      => $data['witness1_name'],
                    'citizenship_no' => $data['witness1_citizenship_no'] ?? null,
                    'signature_path' => $request->file('witness1_signature')
                        ? $request->file('witness1_signature')->store('signatures/witnesses', 'public')
                        : null,
                ]);
            }

            if (! empty($data['witness2_name'])) {
                AgreementWitness::create([
                    'agreement_id'   => $agreement->agreement_id,
                    'full_name'      => $data['witness2_name'],
                    'citizenship_no' => $data['witness2_citizenship_no'] ?? null,
                    'signature_path' => $request->file('witness2_signature')
                        ? $request->file('witness2_signature')->store('signatures/witnesses', 'public')
                        : null,
                ]);
            }

            return $agreement;
        });

        return response()->json([
            'success'      => true,
            'agreement_id' => $agreement->agreement_id,
            'agreement_no' => 'AGJ-AGR-' . str_pad((string) $agreement->agreement_id, 5, '0', STR_PAD_LEFT),
            'message'      => 'Agreement submitted successfully!',
        ], 201);
    }

    /**
     * Download a PDF of the submitted sale/purchase agreement.
     */
    public function downloadPdf(int $id): Response
    {
        $agreement = Agreement::with([
            'property.address',
            'parties.client.permanentAddress',
            'witnesses',
        ])->findOrFail($id);

        $seller = $agreement->parties->firstWhere('party_role', 'seller');
        $buyer = $agreement->parties->firstWhere('party_role', 'buyer');

        $pdf = Pdf::loadView('pdf.agreement', compact('agreement', 'seller', 'buyer'))
            ->setPaper('a4', 'portrait');

        $agreementNo = 'AGJ-AGR-' . str_pad((string) $agreement->agreement_id, 5, '0', STR_PAD_LEFT);

        return $pdf->download("agreement-{$agreementNo}.pdf");
    }
}
