<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KYC Verification - {{ $kyc->full_name ?? ('#' . $kyc->id) }}</title>
    <style>
        @font-face {
            font-family: 'Kalimati';
            font-style: normal;
            font-weight: normal;
            src: url('{{ str_replace("\\", "/", storage_path("fonts/Kalimati.ttf")) }}');
        }
        .np { font-family: 'Kalimati', 'DejaVu Sans', sans-serif; }
        @page { size: A4 portrait; margin: 16mm 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', 'Times', serif;
            font-size: 10.5pt;
            color: #000;
            line-height: 1.4;
        }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .doc-ref { font-size: 7pt; color: #555; }
        .org-name { font-size: 16pt; font-weight: bold; text-align: center; letter-spacing: 0.5px; }
        .org-sub { font-size: 10pt; text-align: center; color: #333; }
        .form-title { font-size: 13pt; font-weight: bold; text-align: center; text-transform: uppercase; margin: 8px 0 1px; letter-spacing: 0.5px; }
        .form-title-np { font-size: 11pt; text-align: center; color: #333; margin-bottom: 6px; }
        .top-line { border-top: 2.5px solid #14532d; border-bottom: 1px solid #000; padding: 3px 0; }

        .status-badge {
            display: inline-block;
            padding: 2px 14px;
            border: 1px solid #000;
            border-radius: 3px;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-approved { background: #e6f4ea; border-color: #14532d; color: #14532d; }
        .status-verified { background: #e0f2fe; border-color: #075985; color: #075985; }
        .status-pending { background: #fef7e0; border-color: #92400e; color: #92400e; }
        .status-rejected { background: #fce8e6; border-color: #991b1b; color: #991b1b; }

        .meta-table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        .meta-table td { font-size: 9.5pt; padding: 3px 4px; vertical-align: middle; }

        .section-heading {
            font-size: 11pt;
            font-weight: bold;
            background: #f3f4f6;
            border-left: 4px solid #14532d;
            padding: 4px 8px;
            margin: 12px 0 6px;
            text-transform: uppercase;
        }
        .section-heading span { font-weight: normal; font-size: 10pt; text-transform: none; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .data-table td { font-size: 10pt; padding: 4px 6px; vertical-align: top; }
        .data-table .label { font-weight: bold; width: 32%; color: #222; }
        .data-table .value { border-bottom: 1px dotted #999; }

        .photos-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .photos-table td { width: 50%; text-align: center; vertical-align: top; padding: 6px; }
        .photo-frame { border: 1px solid #000; padding: 4px; display: inline-block; }
        .photo-frame img { max-height: 180px; max-width: 100%; }
        .photo-caption { font-size: 9pt; font-weight: bold; margin-bottom: 4px; text-transform: uppercase; }
        .no-photo { border: 1px dashed #999; padding: 28px 10px; color: #777; font-size: 9pt; }

        .checklist-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; border: 1px solid #999; }
        .checklist-table th { background: #f3f4f6; font-size: 9pt; text-align: left; padding: 4px 6px; border: 1px solid #999; }
        .checklist-table td { font-size: 9.5pt; padding: 4px 6px; border: 1px solid #999; }
        .check-yes { color: #14532d; font-weight: bold; }
        .check-no { color: #991b1b; }

        .note-box { border: 1px solid #000; padding: 8px 10px; margin: 6px 0; font-size: 10pt; }
        .declaration-box { border: 1px solid #999; background: #fafafa; padding: 8px 10px; margin: 10px 0; font-size: 9pt; font-style: italic; }

        .sig-table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        .sig-table td { width: 33.33%; padding: 6px 8px; vertical-align: bottom; font-size: 9pt; }
        .sig-photo { max-height: 45px; margin-bottom: 3px; }
        .sig-line { border-bottom: 1px solid #000; margin: 30px 0 3px; }
        .sig-label { font-weight: bold; text-transform: uppercase; display: block; }
        .sig-name { font-size: 9pt; margin-top: 2px; }

        .footer { margin-top: 14px; padding-top: 5px; border-top: 1px solid #000; text-align: center; font-size: 7.5pt; color: #555; }

        .stamp {
            position: absolute;
            top: 20mm;
            right: 18mm;
            border: 2.5px double #14532d;
            color: #14532d;
            padding: 5px 14px 6px;
            text-align: center;
            transform: rotate(-8deg);
        }
        .stamp .s1 { font-size: 12pt; font-weight: bold; letter-spacing: 2px; }
        .stamp .s2 { font-size: 9pt; font-weight: bold; margin-top: -2px; }
        .stamp .s3 { font-size: 7pt; border-top: 0.75px solid #14532d; margin-top: 3px; padding-top: 2px; letter-spacing: 0.5px; }
    </style>
</head>
<body>
@php
    $idTypeLabel = match ($kyc->id_type) {
        'citizenship' => 'Citizenship Card',
        'national_id' => 'National ID',
        'passport' => 'Passport',
        'driving_license' => 'Driving License',
        default => $kyc->id_type ?? '—',
    };
    $genderLabel = $kyc->gender ? ucfirst($kyc->gender) : '—';
    $permanent = collect([
        $kyc->permanent_tole,
        $kyc->permanent_ward_no ? 'Ward ' . $kyc->permanent_ward_no : null,
        $kyc->permanent_municipality,
        $kyc->permanent_district,
        $kyc->permanent_province,
    ])->filter()->implode(', ') ?: '—';
    $current = collect([
        $kyc->current_tole,
        $kyc->current_ward_no ? 'Ward ' . $kyc->current_ward_no : null,
        $kyc->current_municipality,
        $kyc->current_district,
        $kyc->current_province,
    ])->filter()->implode(', ') ?: '—';
    $refNo = 'AGJ-KYC-' . str_pad((string) $kyc->id, 5, '0', STR_PAD_LEFT);
@endphp

@if($kyc->status === 'approved')
<div class="stamp">
    <div class="s1">APPROVED</div>
    <div class="s2 np">स्वीकृत</div>
    <div class="s3">{{ $kyc->digital_client_id ?? $refNo }} &middot; {{ optional($kyc->approved_at)->format('Y-m-d') }}</div>
</div>
@endif

<table class="header-table">
    <tr>
        <td style="width:75%; text-align:center;">
            <div class="org-name">API Ghar Jagga</div>
            <div class="org-sub np">अपि घर जग्गा</div>
        </td>
        <td style="width:25%; text-align:right; vertical-align:bottom;"><span class="doc-ref">ANNEX &ndash; F<br>KYC RECORD</span></td>
    </tr>
</table>
<div class="top-line"></div>
<div class="form-title">Know Your Customer (KYC) Verification</div>
<div class="form-title-np np">ग्राहक पहिचान (के.वाई.सी.) प्रमाणीकरण</div>

<table class="meta-table">
    <tr>
        <td style="width:33%;"><strong>Reference No.:</strong> {{ $refNo }}</td>
        <td style="width:34%; text-align:center;">
            <span class="status-badge status-{{ $kyc->status }}">{{ ucfirst($kyc->status) }}</span>
        </td>
        <td style="width:33%; text-align:right;"><strong>Generated:</strong> {{ now()->format('Y-m-d H:i') }}</td>
    </tr>
</table>

<div class="section-heading">1. Client Type <span class="np">/ ग्राहकको प्रकार</span></div>
<table class="data-table">
    <tr><td class="label">Type</td><td class="value">{{ $kyc->user?->client_type ? ucfirst($kyc->user->client_type) : '—' }}</td></tr>
</table>

<div class="section-heading">2. Personal Information <span class="np">/ व्यक्तिगत विवरण</span></div>
<table class="data-table">
    <tr><td class="label">Full Name</td><td class="value np">{{ $kyc->full_name ?? '—' }}</td></tr>
    <tr><td class="label">Father / Mother Name</td><td class="value np">{{ $kyc->father_mother_name ?? '—' }}</td></tr>
    <tr><td class="label">Grandfather Name</td><td class="value np">{{ $kyc->grandfather_name ?? '—' }}</td></tr>
    <tr><td class="label">Spouse Name</td><td class="value np">{{ $kyc->spouse_name ?? '—' }}</td></tr>
    <tr><td class="label">Citizenship No.</td><td class="value np">{{ $kyc->citizenship_no ?? '—' }}</td></tr>
    <tr><td class="label">Date of Birth</td><td class="value">{{ optional($kyc->date_of_birth)->format('Y-m-d') ?: '—' }}</td></tr>
    <tr><td class="label">Gender</td><td class="value">{{ $genderLabel }}</td></tr>
    <tr><td class="label">Nationality</td><td class="value np">{{ $kyc->nationality ?? '—' }}</td></tr>
    <tr><td class="label">Occupation</td><td class="value np">{{ $kyc->occupation ?? '—' }}</td></tr>
</table>

<div class="section-heading">3. Contact Details <span class="np">/ सम्पर्क विवरण</span></div>
<table class="data-table">
    <tr><td class="label">Mobile No.</td><td class="value">{{ $kyc->mobile_no ?? '—' }}</td></tr>
    <tr><td class="label">Alternate Contact No.</td><td class="value">{{ $kyc->alt_contact_no ?? '—' }}</td></tr>
    <tr><td class="label">Telephone No.</td><td class="value">{{ $kyc->telephone_no ?? '—' }}</td></tr>
    <tr><td class="label">Email</td><td class="value">{{ $kyc->email ?? '—' }}</td></tr>
    <tr><td class="label">Permanent Address</td><td class="value np">{{ $permanent }}</td></tr>
    <tr><td class="label">Current Address</td><td class="value np">{{ $current }}</td></tr>
    <tr><td class="label">User Account</td><td class="value">{{ $kyc->user ? collect([$kyc->user->name, $kyc->user->email])->filter()->implode(' · ') : '—' }}</td></tr>
</table>

<div class="section-heading">4. Organization Details (If Applicable) <span class="np">/ संस्था सम्बन्धी विवरण</span></div>
@if($kyc->organization)
<table class="data-table">
    <tr><td class="label">Organization Name</td><td class="value np">{{ $kyc->organization->organization_name ?? '—' }}</td></tr>
    <tr><td class="label">Registration No.</td><td class="value">{{ $kyc->organization->registration_no ?? '—' }}</td></tr>
    <tr><td class="label">PAN / VAT No.</td><td class="value">{{ $kyc->organization->pan_vat_no ?? '—' }}</td></tr>
    <tr><td class="label">Authorized Person</td><td class="value np">{{ $kyc->organization->authorized_person ?? '—' }}</td></tr>
    <tr><td class="label">Designation</td><td class="value">{{ $kyc->organization->designation ?? '—' }}</td></tr>
    <tr><td class="label">Office Address</td><td class="value np">{{ $kyc->organization->office_address ?? '—' }}</td></tr>
</table>
@else
<p style="font-size:9.5pt; color:#555;">Not applicable — individual applicant.</p>
@endif

<div class="section-heading">5. Property Requirement Details <span class="np">/ सम्पत्ति आवश्यकता</span></div>
<p style="font-size:8.5pt; color:#555; margin-bottom:4px;">For Buyer / Investor / Tenant</p>
@if($kyc->propertyRequirement)
<table class="data-table">
    <tr><td class="label">Purpose</td><td class="value">{{ filled($kyc->propertyRequirement->purpose) ? collect($kyc->propertyRequirement->purpose)->map(fn ($p) => ucfirst($p))->implode(', ') : '—' }}</td></tr>
    <tr><td class="label">Property Type</td><td class="value">{{ filled($kyc->propertyRequirement->property_type) ? collect($kyc->propertyRequirement->property_type)->map(fn ($p) => ucfirst($p))->implode(', ') : '—' }}</td></tr>
    <tr><td class="label">Preferred Location</td><td class="value np">{{ $kyc->propertyRequirement->preferred_location ?? '—' }}</td></tr>
    <tr><td class="label">Required Area</td><td class="value">{{ $kyc->propertyRequirement->required_area ?? '—' }}</td></tr>
    <tr><td class="label">Estimated Budget</td><td class="value">{{ $kyc->propertyRequirement->estimated_budget ? 'Rs. '.number_format((float) $kyc->propertyRequirement->estimated_budget, 2) : '—' }}</td></tr>
    <tr><td class="label">Purchase Timeline</td><td class="value">{{ $kyc->propertyRequirement->purchase_timeline ?? '—' }}</td></tr>
</table>
@else
<p style="font-size:9.5pt; color:#555;">Not provided.</p>
@endif

<div class="page-break" style="page-break-before: always;"></div>

<div class="section-heading">7. Required Service Selection <span class="np">/ आवश्यक सेवा छनोट</span></div>
<table class="checklist-table">
    <tr><th style="width:60%;">Service</th><th style="width:40%;">Selected</th></tr>
    @forelse($kyc->serviceRequests as $sr)
        <tr><td>{{ $sr->serviceType?->service_name ?? 'Service' }}</td><td><span class="check-yes">&#10003; Yes</span></td></tr>
    @empty
        <tr><td colspan="2">None selected</td></tr>
    @endforelse
</table>

<div class="section-heading">Identity Document <span class="np">/ परिचयपत्र</span></div>
<table class="data-table">
    <tr><td class="label">ID Type</td><td class="value">{{ $idTypeLabel }}</td></tr>
</table>
<table class="photos-table">
    <tr>
        <td>
            <div class="photo-caption">Passport-Size Photo</div>
            @if($photoData)
                <div class="photo-frame"><img src="{{ $photoData }}"></div>
            @else
                <div class="no-photo">No photo provided</div>
            @endif
        </td>
        <td>
            <div class="photo-caption">Identity Document</div>
            @if($docData)
                <div class="photo-frame"><img src="{{ $docData }}"></div>
            @else
                <div class="no-photo">No document provided</div>
            @endif
        </td>
    </tr>
</table>

<div class="section-heading">8. Document Submission Checklist <span class="np">/ कागजात चेकलिस्ट</span></div>
<table class="checklist-table">
    <tr>
        <th style="width:60%;">Document</th>
        <th style="width:40%;">Status</th>
    </tr>
    @forelse($kyc->documents as $doc)
        <tr>
            <td>{{ $doc->docType?->doc_name ?? 'Document' }}</td>
            <td>
                @if($doc->status === 'submitted')
                    <span class="check-yes">&#10003; Submitted</span>
                @else
                    <span class="check-no">&#10007; Pending</span>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="2">No checklist items recorded.</td></tr>
    @endforelse
</table>

<div class="section-heading">9. Digital Registration Details <span class="np">/ डिजिटल दर्ता विवरण</span></div>
<p style="font-size:8.5pt; color:#555; margin-bottom:4px;">Completed by the verifying officer</p>
<table class="data-table">
    <tr><td class="label">Client ID</td><td class="value">{{ $kyc->digital_client_id ?? '—' }}</td></tr>
    <tr><td class="label">Registration Date</td><td class="value">{{ optional($kyc->verified_at)->format('Y-m-d') ?: '—' }}</td></tr>
    <tr><td class="label">Registered By</td><td class="value">{{ $kyc->verifiedBy?->full_name ?? '—' }}</td></tr>
    <tr><td class="label">Mobile App User ID</td><td class="value">{{ $kyc->mobile_app_user_id ?? '—' }}</td></tr>
    <tr><td class="label">Status</td><td class="value">{{ ucfirst($kyc->status) }}</td></tr>
    <tr><td class="label">Submitted At</td><td class="value">{{ optional($kyc->submitted_at)->format('Y-m-d H:i') ?: '—' }}</td></tr>
    <tr><td class="label">Approved By</td><td class="value">{{ $kyc->approvedBy?->full_name ?? '—' }}{{ $kyc->approved_at ? ' — ' . $kyc->approved_at->format('Y-m-d H:i') : '' }}</td></tr>
</table>
@if($kyc->admin_note)
<div class="note-box">
    <strong>Review Note:</strong> {{ $kyc->admin_note }}
</div>
@endif

<div class="section-heading">10. Client Declaration <span class="np">/ ग्राहक घोषणा</span></div>
<div class="declaration-box">
    I, {{ $kyc->full_name ?? 'the applicant' }}, hereby declare that the information and documents provided above are
    true and correct to the best of my knowledge, as required under Annex F of API GharJagga's client registration process.
    <span class="np">&mdash; मैले माथि उल्लेखित विवरण र कागजातहरू साँचो र सही भएको घोषणा गर्दछु।</span>
</div>

<table class="sig-table">
    <tr>
        <td>
            @if($signatureData)
                <img class="sig-photo" src="{{ $signatureData }}">
            @endif
            <div class="sig-line"></div>
            <span class="sig-label">Applicant</span>
            <div class="sig-name np">{{ $kyc->full_name ?? '—' }}</div>
            <div class="sig-name">Date: {{ optional($kyc->signature_date)->format('Y-m-d') ?: '—' }}</div>
        </td>
        <td>
            <div class="sig-line"></div>
            <span class="sig-label">Verified By</span>
            <div class="sig-name">{{ $kyc->verifiedBy?->full_name ?? '—' }}</div>
            <div class="sig-name">{{ $kyc->verifiedBy?->designation ?? '' }}</div>
            <div class="sig-name">Date: {{ optional($kyc->verified_at)->format('Y-m-d') ?: '—' }}</div>
        </td>
        <td>
            <div class="sig-line"></div>
            <span class="sig-label">Approved By</span>
            <div class="sig-name">{{ $kyc->approvedBy?->full_name ?? '—' }}</div>
            <div class="sig-name">{{ $kyc->approvedBy?->designation ?? '' }}</div>
            <div class="sig-name">Date: {{ optional($kyc->approved_at)->format('Y-m-d') ?: '—' }}</div>
        </td>
    </tr>
</table>

<div class="footer">&copy; Api Ghar Jagga | {{ $refNo }} | This is a system-generated Annex-F KYC record.</div>
</body>
</html>
