<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComplaintRequest;
use App\Models\Address;
use App\Models\Client;
use App\Models\Complaint;
use App\Models\ComplaintEvidence;
use App\Models\Property;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ComplaintController extends Controller
{
    private const EVIDENCE_TYPES = [
        'photo',
        'screenshot',
        'agreement_copy',
        'payment_receipt',
        'other',
    ];

    public function index(): InertiaResponse
    {
        return Inertia::render('Complaint/ComplaintForm');
    }

    public function store(StoreComplaintRequest $request): JsonResponse
    {
        $data = $request->validated();

        $complaint = DB::transaction(function () use ($data, $request) {
            $client = $this->resolveClient($data);

            $propertyId = null;
            if (! empty($data['property_code'])) {
                $propertyId = Property::query()
                    ->where('property_code', $data['property_code'])
                    ->value('property_id');
            }

            $customerSig = $this->storeFile($request->file('customer_signature'), 'signatures/complaints');
            $receivedBySig = $this->storeFile($request->file('received_by_signature'), 'signatures/staff');
            $reviewedBySig = $this->storeFile($request->file('reviewed_by_signature'), 'signatures/staff');

            $complaint = Complaint::create([
                'complaint_code'              => $this->nextComplaintCode(),
                'complaint_date'              => $data['complaint_date'],
                'complaint_time'              => $data['complaint_time'],
                'received_through'            => $data['received_through'],
                'received_through_other'      => $data['received_through_other'] ?? null,
                'received_by_name'            => $data['received_by_name'] ?? null,
                'received_by_designation'     => $data['received_by_designation'] ?? null,
                'received_by_signature_path'  => $receivedBySig,
                'received_by_date'            => $data['received_by_date'] ?? null,
                'client_id'                   => $client->client_id,
                'property_id'                 => $propertyId,
                'property_location'           => $data['property_location'] ?? null,
                'kitta_no'                    => $data['kitta_no'] ?? null,
                'service_reference'           => $data['service_reference'] ?? null,
                'service_date'                => $data['service_date'] ?? null,
                'category'                    => $data['category'],
                'category_other'              => $data['category_other'] ?? null,
                'description'                 => $data['description'],
                'priority'                    => $data['priority'],
                'assigned_department'         => $data['assigned_department'] ?? null,
                'assigned_officer_name'       => $data['assigned_officer_name'] ?? null,
                'investigation_date'          => $data['investigation_date'] ?? null,
                'findings'                    => $data['findings'] ?? null,
                'corrective_action_taken'     => $data['corrective_action_taken'] ?? null,
                'resolution_date'             => $data['resolution_date'] ?? null,
                'status'                      => $data['status'],
                'satisfaction_level'          => $data['satisfaction_level'] ?? null,
                'customer_remarks'            => $data['customer_remarks'] ?? null,
                'customer_signature_name'     => $data['customer_signature_name'],
                'customer_signature_path'     => $customerSig,
                'customer_signature_date'     => $data['customer_signature_date'],
                'reviewed_by_name'            => $data['reviewed_by_name'] ?? null,
                'reviewed_by_designation'     => $data['reviewed_by_designation'] ?? null,
                'reviewed_by_signature_path'  => $reviewedBySig,
                'reviewed_by_date'            => $data['reviewed_by_date'] ?? null,
            ]);

            $this->storeEvidence($complaint, $data, $request);

            return $complaint;
        });

        return response()->json([
            'success'        => true,
            'complaint_id'   => $complaint->complaint_id,
            'complaint_code' => $complaint->complaint_code,
            'message'        => 'Complaint registered successfully!',
        ], 201);
    }

    public function downloadPdf(int $id): Response
    {
        $complaint = Complaint::with(['client.permanentAddress', 'property', 'evidence'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('pdf.complaint', compact('complaint'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("customer-complaint-{$complaint->complaint_code}.pdf");
    }

    private function resolveClient(array $data): Client
    {
        if (! empty($data['client_code'])) {
            return Client::query()
                ->where('client_code', $data['client_code'])
                ->firstOrFail();
        }

        $address = Address::create([
            'full_address_text' => $data['address'],
        ]);

        return Client::create([
            'client_code'          => $this->nextClientCode(),
            'client_type'          => $data['customer_type'],
            'client_type_other'    => $data['customer_type_other'] ?? null,
            'full_name'            => $data['full_name'],
            'mobile_no'            => $data['mobile_no'],
            'email'                => $data['email'] ?? null,
            'permanent_address_id' => $address->address_id,
            'registration_date'    => now()->toDateString(),
            'mis_entry_status'     => 'pending',
            'is_active'            => true,
        ]);
    }

    private function storeEvidence(Complaint $complaint, array $data, StoreComplaintRequest $request): void
    {
        $checked = $data['attached_evidence'] ?? [];
        $files = $request->file('evidence_files', []);

        foreach (self::EVIDENCE_TYPES as $type) {
            $file = $files[$type] ?? null;
            $isChecked = in_array($type, $checked, true);

            if (! $isChecked && ! $file) {
                continue;
            }

            $path = $this->storeFile($file instanceof UploadedFile ? $file : null, 'complaints/evidence');

            $ref = $path;
            if (! $ref) {
                $ref = $type === 'other'
                    ? ($data['evidence_other_note'] ?? 'attached')
                    : 'attached';
            } elseif ($type === 'other' && ! empty($data['evidence_other_note'])) {
                $ref = $path.' | '.$data['evidence_other_note'];
            }

            ComplaintEvidence::create([
                'complaint_id'  => $complaint->complaint_id,
                'evidence_type' => $type,
                'file_ref'      => $ref,
            ]);
        }
    }

    private function nextComplaintCode(): string
    {
        $count = Complaint::count() + 1;

        return 'CMP-' . date('Ymd') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    private function nextClientCode(): string
    {
        $count = Client::count() + 1;

        return 'CLT-' . date('Ymd') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    private function storeFile(?UploadedFile $file, string $directory): ?string
    {
        if (! $file) {
            return null;
        }

        return $file->store($directory, 'public');
    }
}
