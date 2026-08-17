<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Registration - {{ $client->client_code }}</title>
    <style>
        @font-face {
            font-family: 'NotoDevanagari';
            font-style: normal;
            font-weight: normal;
            src: url('{{ str_replace("\\", "/", storage_path("fonts/NotoSansDevanagari.ttf")) }}');
        }
        .np { font-family: 'NotoDevanagari', 'DejaVu Sans', sans-serif; }
        @page { size: A4 portrait; margin: 20mm 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10pt; color: #000; line-height: 1.4; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .doc-ref { font-size: 7pt; color: #555; }
        .org-name { font-size: 13pt; font-weight: bold; text-align: center; }
        .org-sub { font-size: 9pt; text-align: center; color: #333; }
        .form-title { font-size: 12pt; font-weight: bold; text-align: center; text-transform: uppercase; margin: 8px 0 2px; }
        .form-title-np { font-size: 10pt; text-align: center; color: #333; margin-bottom: 8px; }
        .top-line { border-top: 2px solid #000; border-bottom: 1px solid #000; padding: 3px 0; }
        .meta-table, .data-table, .bordered-table, .sig-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .meta-table td, .data-table td { font-size: 9pt; padding: 4px 6px; }
        .data-table .label { font-weight: bold; width: 32%; color: #333; }
        .data-table .value { border-bottom: 1px dotted #999; }
        .section-heading { font-size: 10pt; font-weight: bold; border-bottom: 1px solid #000; padding: 4px 0; margin: 12px 0 6px; text-transform: uppercase; }
        .section-heading span { font-weight: normal; font-size: 9pt; }
        .bordered-table th, .bordered-table td { border: 1px solid #000; padding: 4px 6px; font-size: 9pt; text-align: left; }
        .bordered-table th { background: #f0f0f0; }
        .declaration-box { border: 1px solid #000; padding: 8px 10px; margin: 6px 0; font-size: 9pt; line-height: 1.6; }
        .sig-table td { width: 33%; padding: 5px 8px; vertical-align: top; font-size: 8pt; }
        .sig-label { font-weight: bold; text-transform: uppercase; display: block; margin-bottom: 8px; }
        .sig-line { border-bottom: 1px solid #000; margin: 28px 0 3px; }
        .footer { margin-top: 12px; padding-top: 5px; border-top: 1px solid #000; text-align: center; font-size: 7pt; color: #555; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
@php
    $sig = function (?string $path) {
        $full = $path ? storage_path('app/public/' . $path) : null;
        return ($full && file_exists($full)) ? $full : null;
    };
    $clientType = $client->client_type === 'other'
        ? ($client->client_type_other ?: 'Other')
        : ucwords(str_replace('_', ' ', $client->client_type ?? '—'));
    $services = $client->serviceRequests->map(fn ($r) => $r->serviceType->service_name ?? null)->filter();
    $docs = $client->documents;
@endphp

<table class="header-table">
    <tr>
        <td style="width:25%;"><span class="doc-ref">Document Code: AGJ-FRM-07<br>Version: 1.0</span></td>
        <td style="width:50%; text-align:center;">
            <div class="org-name">Api Ghar Jagga Pvt. Ltd.</div>
            <div class="org-sub np">अपि घर जग्गा प्रा. लि.</div>
        </td>
        <td style="width:25%; text-align:right;"><span class="doc-ref">ANNEX – F</span></td>
    </tr>
</table>
<div class="top-line"></div>
<div class="form-title">Client Registration Form</div>
<div class="form-title-np np">ग्राहक दर्ता फारम</div>
<table class="meta-table">
    <tr>
        <td><strong>Client ID:</strong> {{ $client->client_code }}</td>
        <td style="text-align:right;"><strong>Date:</strong> {{ optional($client->registration_date)->format('Y-m-d') }}</td>
    </tr>
</table>
<p style="font-size:8pt; color:#333; margin-bottom:8px;">
    Project / Service: Api Ghar Jagga Property Listing, Verification &amp; Valuation Service
</p>

<div class="section-heading">1. Client Type <span class="np">/ ग्राहकको प्रकार</span></div>
<table class="data-table"><tr><td class="label">Type</td><td class="value">{{ $clientType }}</td></tr></table>

<div class="section-heading">2. Client Personal Information <span class="np">/ व्यक्तिगत विवरण</span></div>
<table class="data-table">
    <tr><td class="label">Full Name</td><td class="value">{{ $client->full_name }}</td></tr>
    <tr><td class="label">Father/Mother Name</td><td class="value">{{ $client->father_mother_name ?? '—' }}</td></tr>
    <tr><td class="label">Spouse Name</td><td class="value">{{ $client->spouse_name ?? '—' }}</td></tr>
    <tr><td class="label">Citizenship No.</td><td class="value">{{ $client->citizenship_no ?? '—' }}</td></tr>
    <tr><td class="label">Nationality</td><td class="value">{{ $client->nationality ?? '—' }}</td></tr>
    <tr><td class="label">Date of Birth</td><td class="value">{{ optional($client->date_of_birth)->format('Y-m-d') ?: '—' }}</td></tr>
    <tr><td class="label">Gender</td><td class="value">{{ $client->gender ? ucfirst($client->gender) : '—' }}</td></tr>
    <tr><td class="label">Occupation</td><td class="value">{{ $client->occupation ?? '—' }}</td></tr>
</table>

<div class="section-heading">3. Contact Details <span class="np">/ सम्पर्क विवरण</span></div>
<table class="data-table">
    <tr><td class="label">Mobile No.</td><td class="value">{{ $client->mobile_no }}</td></tr>
    <tr><td class="label">Alternate Contact</td><td class="value">{{ $client->alt_contact_no ?? '—' }}</td></tr>
    <tr><td class="label">Email</td><td class="value">{{ $client->email ?? '—' }}</td></tr>
    <tr><td class="label">Permanent Address</td><td class="value">{{ $client->permanentAddress->full_address_text ?? '—' }}</td></tr>
    <tr><td class="label">Current Address</td><td class="value">{{ $client->currentAddress->full_address_text ?? '—' }}</td></tr>
</table>

<div class="section-heading">4. Organization Details <span class="np">/ संस्था सम्बन्धी विवरण</span></div>
@if($client->organization)
<table class="data-table">
    <tr><td class="label">Organization Name</td><td class="value">{{ $client->organization->organization_name }}</td></tr>
    <tr><td class="label">Registration No.</td><td class="value">{{ $client->organization->registration_no ?? '—' }}</td></tr>
    <tr><td class="label">PAN/VAT No.</td><td class="value">{{ $client->organization->pan_vat_no ?? '—' }}</td></tr>
    <tr><td class="label">Authorized Person</td><td class="value">{{ $client->organization->authorized_person ?? '—' }}</td></tr>
    <tr><td class="label">Designation</td><td class="value">{{ $client->organization->designation ?? '—' }}</td></tr>
    <tr><td class="label">Office Address</td><td class="value">{{ $client->organization->officeAddress->full_address_text ?? '—' }}</td></tr>
</table>
@else
<p style="font-size:9pt;">Not applicable</p>
@endif

<div class="section-heading">5. Property Requirement Details <span class="np">/ सम्पत्ति आवश्यकता</span></div>
@if($client->propertyRequirement)
<table class="data-table">
    <tr><td class="label">Purpose</td><td class="value">{{ ucfirst($client->propertyRequirement->purpose ?? '—') }}</td></tr>
    <tr><td class="label">Property Type</td><td class="value">{{ ucfirst($client->propertyRequirement->property_type ?? '—') }}</td></tr>
    <tr><td class="label">Preferred Location</td><td class="value">{{ $client->propertyRequirement->preferred_location ?? '—' }}</td></tr>
    <tr><td class="label">Required Area</td><td class="value">{{ $client->propertyRequirement->required_area ?? '—' }}</td></tr>
    <tr><td class="label">Estimated Budget</td><td class="value">{{ $client->propertyRequirement->estimated_budget ? 'Rs. '.number_format($client->propertyRequirement->estimated_budget, 2) : '—' }}</td></tr>
    <tr><td class="label">Purchase Timeline</td><td class="value">{{ $client->propertyRequirement->purchase_timeline ?? '—' }}</td></tr>
</table>
@else
<p style="font-size:9pt;">Not provided</p>
@endif

<div class="section-heading">6. Property Owner Details <span class="np">/ सम्पत्ति धनी विवरण</span></div>
@if($client->ownerListing)
<table class="data-table">
    <tr><td class="label">Available For</td><td class="value">{{ implode(', ', array_map('ucfirst', $client->ownerListing->available_for ?? [])) ?: '—' }}</td></tr>
    <tr><td class="label">Property Location</td><td class="value">{{ $client->ownerListing->property_location ?? '—' }}</td></tr>
    <tr><td class="label">Kitta No.</td><td class="value">{{ $client->ownerListing->kitta_no ?? '—' }}</td></tr>
    <tr><td class="label">Land Area</td><td class="value">{{ $client->ownerListing->land_area ?? '—' }}</td></tr>
    <tr><td class="label">Building Details</td><td class="value">{{ $client->ownerListing->building_details ?? '—' }}</td></tr>
    <tr><td class="label">Expected Price</td><td class="value">{{ $client->ownerListing->expected_price ? 'Rs. '.number_format($client->ownerListing->expected_price, 2) : '—' }}</td></tr>
</table>
@else
<p style="font-size:9pt;">Not provided</p>
@endif

<div class="page-break"></div>

<div class="section-heading">7. Required Service Selection <span class="np">/ आवश्यक सेवा छनोट</span></div>
<table class="bordered-table">
    <tr><th>Service</th><th>Selected</th></tr>
    @foreach($services as $name)
        <tr><td>{{ $name }}</td><td>Yes</td></tr>
    @endforeach
    @if($services->isEmpty())
        <tr><td colspan="2">None selected</td></tr>
    @endif
</table>

<div class="section-heading">8. Document Submission Checklist <span class="np">/ कागजात चेकलिस्ट</span></div>
<table class="bordered-table">
    <tr><th>Document</th><th>Status</th></tr>
    @forelse($docs as $doc)
        <tr>
            <td>{{ $doc->documentType->doc_name ?? '—' }}@if($doc->file_ref) ({{ $doc->file_ref }})@endif</td>
            <td>{{ ucfirst($doc->status) }}</td>
        </tr>
    @empty
        <tr><td colspan="2">No documents marked</td></tr>
    @endforelse
</table>

<div class="section-heading">9. Digital Registration Details <span class="np">/ डिजिटल दर्ता</span></div>
<table class="data-table">
    <tr><td class="label">Client ID</td><td class="value">{{ $client->client_code }}</td></tr>
    <tr><td class="label">Registration Date</td><td class="value">{{ optional($client->registration_date)->format('Y-m-d') }}</td></tr>
    <tr><td class="label">Registered By</td><td class="value">{{ $client->registered_by_name ?? '—' }} {{ $client->registered_by_designation ? '(' . $client->registered_by_designation . ')' : '' }}</td></tr>
    <tr><td class="label">Mobile App User ID</td><td class="value">{{ $client->mobile_app_user_id ?? '—' }}</td></tr>
    <tr><td class="label">MIS Entry Status</td><td class="value">{{ ucfirst($client->mis_entry_status) }}</td></tr>
</table>

<div class="section-heading">10. Client Declaration <span class="np">/ ग्राहक घोषणा</span></div>
<div class="declaration-box">
    I hereby confirm that the information provided in this registration form is true and correct. I authorize Api Ghar Jagga Pvt. Ltd. to verify, process, store and use the information for property-related services.
    <p class="np" style="font-size:8pt; margin-top:6px;">म यसद्वारा घोषणा गर्दछु कि यस फारममा उपलब्ध गराइएको जानकारी सत्य र सही छ। म Api Ghar Jagga Pvt. Ltd. लाई सम्पत्ति सम्बन्धी सेवा प्रदान गर्ने प्रयोजनका लागि उक्त विवरण प्रमाणीकरण, प्रशोधन, अभिलेख तथा प्रयोग गर्न अनुमति दिन्छु।</p>
</div>

<table class="sig-table">
    <tr>
        <td>
            <span class="sig-label">Client Signature</span>
            @if($img = $sig($client->signature_path))
                <img src="{{ $img }}" style="height:40px; max-width:140px;">
            @else
                <div class="sig-line"></div>
            @endif
            <div>Name: {{ $client->signature_name ?: $client->full_name }}</div>
            <div>Date: {{ optional($client->signature_date)->format('Y-m-d') ?: '—' }}</div>
        </td>
        <td>
            <span class="sig-label">Registered By</span>
            @if($img = $sig($client->registered_by_signature_path))
                <img src="{{ $img }}" style="height:40px; max-width:140px;">
            @else
                <div class="sig-line"></div>
            @endif
            <div>Name: {{ $client->registered_by_name ?? '—' }}</div>
            <div>Designation: {{ $client->registered_by_designation ?? '—' }}</div>
            <div>Date: {{ optional($client->registered_by_date)->format('Y-m-d') ?: '—' }}</div>
        </td>
        <td>
            <span class="sig-label">Approved By</span>
            @if($img = $sig($client->approved_by_signature_path))
                <img src="{{ $img }}" style="height:40px; max-width:140px;">
            @else
                <div class="sig-line"></div>
            @endif
            <div>Name: {{ $client->approved_by_name ?? '—' }}</div>
            <div>Designation: {{ $client->approved_by_designation ?? '—' }}</div>
            <div>Date: {{ optional($client->approved_by_date)->format('Y-m-d') ?: '—' }}</div>
        </td>
    </tr>
</table>

<div class="footer">© Api Ghar Jagga Pvt. Ltd. | AGJ-FRM-07 | Generated: {{ now()->format('Y-m-d H:i') }}</div>
</body>
</html>
