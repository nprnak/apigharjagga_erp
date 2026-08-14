<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Client;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Property::with(['owner:client_id,full_name,client_code', 'address'])
            ->when($request->search, fn ($q, $s) =>
                $q->where('property_code', 'like', "%{$s}%")
                  ->orWhere('kitta_no', 'like', "%{$s}%")
            )
            ->when($request->type, fn ($q, $t) => $q->where('property_type', $t))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest('created_at');

        return Inertia::render('Admin/Properties/Index', [
            'properties' => $query->paginate(15)->withQueryString(),
            'filters'    => $request->only(['search', 'type', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Properties/Form', [
            'clients' => Client::where('is_active', true)->get(['client_id', 'full_name', 'client_code']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'owner_client_id'   => ['required', 'exists:clients,client_id'],
            'property_type'     => ['required', 'in:land,house,apartment,commercial_building,office_space,industrial_property,agricultural_land,other'],
            'kitta_no'          => ['nullable', 'string', 'max:50'],
            'area'              => ['nullable', 'string', 'max:100'],
            'ownership_type'    => ['nullable', 'string', 'max:50'],
            'ownership_certificate_no' => ['nullable', 'string', 'max:50'],
            'year_of_construction' => ['nullable', 'integer'],
            'no_of_floors'      => ['nullable', 'integer'],
            'structure_type'    => ['nullable', 'string'],
            'province'          => ['nullable', 'string'],
            'district'          => ['nullable', 'string'],
            'municipality'      => ['nullable', 'string'],
            'ward_no'           => ['nullable', 'integer'],
            'tole'              => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $address = Address::create([
                'province'     => $validated['province'] ?? null,
                'district'     => $validated['district'] ?? null,
                'municipality' => $validated['municipality'] ?? null,
                'ward_no'      => $validated['ward_no'] ?? null,
                'tole'         => $validated['tole'] ?? null,
            ]);

            $code = 'PROP-' . str_pad(Property::max('property_id') + 1, 6, '0', STR_PAD_LEFT);

            Property::create([
                'property_code'   => $code,
                'owner_client_id' => $validated['owner_client_id'],
                'property_type'   => $validated['property_type'],
                'address_id'      => $address->address_id,
                'kitta_no'        => $validated['kitta_no'] ?? null,
                'area'            => $validated['area'] ?? null,
                'ownership_type'  => $validated['ownership_type'] ?? null,
                'ownership_certificate_no' => $validated['ownership_certificate_no'] ?? null,
                'year_of_construction' => $validated['year_of_construction'] ?? null,
                'no_of_floors'    => $validated['no_of_floors'] ?? null,
                'structure_type'  => $validated['structure_type'] ?? null,
                'status'          => 'draft',
            ]);
        });

        return redirect()->route('admin.properties.index')->with('success', 'Property registered successfully.');
    }

    public function show(Property $property): Response
    {
        $property->load(['owner', 'address', 'photos', 'documents.docType', 'valuationRequests.reports']);

        return Inertia::render('Admin/Properties/Show', ['property' => $property]);
    }

    public function edit(Property $property): Response
    {
        $property->load('address');

        return Inertia::render('Admin/Properties/Form', [
            'property' => $property,
            'clients'  => Client::where('is_active', true)->get(['client_id', 'full_name', 'client_code']),
        ]);
    }

    public function update(Request $request, Property $property): RedirectResponse
    {
        $validated = $request->validate([
            'property_type'  => ['required', 'in:land,house,apartment,commercial_building,office_space,industrial_property,agricultural_land,other'],
            'kitta_no'       => ['nullable', 'string', 'max:50'],
            'area'           => ['nullable', 'string', 'max:100'],
            'status'         => ['required', 'in:draft,listed,under_verification,under_valuation,under_negotiation,sold,rented,leased,withdrawn,rejected'],
            'year_of_construction' => ['nullable', 'integer'],
            'no_of_floors'   => ['nullable', 'integer'],
        ]);

        $property->update($validated);

        return redirect()->route('admin.properties.show', $property)->with('success', 'Property updated.');
    }
}
