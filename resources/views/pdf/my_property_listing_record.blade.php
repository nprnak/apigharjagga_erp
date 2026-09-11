<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Property Listing - {{ $property->property_code }}</title>
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
        body { font-family: 'Times New Roman', 'Times', serif; font-size: 10.5pt; color: #18242E; line-height: 1.4; }
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

        .section-heading { font-size: 11pt; font-weight: bold; border-bottom: 1.25px solid #18242E; padding: 4px 0; margin: 12px 0 6px; }
        .section-heading .num { display: inline-block; width: 15px; height: 15px; line-height: 15px; text-align: center; background: #18242E; color: #fff; font-size: 8pt; border-radius: 2px; margin-right: 4px; }
        .section-heading span.np { font-weight: normal; font-size: 10pt; color: #5C6B76; }
        .section-heading .scope { float: right; font-weight: normal; font-size: 8pt; font-style: italic; color: #5C6B76; }
        .subheading { font-size: 9pt; font-weight: bold; text-transform: uppercase; color: #5C6B76; margin: 8px 0 3px; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .data-table td { font-size: 10pt; padding: 4px 6px; vertical-align: top; }
        .data-table .label { font-weight: bold; width: 34%; color: #333; }
        .data-table .value { border-bottom: 1px dotted #999; }

        .pill { display: inline-block; border: 1px solid #C3CBD1; border-radius: 9px; padding: 2px 9px; font-size: 8.5pt; color: #93A1A9; margin: 0 4px 4px 0; }
        .pill.on { color: #125E4A; font-weight: bold; border-color: #9BC4B4; background: #EEF4F0; }

        .checklist-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; border: 1px solid #999; }
        .checklist-table th { background: #f3f4f6; font-size: 9pt; text-align: left; padding: 4px 6px; border: 1px solid #999; }
        .checklist-table td { font-size: 9.5pt; padding: 4px 6px; border: 1px solid #999; }
        .check-yes { color: #125E4A; font-weight: bold; }
        .check-no { color: #92400e; }

        .photos-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .photos-table td { width: 25%; text-align: center; vertical-align: top; padding: 4px; }
        .photo-frame { border: 1px solid #000; padding: 3px; display: inline-block; }
        .photo-frame img { max-height: 110px; max-width: 100%; }

        .note-box { border: 1px solid #000; padding: 8px 10px; margin: 6px 0; font-size: 10pt; }
        .declaration-box { border: 1px solid #999; background: #fafafa; padding: 8px 10px; margin: 10px 0; font-size: 9pt; font-style: italic; }

        .sig-table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        .sig-table td { width: 50%; padding: 6px 8px; vertical-align: bottom; font-size: 9pt; }
        .sig-photo { max-height: 45px; margin-bottom: 3px; }
        .sig-line { border-bottom: 1px solid #000; margin: 30px 0 3px; }
        .sig-label { font-weight: bold; text-transform: uppercase; display: block; }
        .sig-name { font-size: 9pt; margin-top: 2px; }

        .footer { margin-top: 14px; padding-top: 5px; border-top: 1px solid #000; text-align: center; font-size: 7.5pt; color: #555; }

        .stamp { position: absolute; top: 20mm; right: 18mm; border: 2.5px double; padding: 5px 14px 6px; text-align: center; transform: rotate(-8deg); }
        .stamp.stamp-approved { border-color: #125E4A; color: #125E4A; }
        .stamp.stamp-pending { border-color: #075985; color: #075985; }
        .stamp .s1 { font-size: 12pt; font-weight: bold; letter-spacing: 2px; }
        .stamp .s2 { font-size: 9pt; font-weight: bold; margin-top: -2px; }
        .stamp .s3 { font-size: 7pt; border-top: 0.75px solid; margin-top: 3px; padding-top: 2px; letter-spacing: 0.5px; }
    </style>
</head>
<body>
@php
    $listing = $property->listings->sortByDesc('listing_id')->first();
    $kyc = $property->user?->kycVerification;

    $propertyTypeOptions = ['land' => 'Land', 'house' => 'House', 'apartment' => 'Apartment', 'commercial_building' => 'Commercial Building', 'office_space' => 'Office Space', 'industrial_property' => 'Industrial Property', 'agricultural_land' => 'Agricultural Land', 'other' => 'Other'];
    $ownershipRoleOptions = ['self' => 'Sole Owner (Self)', 'family_member' => 'Family Member', 'authorized_representative' => 'Authorized Representative', 'company' => 'Company'];
    $purposeOptions = ['sale' => 'For Sale', 'rent' => 'For Rent', 'lease' => 'Long-Term Lease', 'exchange' => 'Exchange', 'investment' => 'Joint Investment', 'other' => 'Other'];

    $stampMeta = [
        'approved' => ['label' => 'APPROVED', 'np' => 'स्वीकृत', 'class' => 'stamp-approved'],
        'pending' => ['label' => 'SUBMITTED', 'np' => 'पेश गरिएको', 'class' => 'stamp-pending'],
    ];
    $stamp = $stampMeta[$property->approval_status] ?? null;
@endphp

@if($stamp)
<div class="stamp {{ $stamp['class'] }}">
    <div class="s1">{{ $stamp['label'] }}</div>
    <div class="s2 np">{{ $stamp['np'] }}</div>
    <div class="s3">{{ $property->property_code }} &middot; {{ optional($property->updated_at)->format('Y-m-d') }}</div>
</div>
@endif

<table class="header-table">
    <tr>
        <td style="width:75%; text-align:center;">
            <div class="org-name">API Ghar Jagga Pvt. Ltd.</div>
            <div class="org-sub np">अपि घर जग्गा प्रा. लि.</div>
        </td>
        <td style="width:25%; text-align:right; vertical-align:bottom;"><span class="doc-ref">AGJ-FRM-001<br>ANNEX &ndash; A</span></td>
    </tr>
</table>
<div class="top-line"></div>
<div class="form-title">Property Listing Application Record</div>
<div class="form-title-np np">सम्पत्ति सूचीकरण आवेदन अभिलेख</div>

<table class="strip-table">
    <tr>
        <td><span class="strip-label">Property Code</span><span class="strip-value">{{ $property->property_code }}</span></td>
        <td><span class="strip-label">Application No.</span><span class="strip-value">{{ $listing?->application_no ?? '—' }}</span></td>
        <td><span class="strip-label">Submitted</span><span class="strip-value">{{ optional($property->created_at)->format('Y-m-d') ?: '—' }}</span></td>
        <td><span class="strip-label">Marketplace Status</span><span class="strip-value">{{ ucwords(str_replace('_', ' ', $property->status)) }}</span></td>
    </tr>
</table>

<div class="section-heading"><span class="num">1</span>Applicant Details <span class="np">/ आवेदक विवरण</span><span class="scope">From approved KYC</span></div>
<table class="data-table">
    <tr><td class="label">Full Name</td><td class="value np">{{ $kyc?->full_name ?? '—' }}</td></tr>
    <tr><td class="label">Citizenship No.</td><td class="value">{{ $kyc?->citizenship_no ?? '—' }}</td></tr>
    <tr><td class="label">Mobile No.</td><td class="value">{{ $kyc?->mobile_no ?? '—' }}</td></tr>
    <tr><td class="label">Email</td><td class="value">{{ $kyc?->email ?? '—' }}</td></tr>
</table>

<div class="section-heading"><span class="num">2</span>Property Owner Details <span class="np">/ धनी विवरण</span></div>
<div>
    @foreach($ownershipRoleOptions as $val => $label)
        <span class="pill {{ $property->ownership_role === $val ? 'on' : '' }}">{{ $label }}</span>
    @endforeach
</div>
@if($property->ownership_role !== 'self')
<table class="data-table">
    <tr><td class="label">Owner Full Name</td><td class="value np">{{ $property->owner_full_name ?? '—' }}</td></tr>
    <tr><td class="label">Owner Citizenship No.</td><td class="value">{{ $property->owner_citizenship_no ?? '—' }}</td></tr>
    <tr><td class="label">Relationship / Authority</td><td class="value np">{{ $property->owner_relation ?? '—' }}</td></tr>
</table>
@endif

<div class="section-heading"><span class="num">3</span>Property Details <span class="np">/ सम्पत्ति विवरण</span></div>
<div style="margin-bottom:4px;">
    @foreach($propertyTypeOptions as $val => $label)
        <span class="pill {{ $property->property_type === $val ? 'on' : '' }}">{{ $label }}</span>
    @endforeach
</div>

<div class="subheading">Address of Property / सम्पत्तिको ठेगाना</div>
@php
    $addressLine = collect([$property->address?->tole_locality, $property->address?->ward_no ? 'Ward '.$property->address->ward_no : null, $property->address?->municipality, $property->address?->district, $property->address?->province])->filter()->implode(', ') ?: '—';
@endphp
<table class="data-table"><tr><td class="label">Full Address</td><td class="value np">{{ $addressLine }}</td></tr></table>

<div class="subheading">Land Information / जग्गा विवरण</div>
<table class="data-table">
    <tr><td class="label">Kitta No.</td><td class="value">{{ $property->kitta_no ?? '—' }}</td></tr>
    <tr><td class="label">Land Area</td><td class="value">{{ $property->area ?? '—' }}</td></tr>
    <tr><td class="label">Map Sheet No.</td><td class="value">{{ $property->map_sheet_no ?? '—' }}</td></tr>
    <tr><td class="label">Ownership Type</td><td class="value">{{ $property->ownership_type ? ucfirst($property->ownership_type) : '—' }}</td></tr>
    <tr><td class="label">Ownership Certificate No.</td><td class="value">{{ $property->ownership_certificate_no ?? '—' }}</td></tr>
    <tr><td class="label">Road Access</td><td class="value">{{ $property->road_access ?? '—' }}</td></tr>
    <tr><td class="label">Road Width</td><td class="value">{{ $property->road_width ?? '—' }}</td></tr>
    <tr><td class="label">Facing Direction</td><td class="value">{{ $property->facing_direction ?? '—' }}</td></tr>
</table>

@if($property->isBuildingType())
<div class="subheading">Building Details / घर विवरण</div>
<table class="data-table">
    <tr><td class="label">Year Built</td><td class="value">{{ $property->year_of_construction ?? '—' }}</td></tr>
    <tr><td class="label">No. of Floors</td><td class="value">{{ $property->no_of_floors ?? '—' }}</td></tr>
    <tr><td class="label">Covered Area</td><td class="value">{{ $property->covered_area ?? '—' }}</td></tr>
    <tr><td class="label">Structure System</td><td class="value">{{ $property->structure_type ?? '—' }}</td></tr>
    <tr><td class="label">Roof Type</td><td class="value">{{ $property->roof_type ?? '—' }}</td></tr>
    <tr><td class="label">Parking</td><td class="value">{{ $property->parking ?? '—' }}</td></tr>
    <tr><td class="label">Water Supply</td><td class="value">{{ $property->water_supply ?? '—' }}</td></tr>
    <tr><td class="label">Electricity</td><td class="value">{{ $property->electricity ?? '—' }}</td></tr>
    <tr><td class="label">Internet</td><td class="value">{{ $property->internet ?? '—' }}</td></tr>
    <tr><td class="label">Drainage</td><td class="value">{{ $property->drainage ?? '—' }}</td></tr>
    <tr><td class="label">Building Permit No.</td><td class="value">{{ $property->building_permit_no ?? '—' }}</td></tr>
    <tr><td class="label">Current Condition</td><td class="value">{{ $property->current_building_condition ? ucfirst($property->current_building_condition) : '—' }}</td></tr>
</table>
@endif

<div class="page-break" style="page-break-before: always;"></div>

<div class="section-heading"><span class="num">4</span>Purpose of Listing <span class="np">/ उद्देश्य</span></div>
<div>
    @foreach($purposeOptions as $val => $label)
        <span class="pill {{ $listing?->purpose_of_listing === $val ? 'on' : '' }}">{{ $label }}</span>
    @endforeach
</div>

<div class="section-heading"><span class="num">5</span>Expected Price <span class="np">/ अपेक्षित मूल्य</span></div>
<table class="data-table">
    <tr><td class="label">Expected Selling Price</td><td class="value">{{ $listing?->expected_selling_price ? 'Rs. '.number_format((float) $listing->expected_selling_price, 2) : '—' }}</td></tr>
    <tr><td class="label">Negotiable</td><td class="value">{{ $listing ? ($listing->negotiable ? 'Yes' : 'No') : '—' }}</td></tr>
    <tr><td class="label">Minimum Acceptable Price</td><td class="value">{{ $listing?->minimum_acceptable_price ? 'Rs. '.number_format((float) $listing->minimum_acceptable_price, 2) : '—' }}</td></tr>
    <tr><td class="label">Monthly Rent</td><td class="value">{{ $listing?->rental_amount ? 'Rs. '.number_format((float) $listing->rental_amount, 2) : '—' }}</td></tr>
</table>

<div class="section-heading"><span class="num">6</span>Property Documents Submitted <span class="np">/ कागजात</span><span class="scope">Excludes documents already verified with KYC</span></div>
<table class="checklist-table">
    <tr><th style="width:60%;">Document</th><th style="width:40%;">Status</th></tr>
    @forelse($property->documents as $doc)
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
        <tr><td colspan="2">No documents recorded.</td></tr>
    @endforelse
</table>

<div class="section-heading"><span class="num">7</span>Property Features <span class="np">/ विशेषताहरू</span></div>
@php $selectedFeatureIds = $property->features->pluck('feature_id')->all(); @endphp
<div>
    @foreach(\App\Models\PropertyFeatureType::orderBy('feature_name')->get() as $feature)
        <span class="pill {{ in_array($feature->feature_id, $selectedFeatureIds, true) ? 'on' : '' }}">{{ $feature->feature_name }}</span>
    @endforeach
</div>

<div class="section-heading">Photographs of Property <span class="np">/ तस्विरहरू</span></div>
@if($property->photos->isNotEmpty())
<table class="photos-table">
    @foreach($property->photos->take(8)->chunk(4) as $row)
        <tr>
            @foreach($row as $photo)
                <td><div class="photo-frame"><img src="{{ $photoData[$photo->photo_id] ?? '' }}"></div></td>
            @endforeach
        </tr>
    @endforeach
</table>
@else
<p style="font-size:9.5pt; color:#555;">No photographs uploaded.</p>
@endif

<div class="section-heading"><span class="num">9</span>Declaration &amp; Signature <span class="np">/ घोषणा</span></div>
<div class="declaration-box">
    I hereby declare that the information provided above about this property is true and correct to the best of my
    knowledge, as required under Annex A of API GharJagga's property listing process.
    <span class="np">&mdash; मैले माथि उल्लेखित सम्पत्ति सम्बन्धी विवरण साँचो र सही भएको घोषणा गर्दछु।</span>
</div>

<table class="sig-table">
    <tr>
        <td>
            @if($signatureData)
                <img class="sig-photo" src="{{ $signatureData }}">
            @endif
            <div class="sig-line"></div>
            <span class="sig-label">Applicant</span>
            <div class="sig-name np">{{ $kyc?->full_name ?? $property->user?->name ?? '—' }}</div>
            <div class="sig-name">Date: {{ optional($property->created_at)->format('Y-m-d') ?: '—' }}</div>
        </td>
        <td>
            <div class="sig-line"></div>
            <span class="sig-label">Assigned / Reviewing Officer</span>
            <div class="sig-name">{{ $listing?->assignedOfficer?->full_name ?? '—' }}</div>
            <div class="sig-name">Date: {{ optional($property->updated_at)->format('Y-m-d') ?: '—' }}</div>
        </td>
    </tr>
</table>

<div class="section-heading"><span class="num">10</span>Office Use Only <span class="np">/ कार्यालय प्रयोजनका लागि मात्र</span><span class="scope">Completed by the reviewing officer</span></div>
<table class="data-table">
    <tr><td class="label">Assigned / Reviewing Officer</td><td class="value">{{ $listing?->assignedOfficer?->full_name ?? '—' }}</td></tr>
    <tr><td class="label">Legal Verification Status</td><td class="value">{{ $listing?->legal_verification_status ? ucfirst($listing->legal_verification_status) : '—' }}</td></tr>
    <tr><td class="label">Listing Status</td><td class="value">{{ $listing?->listing_status ? ucfirst($listing->listing_status) : '—' }}</td></tr>
    <tr><td class="label">Reviewed On</td><td class="value">{{ optional($property->updated_at)->format('Y-m-d') ?: '—' }}</td></tr>
</table>
@if($listing?->remarks)
<div class="note-box"><strong>Remarks:</strong> {{ $listing->remarks }}</div>
@endif

<div class="footer">&copy; Api Ghar Jagga | AGJ-FRM-001 | This is a system-generated Annex-A property listing record.</div>
</body>
</html>
