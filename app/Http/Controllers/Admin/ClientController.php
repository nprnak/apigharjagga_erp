<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Client;
use App\Models\Role;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Client::with('permanentAddress')
            ->when($request->search, fn ($q, $s) =>
                $q->where('full_name', 'like', "%{$s}%")
                  ->orWhere('client_code', 'like', "%{$s}%")
                  ->orWhere('mobile_no', 'like', "%{$s}%")
            )
            ->when($request->type, fn ($q, $t) => $q->where('client_type', $t))
            ->latest('created_at');

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'type']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Clients/Form', [
            'staffList' => Staff::where('is_active', true)->get(['staff_id', 'full_name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_type'        => ['required', 'in:owner,buyer,investor,tenant,agent,other'],
            'full_name'          => ['required', 'string', 'max:150'],
            'father_mother_name' => ['nullable', 'string', 'max:150'],
            'citizenship_no'     => ['nullable', 'string', 'max:50', 'unique:clients'],
            'mobile_no'          => ['required', 'string', 'max:20'],
            'email'              => ['nullable', 'email', 'max:150'],
            'gender'             => ['nullable', 'string'],
            'date_of_birth'      => ['nullable', 'date'],
            'occupation'         => ['nullable', 'string', 'max:100'],
            'province'           => ['nullable', 'string'],
            'district'           => ['nullable', 'string'],
            'municipality'       => ['nullable', 'string'],
            'ward_no'            => ['nullable', 'integer'],
            'tole'               => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $address = Address::create([
                'province'     => $validated['province'] ?? null,
                'district'     => $validated['district'] ?? null,
                'municipality' => $validated['municipality'] ?? null,
                'ward_no'      => $validated['ward_no'] ?? null,
                'tole'         => $validated['tole'] ?? null,
            ]);

            $code = 'CLT-' . str_pad(Client::max('client_id') + 1, 6, '0', STR_PAD_LEFT);

            Client::create([
                'client_code'        => $code,
                'client_type'        => $validated['client_type'],
                'full_name'          => $validated['full_name'],
                'father_mother_name' => $validated['father_mother_name'] ?? null,
                'citizenship_no'     => $validated['citizenship_no'] ?? null,
                'mobile_no'          => $validated['mobile_no'],
                'email'              => $validated['email'] ?? null,
                'gender'             => $validated['gender'] ?? null,
                'date_of_birth'      => $validated['date_of_birth'] ?? null,
                'occupation'         => $validated['occupation'] ?? null,
                'permanent_address_id' => $address->address_id,
                'registered_by'      => $request->user()?->id,
                'registration_date'  => now()->toDateString(),
                'mis_entry_status'   => 'completed',
            ]);
        });

        return redirect()->route('admin.clients.index')->with('success', 'Client registered successfully.');
    }

    public function show(Client $client): Response
    {
        $client->load([
            'permanentAddress', 'currentAddress',
            'properties', 'documents.docType',
            'serviceRequests.serviceType',
        ]);

        return Inertia::render('Admin/Clients/Show', ['client' => $client]);
    }

    public function edit(Client $client): Response
    {
        $client->load('permanentAddress');

        return Inertia::render('Admin/Clients/Form', [
            'client'    => $client,
            'staffList' => Staff::where('is_active', true)->get(['staff_id', 'full_name']),
        ]);
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'client_type'    => ['required', 'in:owner,buyer,investor,tenant,agent,other'],
            'full_name'      => ['required', 'string', 'max:150'],
            'mobile_no'      => ['required', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:150'],
            'citizenship_no' => ['nullable', 'string', 'max:50', "unique:clients,citizenship_no,{$client->client_id},client_id"],
            'gender'         => ['nullable', 'string'],
            'date_of_birth'  => ['nullable', 'date'],
            'occupation'     => ['nullable', 'string', 'max:100'],
        ]);

        $client->update($validated);

        return redirect()->route('admin.clients.show', $client)->with('success', 'Client updated successfully.');
    }
}
