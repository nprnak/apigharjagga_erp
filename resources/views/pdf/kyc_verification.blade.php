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
        @page { size: A4 portrait; margin: 18mm 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', 'Times', serif;
            font-size: 11pt;
            color: #000;
            line-height: 1.45;
        }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .doc-ref { font-size: 7pt; color: #555; }
        .org-name { font-size: 15pt; font-weight: bold; text-align: center; }
        .org-sub { font-size: 10pt; text-align: center; color: #333; }
        .form-title { font-size: 13pt; font-weight: bold; text-align: center; text-transform: uppercase; margin: 8px 0 1px; letter-spacing: 0.5px; }
        .form-title-np { font-size: 11pt; text-align: center; color: #333; margin-bottom: 6px; }
        .top-line { border-top: 2px solid #000; border-bottom: 1px solid #000; padding: 3px 0; }

        .status-badge {
            display: inline-block;
            padding: 2px 12px;
            border: 1px solid #000;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-approved { background: #e6f4ea; }
        .status-pending  { background: #fef7e0; }
        .status-rejected { background: #fce8e6; }

        .meta-table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        .meta-table td { font-size: 10pt; padding: 3px 4px; vertical-align: top; }

        .section-heading {
            font-size: 11pt;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding: 4px 0;
            margin: 14px 0 6px;
            text-transform: uppercase;
        }
        .section-heading span { font-weight: normal; font-size: 10pt; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .data-table td { font-size: 10.5pt; padding: 4px 6px; vertical-align: top; }
        .data-table .label { font-weight: bold; width: 30%; color: #222; }
        .data-table .value { border-bottom: 1px dotted #999; }

        .photos-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .photos-table td { width: 50%; text-align: center; vertical-align: top; padding: 6px; }
        .photo-frame { border: 1px solid #000; padding: 4px; display: inline-block; }
        .photo-frame img { max-height: 190px; max-width: 100%; }
        .photo-caption { font-size: 9pt; font-weight: bold; margin-bottom: 4px; text-transform: uppercase; }
        .no-photo { border: 1px dashed #999; padding: 30px 10px; color: #777; font-size: 9pt; }

        .note-box { border: 1px solid #000; padding: 8px 10px; margin: 6px 0; font-size: 10pt; }

        .sig-table { width: 100%; border-collapse: collapse; margin-top: 26px; }
        .sig-table td { width: 50%; padding: 6px 10px; vertical-align: bottom; font-size: 9.5pt; }
        .sig-line { border-bottom: 1px solid #000; margin: 34px 0 3px; }
        .sig-label { font-weight: bold; text-transform: uppercase; }

        .footer { margin-top: 16px; padding-top: 5px; border-top: 1px solid #000; text-align: center; font-size: 7.5pt; color: #555; }
    </style>
</head>
<body>
@php
    $idTypeLabel = match ($kyc->id_type) {
        'citizenship'     => 'Citizenship Card',
        'national_id'     => 'National ID',
        'passport'        => 'Passport',
        'driving_license' => 'Driving License',
        default           => $kyc->id_type ?? '—',
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

<table class="header-table">
    <tr>
        <td style="width:50%; text-align:center;">
            <div class="org-name">API Ghar Jagga</div>
            <div class="org-sub np">अपि घर जग्गा</div>
        </td>
        <td style="width:25%; text-align:right;"><span class="doc-ref">KYC RECORD</span></td>
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

<div class="section-heading">1. Personal Information <span class="np">/ व्यक्तिगत विवरण</span></div>
<table class="data-table">
    <tr><td class="label">Full Name</td><td class="value np">{{ $kyc->full_name ?? '—' }}</td></tr>
    <tr><td class="label">Father / Mother Name</td><td class="value np">{{ $kyc->father_mother_name ?? '—' }}</td></tr>
    <tr><td class="label">Spouse Name</td><td class="value np">{{ $kyc->spouse_name ?? '—' }}</td></tr>
    <tr><td class="label">Citizenship No.</td><td class="value np">{{ $kyc->citizenship_no ?? '—' }}</td></tr>
    <tr><td class="label">Date of Birth</td><td class="value">{{ optional($kyc->date_of_birth)->format('Y-m-d') ?: '—' }}</td></tr>
    <tr><td class="label">Gender</td><td class="value">{{ $genderLabel }}</td></tr>
    <tr><td class="label">Nationality</td><td class="value np">{{ $kyc->nationality ?? '—' }}</td></tr>
    <tr><td class="label">Occupation</td><td class="value np">{{ $kyc->occupation ?? '—' }}</td></tr>
</table>

<div class="section-heading">2. Contact Details <span class="np">/ सम्पर्क विवरण</span></div>
<table class="data-table">
    <tr><td class="label">Mobile No.</td><td class="value">{{ $kyc->mobile_no ?? '—' }}</td></tr>
    <tr><td class="label">Email</td><td class="value">{{ $kyc->email ?? '—' }}</td></tr>
    <tr><td class="label">User Account</td><td class="value">{{ $kyc->user ? collect([$kyc->user->name, $kyc->user->email])->filter()->implode(' · ') : '—' }}</td></tr>
</table>

<div class="section-heading">3. Address <span class="np">/ ठेगाना</span></div>
<table class="data-table">
    <tr><td class="label">Permanent Address</td><td class="value np">{{ $permanent }}</td></tr>
    <tr><td class="label">Current Address</td><td class="value np">{{ $current }}</td></tr>
</table>

<div class="section-heading">4. Identity Document <span class="np">/ परिचयपत्र</span></div>
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

<div class="section-heading">5. Verification Status <span class="np">/ प्रमाणीकरण अवस्था</span></div>
<table class="data-table">
    <tr><td class="label">Status</td><td class="value">{{ ucfirst($kyc->status) }}</td></tr>
    <tr><td class="label">Submitted At</td><td class="value">{{ optional($kyc->submitted_at)->format('Y-m-d H:i') ?: '—' }}</td></tr>
    <tr><td class="label">Reviewed At</td><td class="value">{{ optional($kyc->reviewed_at)->format('Y-m-d H:i') ?: '—' }}</td></tr>
</table>
@if($kyc->admin_note)
<div class="note-box">
    <strong>Admin Note:</strong> {{ $kyc->admin_note }}
</div>
@endif

<table class="sig-table">
    <tr>
        <td>
            <div class="sig-line"></div>
            <span class="sig-label">Applicant Signature</span>
            <div class="np">{{ $kyc->full_name ?? '' }}</div>
        </td>
        <td>
            <div class="sig-line"></div>
            <span class="sig-label">Verified By (Admin)</span>
            <div>Date: {{ optional($kyc->reviewed_at)->format('Y-m-d') ?: '—' }}</div>
        </td>
    </tr>
</table>

<div class="footer">© Api Ghar Jagga | {{ $refNo }} | This is a system-generated KYC record.</div>
</body>
</html>
