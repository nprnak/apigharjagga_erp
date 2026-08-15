<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Property;
use App\Models\Staff;
use App\Models\ValuationReport;
use App\Models\ValuationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ValuationController extends Controller
{
    public function index(Request $request): Response
    {
        $query = ValuationRequest::with([
            'client:client_id,full_name,client_code',
            'property:property_id,property_code,property_type',
            'assignedValuator:staff_id,full_name',
        ])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) =>
                $q->where('request_code', 'like', "%{$s}%")
            )
            ->latest('created_at');

        return Inertia::render('Admin/Valuations/Index', [
            'requests' => $query->paginate(15)->withQueryString(),
            'filters'  => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Valuations/Form', [
            'clients'    => Client::where('is_active', true)->get(['client_id', 'full_name', 'client_code']),
            'properties' => Property::with('owner:client_id,full_name')
                ->whereNotIn('status', ['sold', 'withdrawn'])
                ->get(['property_id', 'property_code', 'property_type', 'owner_client_id']),
            'valuators'  => Staff::where('is_active', true)->get(['staff_id', 'full_name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id'                  => ['required', 'exists:clients,client_id'],
            'property_id'                => ['required', 'exists:properties,property_id'],
            'purpose_of_valuation'       => ['nullable', 'in:bank_loan_mortgage,buying_selling,insurance,legal,investment_decision,other'],
            'requested_valuation_type'   => ['nullable', 'in:market_value,forced_sale_value,government_value_reference,rental_value'],
            'preferred_visit_date'       => ['nullable', 'date'],
            'site_contact_person_name'   => ['nullable', 'string', 'max:150'],
            'site_contact_mobile'        => ['nullable', 'string', 'max:20'],
            'assigned_valuator_staff_id' => ['nullable', 'exists:staff,staff_id'],
            'remarks'                    => ['nullable', 'string'],
        ]);

        $code = 'VAL-' . str_pad(ValuationRequest::max('request_id') + 1, 6, '0', STR_PAD_LEFT);

        ValuationRequest::create(array_merge($validated, [
            'request_code'              => $code,
            'application_received_date' => now()->toDateString(),
            'status'                    => 'received',
        ]));

        return redirect()->route('admin.valuations.index')->with('success', 'Valuation request created.');
    }

    public function show(ValuationRequest $valuation): Response
    {
        $valuation->load([
            'client', 'property.address', 'assignedValuator', 'reports.valuator', 'reports.approvedBy',
        ]);

        return Inertia::render('Admin/Valuations/Show', [
            'valuation' => $valuation,
            'valuators' => Staff::where('is_active', true)->get(['staff_id', 'full_name']),
        ]);
    }

    public function updateStatus(Request $request, ValuationRequest $valuation): RedirectResponse
    {
        $validated = $request->validate([
            'status'                     => ['required', 'in:received,site_visit_scheduled,in_progress,report_issued,cancelled'],
            'assigned_valuator_staff_id' => ['nullable', 'exists:staff,staff_id'],
            'field_visit_date'           => ['nullable', 'date'],
            'remarks'                    => ['nullable', 'string'],
        ]);

        $valuation->update($validated);

        return back()->with('success', 'Status updated.');
    }

    public function storeReport(Request $request, ValuationRequest $valuation): RedirectResponse
    {
        $validated = $request->validate([
            'valuation_type'   => ['required', 'in:market_value,forced_sale_value,mortgage_valuation,fair_value,insurance_value,investment_value,rental_value,government_valuation,asset_valuation'],
            'valuated_amount'  => ['required', 'numeric', 'min:0'],
            'rate_basis'       => ['nullable', 'string'],
            'valuator_staff_id' => ['nullable', 'exists:staff,staff_id'],
            'issued_date'      => ['nullable', 'date'],
        ]);

        $reportNo = 'RPT-' . str_pad(ValuationReport::max('report_id') + 1, 6, '0', STR_PAD_LEFT);

        ValuationReport::create(array_merge($validated, [
            'report_no'   => $reportNo,
            'request_id'  => $valuation->request_id,
            'property_id' => $valuation->property_id,
            'approval_status' => 'draft',
        ]));

        $valuation->update(['status' => 'report_issued']);

        return back()->with('success', 'Valuation report created.');
    }
}
