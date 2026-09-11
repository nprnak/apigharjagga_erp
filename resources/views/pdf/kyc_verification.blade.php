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
            color: #18242E;
            line-height: 1.4;
        }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .doc-ref { font-size: 7pt; color: #5C6B76; }
        .org-name { font-size: 16pt; font-weight: bold; text-align: center; letter-spacing: 0.5px; }
        .org-sub { font-size: 10pt; text-align: center; color: #5C6B76; }
        .form-title { font-size: 13pt; font-weight: bold; text-align: center; margin: 8px 0 1px; letter-spacing: 0.5px; }
        .form-title-np { font-size: 11pt; text-align: center; color: #5C6B76; margin-bottom: 6px; }
        .top-line { border-top: 2.5px solid #125E4A; border-bottom: 1px solid #000; padding: 3px 0; }

        .strip-table { width: 100%; border-collapse: collapse; margin: 10px 0; border: 1px solid #C3CBD1; }
        .strip-table td { font-size: 9pt; padding: 5px 8px; border-right: 1px solid #E2E7EA; }
        .strip-table td:last-child { border-right: 0; }
        .strip-table .strip-label { display: block; font-size: 7pt; color: #5C6B76; }
        .strip-table .strip-value { font-weight: bold; font-size: 10pt; }

        .section-heading {
            font-size: 11pt;
            font-weight: bold;
            border-bottom: 1.25px solid #18242E;
            padding: 4px 0;
            margin: 12px 0 6px;
        }
        .section-heading .num {
            display: inline-block;
            width: 15px;
            height: 15px;
            line-height: 15px;
            text-align: center;
            background: #18242E;
            color: #fff;
            font-size: 8pt;
            border-radius: 2px;
            margin-right: 4px;
        }
        .section-heading span.np { font-weight: normal; font-size: 10pt; color: #5C6B76; }
        .section-heading .scope { float: right; font-weight: normal; font-size: 8pt; font-style: italic; color: #5C6B76; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .data-table td { font-size: 10pt; padding: 4px 6px; vertical-align: top; }
        .data-table .label { font-weight: bold; width: 32%; color: #333; }
        .data-table .value { border-bottom: 1px dotted #999; }

        .pill { display: inline-block; border: 1px solid #C3CBD1; border-radius: 9px; padding: 2px 9px; font-size: 8.5pt; color: #93A1A9; margin: 0 4px 4px 0; }
        .pill.on { color: #125E4A; font-weight: bold; border-color: #9BC4B4; background: #EEF4F0; }

        .photos-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .photos-table td { width: 50%; text-align: center; vertical-align: top; padding: 6px; }
        .photo-frame { border: 1px solid #000; padding: 4px; display: inline-block; }
        .photo-frame img { max-height: 180px; max-width: 100%; }
        .photo-caption { font-size: 9pt; font-weight: bold; margin-bottom: 4px; text-transform: uppercase; }
        .no-photo { border: 1px dashed #999; padding: 28px 10px; color: #777; font-size: 9pt; }

        .checklist-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; border: 1px solid #999; }
        .checklist-table th { background: #f3f4f6; font-size: 9pt; text-align: left; padding: 4px 6px; border: 1px solid #999; }
        .checklist-table td { font-size: 9.5pt; padding: 4px 6px; border: 1px solid #999; }
        .check-yes { color: #125E4A; font-weight: bold; }
        .check-no { color: #92400e; }

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
            border: 2.5px double;
            padding: 5px 14px 6px;
            text-align: center;
            transform: rotate(-8deg);
        }
        .stamp.stamp-approved { border-color: #125E4A; color: #125E4A; }
        .stamp.stamp-verified { border-color: #075985; color: #075985; }
        .stamp.stamp-pending { border-color: #92400e; color: #92400e; }
        .stamp .s1 { font-size: 12pt; font-weight: bold; letter-spacing: 2px; }
        .stamp .s2 { font-size: 9pt; font-weight: bold; margin-top: -2px; }
        .stamp .s3 { font-size: 7pt; border-top: 0.75px solid; margin-top: 3px; padding-top: 2px; letter-spacing: 0.5px; }
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
    $refNo = $kyc->digital_client_id ?? ('AGJ-KYC-' . str_pad((string) $kyc->id, 5, '0', STR_PAD_LEFT));

    // Mirrors the in-app summary page's $statusMeta so both surfaces agree
    // on when a seal/stamp appears and what it says.
    $stampMeta = [
        'approved' => ['label' => 'APPROVED', 'np' => 'स्वीकृत', 'class' => 'stamp-approved'],
        'verified' => ['label' => 'VERIFIED', 'np' => 'प्रमाणित', 'class' => 'stamp-verified'],
        'pending' => ['label' => 'SUBMITTED', 'np' => 'पेश गरिएको', 'class' => 'stamp-pending'],
    ];
    $stamp = $stampMeta[$kyc->status] ?? null;
    $stampDate = match ($kyc->status) {
        'approved' => $kyc->approved_at,
        'verified' => $kyc->verified_at,
        default => $kyc->submitted_at,
    };
@endphp

@if($stamp)
<div class="stamp {{ $stamp['class'] }}">
    <div class="s1">{{ $stamp['label'] }}</div>
    <div class="s2 np">{{ $stamp['np'] }}</div>
    <div class="s3">{{ $refNo }} &middot; {{ optional($stampDate)->format('Y-m-d') }}</div>
</div>
@endif

<table class="header-table">
    <tr>
        <td style="width:75%; text-align:center;">
            <div class="org-name">API Ghar Jagga Pvt. Ltd.</div>
            <div class="org-sub np">अपि घर जग्गा प्रा. लि.</div>
        </td>
        <td style="width:25%; text-align:right; vertical-align:bottom;"><span class="doc-ref">AGJ-FRM-07<br>ANNEX &ndash; F</span></td>
    </tr>
</table>
<div class="top-line"></div>
<div class="form-title">Client KYC Verification Record</div>
<div class="form-title-np np">ग्राहक पहिचान (के.वाई.सी.) प्रमाणीकरण अभिलेख</div>

<table class="strip-table">
    <tr>
        <td><span class="strip-label">Client ID</span><span class="strip-value">{{ $kyc->digital_client_id ?? 'Pending' }}</span></td>
        <td><span class="strip-label">Submitted</span><span class="strip-value">{{ optional($kyc->submitted_at)->format('Y-m-d') ?: '—' }}</span></td>
        <td><span class="strip-label">Verified</span><span class="strip-value">{{ optional($kyc->verified_at)->format('Y-m-d') ?: '—' }}</span></td>
        <td><span class="strip-label">Approved</span><span class="strip-value">{{ optional($kyc->approved_at)->format('Y-m-d') ?: '—' }}</span></td>
    </tr>
</table>

<div class="section-heading"><span class="num">1</span>Client Type <span class="np">/ ग्राहकको प्रकार</span></div>
<div>
    @foreach(\App\Models\User::clientTypeOptions() as $ctVal => $ctLabel)
        <span class="pill {{ $kyc->user?->client_type === $ctVal ? 'on' : '' }}">{{ $ctLabel }}</span>
    @endforeach
</div>

<div class="section-heading"><span class="num">2</span>Personal Information <span class="np">/ व्यक्तिगत विवरण</span></div>
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

<div class="section-heading"><span class="num">3</span>Contact Details <span class="np">/ सम्पर्क विवरण</span></div>
<table class="data-table">
    <tr><td class="label">Mobile No.</td><td class="value">{{ $kyc->mobile_no ?? '—' }}</td></tr>
    <tr><td class="label">Alternate Contact No.</td><td class="value">{{ $kyc->alt_contact_no ?? '—' }}</td></tr>
    <tr><td class="label">Telephone No.</td><td class="value">{{ $kyc->telephone_no ?? '—' }}</td></tr>
    <tr><td class="label">Email</td><td class="value">{{ $kyc->email ?? '—' }}</td></tr>
    <tr><td class="label">Permanent Address</td><td class="value np">{{ $permanent }}</td></tr>
    <tr><td class="label">Current Address</td><td class="value np">{{ $current }}</td></tr>
</table>

<div class="section-heading"><span class="num">4</span>Organization Details <span class="np">/ संस्था सम्बन्धी विवरण</span><span class="scope">If applicable</span></div>
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

<div class="section-heading"><span class="num">5</span>Property Requirement Details <span class="np">/ सम्पत्ति आवश्यकता</span><span class="scope">For Buyer / Investor / Tenant</span></div>
@if($kyc->propertyRequirement)
    <div style="margin-bottom:4px;">
        <span style="font-size:8.5pt; color:#5C6B76;">Purpose:</span>
        @foreach(['purchase' => 'Purchase', 'investment' => 'Investment', 'rent' => 'Rent'] as $val => $label)
            <span class="pill {{ in_array($val, (array) $kyc->propertyRequirement->purpose, true) ? 'on' : '' }}">{{ $label }}</span>
        @endforeach
    </div>
    <div style="margin-bottom:6px;">
        <span style="font-size:8.5pt; color:#5C6B76;">Property Type:</span>
        @foreach(['land' => 'Land', 'house' => 'House', 'apartment' => 'Apartment', 'commercial' => 'Commercial', 'other' => 'Other'] as $val => $label)
            <span class="pill {{ in_array($val, (array) $kyc->propertyRequirement->property_type, true) ? 'on' : '' }}">{{ $label }}</span>
        @endforeach
    </div>
<table class="data-table">
    <tr><td class="label">Preferred Location</td><td class="value np">{{ $kyc->propertyRequirement->preferred_location ?? '—' }}</td></tr>
    <tr><td class="label">Required Area</td><td class="value">{{ $kyc->propertyRequirement->required_area ?? '—' }}</td></tr>
    <tr><td class="label">Estimated Budget</td><td class="value">{{ $kyc->propertyRequirement->estimated_budget ? 'Rs. '.number_format((float) $kyc->propertyRequirement->estimated_budget, 2) : '—' }}</td></tr>
    <tr><td class="label">Purchase Timeline</td><td class="value">{{ $kyc->propertyRequirement->purchase_timeline ?? '—' }}</td></tr>
</table>
@else
<p style="font-size:9.5pt; color:#555;">Not provided.</p>
@endif

<div class="page-break" style="page-break-before: always;"></div>

<div class="section-heading"><span class="num">7</span>Required Service Selection <span class="np">/ आवश्यक सेवा छनोट</span></div>
@php $selectedServiceIds = $kyc->serviceRequests->pluck('service_type_id')->all(); @endphp
<div>
    @foreach(\App\Models\ServiceType::where('is_active', true)->get() as $svc)
        <span class="pill {{ in_array($svc->service_type_id, $selectedServiceIds, true) ? 'on' : '' }}">{{ $svc->service_name }}</span>
    @endforeach
</div>

<div class="section-heading">Photograph &amp; Identity Document <span class="np">/ फोटो र परिचयपत्र</span></div>
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

<div class="section-heading"><span class="num">8</span>Document Submission Checklist <span class="np">/ कागजात चेकलिस्ट</span></div>
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

<div class="section-heading"><span class="num">9</span>Digital Registration Details <span class="np">/ डिजिटल दर्ता विवरण</span><span class="scope">Completed by the verifying officer</span></div>
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

<div class="section-heading"><span class="num">10</span>Client Declaration <span class="np">/ ग्राहक घोषणा</span></div>
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
