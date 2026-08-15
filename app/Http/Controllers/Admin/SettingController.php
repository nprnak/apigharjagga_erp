<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function edit(): Response
    {
        $company = CompanyProfile::getSingleton();

        return Inertia::render('Admin/Settings', [
            'company' => array_merge($company->toArray(), [
                'logo_url' => $company->logo_url,
            ]),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name'           => ['required', 'string', 'max:200'],
            'registration_no'        => ['nullable', 'string', 'max:50'],
            'broker_licence_no'      => ['nullable', 'string', 'max:50'],
            'land_survey_licence_no' => ['nullable', 'string', 'max:50'],
            'pan_vat_no'             => ['nullable', 'string', 'max:50'],
            'contact_no'             => ['nullable', 'string', 'max:20'],
            'email'                  => ['nullable', 'email', 'max:150'],
            'website'                => ['nullable', 'url', 'max:200'],
            'tagline'                => ['nullable', 'string', 'max:300'],
            'licence_expiry_date'    => ['nullable', 'date'],
            'logo'                   => ['nullable', 'image', 'max:2048'],
        ]);

        $company = CompanyProfile::first();

        if (! $company) {
            $company = new CompanyProfile();
        }

        $company->fill(collect($validated)->except('logo')->toArray());

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $company->logo_path = $path;
        }

        $company->save();

        return back()->with('success', 'Settings saved successfully.');
    }
}
