<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Complaint - {{ $complaint->complaint_code }}</title>
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
        .desc-box { border: 1px solid #ccc; padding: 8px; font-size: 9pt; min-height: 50px; }
        .workflow { font-size: 8pt; text-align: center; margin-top: 10px; color: #333; }
    </style>
</head>
<body>
@php
    $sig = function (?string $path) {
        $full = $path ? storage_path('app/public/' . $path) : null;
        return ($full && file_exists($full)) ? $full : null;
    };
    $client = $complaint->client;
    $clientType = $client && $client->client_type === 'other'
        ? ($client->client_type_other ?: 'Other')
        : ucwords(str_replace('_', ' ', $client->client_type ?? '—'));
    $received = $complaint->received_through === 'other'
        ? ($complaint->received_through_other ?: 'Other')
        : ucwords(str_replace('_', ' ', $complaint->received_through ?? '—'));
    $category = $complaint->category === 'other'
        ? ($complaint->category_other ?: 'Other')
        : ucwords(str_replace('_', ' ', $complaint->category ?? '—'));
    $time = $complaint->complaint_time
        ? \Illuminate\Support\Carbon::parse($complaint->complaint_time)->format('H:i')
        : '—';
    $evidenceTypes = ['photo' => 'Photo', 'screenshot' => 'Screenshot', 'agreement_copy' => 'Agreement Copy', 'payment_receipt' => 'Payment Receipt', 'other' => 'Other Documents'];
    $attached = $complaint->evidence->pluck('evidence_type')->all();
@endphp

<table class="header-table">
    <tr>
        <td style="width:25%;"><span class="doc-ref">Document Code: AGJ-FRM-08<br>Version: 1.0</span></td>
        <td style="width:50%; text-align:center;">
            <div class="org-name">Api Ghar Jagga Pvt. Ltd.</div>
            <div class="org-sub np">अपि घर जग्गा प्रा. लि.</div>
        </td>
        <td style="width:25%; text-align:right;"><span class="doc-ref">ANNEX – G</span></td>
    </tr>
</table>
<div class="top-line"></div>
<div class="form-title">Customer Complaint Form</div>
<div class="form-title-np np">ग्राहक गुनासो / उजुरी फारम</div>
<table class="meta-table">
    <tr>
        <td><strong>Complaint ID:</strong> {{ $complaint->complaint_code }}</td>
        <td style="text-align:right;"><strong>Date:</strong> {{ optional($complaint->complaint_date)->format('Y-m-d') }} &nbsp; <strong>Time:</strong> {{ $time }}</td>
    </tr>
</table>
<p style="font-size:8pt; color:#333; margin-bottom:8px;">
    Project / Service: Api Ghar Jagga Property Listing, Verification, Valuation &amp; Digital Service
</p>

<div class="section-heading">1. Complaint Registration Details <span class="np">/ गुनासो दर्ता विवरण</span></div>
<table class="data-table">
    <tr><td class="label">Complaint ID</td><td class="value">{{ $complaint->complaint_code }}</td></tr>
    <tr><td class="label">Date of Complaint</td><td class="value">{{ optional($complaint->complaint_date)->format('Y-m-d') }}</td></tr>
    <tr><td class="label">Time</td><td class="value">{{ $time }}</td></tr>
    <tr><td class="label">Received Through</td><td class="value">{{ $received }}</td></tr>
    <tr><td class="label">Received By</td><td class="value">{{ $complaint->received_by_name ?? '—' }} {{ $complaint->received_by_designation ? '(' . $complaint->received_by_designation . ')' : '' }}</td></tr>
</table>

<div class="section-heading">2. Customer Information <span class="np">/ ग्राहक विवरण</span></div>
<table class="data-table">
    <tr><td class="label">Full Name</td><td class="value">{{ $client->full_name ?? '—' }}</td></tr>
    <tr><td class="label">Client ID</td><td class="value">{{ $client->client_code ?? '—' }}</td></tr>
    <tr><td class="label">Mobile No.</td><td class="value">{{ $client->mobile_no ?? '—' }}</td></tr>
    <tr><td class="label">Email Address</td><td class="value">{{ $client->email ?? '—' }}</td></tr>
    <tr><td class="label">Address</td><td class="value">{{ $client->permanentAddress->full_address_text ?? '—' }}</td></tr>
    <tr><td class="label">Customer Type</td><td class="value">{{ $clientType }}</td></tr>
</table>

<div class="section-heading">3. Property Related Details <span class="np">/ सम्पत्ति सम्बन्धी विवरण</span></div>
<table class="data-table">
    <tr><td class="label">Property ID</td><td class="value">{{ $complaint->property->property_code ?? '—' }}</td></tr>
    <tr><td class="label">Property Location</td><td class="value">{{ $complaint->property_location ?? '—' }}</td></tr>
    <tr><td class="label">Kitta No.</td><td class="value">{{ $complaint->kitta_no ?? '—' }}</td></tr>
    <tr><td class="label">Service Taken</td><td class="value">{{ $complaint->service_reference ?? '—' }}</td></tr>
    <tr><td class="label">Date of Service</td><td class="value">{{ optional($complaint->service_date)->format('Y-m-d') ?: '—' }}</td></tr>
</table>

<div class="section-heading">4. Complaint Category <span class="np">/ गुनासोको प्रकार</span></div>
<table class="data-table">
    <tr><td class="label">Category</td><td class="value">{{ $category }}</td></tr>
</table>

<div class="section-heading">5. Complaint Description <span class="np">/ गुनासोको विवरण</span></div>
<div class="desc-box">{{ $complaint->description }}</div>

<div class="page-break"></div>

<div class="section-heading">6. Supporting Documents / Evidence <span class="np">/ प्रमाण</span></div>
<table class="bordered-table">
    <tr><th>Document</th><th>Attached</th></tr>
    @foreach($evidenceTypes as $key => $label)
        <tr>
            <td>{{ $label }}</td>
            <td>{{ in_array($key, $attached, true) ? 'Yes' : 'No' }}</td>
        </tr>
    @endforeach
</table>

<div class="section-heading">7. Complaint Priority Level <span class="np">/ प्राथमिकता</span></div>
<table class="data-table">
    <tr><td class="label">Priority</td><td class="value">{{ ucfirst($complaint->priority) }}</td></tr>
</table>

<div class="section-heading">8. Internal Investigation &amp; Action <span class="np">/ आन्तरिक अनुसन्धान</span></div>
<table class="data-table">
    <tr><td class="label">Assigned Department</td><td class="value">{{ $complaint->assigned_department ?? '—' }}</td></tr>
    <tr><td class="label">Assigned Officer</td><td class="value">{{ $complaint->assigned_officer_name ?? '—' }}</td></tr>
    <tr><td class="label">Investigation Date</td><td class="value">{{ optional($complaint->investigation_date)->format('Y-m-d') ?: '—' }}</td></tr>
    <tr><td class="label">Findings</td><td class="value">{{ $complaint->findings ?? '—' }}</td></tr>
    <tr><td class="label">Corrective Action Taken</td><td class="value">{{ $complaint->corrective_action_taken ?? '—' }}</td></tr>
    <tr><td class="label">Resolution Date</td><td class="value">{{ optional($complaint->resolution_date)->format('Y-m-d') ?: '—' }}</td></tr>
</table>

<div class="section-heading">9. Complaint Status <span class="np">/ गुनासो स्थिति</span></div>
<table class="data-table">
    <tr><td class="label">Status</td><td class="value">{{ ucwords(str_replace('_', ' ', $complaint->status)) }}</td></tr>
</table>

<div class="section-heading">10. Customer Feedback After Resolution <span class="np">/ समाधानपछिको प्रतिक्रिया</span></div>
<table class="data-table">
    <tr><td class="label">Satisfaction Level</td><td class="value">{{ $complaint->satisfaction_level ? ucwords(str_replace('_', ' ', $complaint->satisfaction_level)) : '—' }}</td></tr>
    <tr><td class="label">Customer Remarks</td><td class="value">{{ $complaint->customer_remarks ?? '—' }}</td></tr>
</table>

<div class="section-heading">11. Declaration <span class="np">/ घोषणा</span></div>
<div class="declaration-box">
    I confirm that the complaint information provided above is accurate and authorize Api Ghar Jagga Pvt. Ltd. to investigate and resolve the issue according to company policies and procedures.
    <p class="np" style="font-size:8pt; margin-top:6px;">म घोषणा गर्दछु कि माथि उल्लिखित गुनासो विवरण सही छ र Api Ghar Jagga Pvt. Ltd. लाई कम्पनीको नीति तथा प्रक्रियाअनुसार अनुसन्धान तथा समाधान गर्न अनुमति दिन्छु।</p>
</div>

<table class="sig-table">
    <tr>
        <td>
            <span class="sig-label">Customer Signature</span>
            @if($img = $sig($complaint->customer_signature_path))
                <img src="{{ $img }}" style="height:40px; max-width:140px;">
            @else
                <div class="sig-line"></div>
            @endif
            <div>Name: {{ $complaint->customer_signature_name ?: ($client->full_name ?? '—') }}</div>
            <div>Date: {{ optional($complaint->customer_signature_date)->format('Y-m-d') ?: '—' }}</div>
        </td>
        <td>
            <span class="sig-label">Received By</span>
            @if($img = $sig($complaint->received_by_signature_path))
                <img src="{{ $img }}" style="height:40px; max-width:140px;">
            @else
                <div class="sig-line"></div>
            @endif
            <div>Name: {{ $complaint->received_by_name ?? '—' }}</div>
            <div>Designation: {{ $complaint->received_by_designation ?? '—' }}</div>
            <div>Date: {{ optional($complaint->received_by_date)->format('Y-m-d') ?: '—' }}</div>
        </td>
        <td>
            <span class="sig-label">Reviewed &amp; Approved By</span>
            @if($img = $sig($complaint->reviewed_by_signature_path))
                <img src="{{ $img }}" style="height:40px; max-width:140px;">
            @else
                <div class="sig-line"></div>
            @endif
            <div>Name: {{ $complaint->reviewed_by_name ?? '—' }}</div>
            <div>Designation: {{ $complaint->reviewed_by_designation ?? '—' }}</div>
            <div>Date: {{ optional($complaint->reviewed_by_date)->format('Y-m-d') ?: '—' }}</div>
        </td>
    </tr>
</table>

<p class="workflow">
    Complaint Management Workflow:<br>
    Submission → Registration (AGJ-MIS) → Assignment → Investigation → Corrective Action → Customer Feedback → Closure &amp; Archive
</p>
<div class="footer">© Api Ghar Jagga Pvt. Ltd. | AGJ-FRM-08 | Generated: {{ now()->format('Y-m-d H:i') }}</div>
</body>
</html>
