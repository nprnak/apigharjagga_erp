<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Property Listing Application - {{ $listing->application_no }}</title>
    <style>
        @font-face {
            font-family: 'NotoDevanagari';
            font-style: normal;
            font-weight: normal;
            src: url('{{ str_replace("\\", "/", storage_path("fonts/NotoSansDevanagari.ttf")) }}');
        }
        .np {
            font-family: 'NotoDevanagari', 'DejaVu Sans', sans-serif;
        }
        @page {
            size: A4 portrait;
            margin: 20mm 15mm 20mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #000;
            line-height: 1.4;
        }

        /* ── Header ───────────────────────────────────── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .header-table td {
            vertical-align: middle;
            padding: 0;
        }

        .doc-ref {
            font-size: 7pt;
            color: #555;
        }

        .org-name {
            font-size: 13pt;
            font-weight: bold;
            text-align: center;
        }

        .org-sub {
            font-size: 9pt;
            text-align: center;
            color: #333;
        }

        .form-title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 8px 0 2px;
        }

        .form-title-np {
            font-size: 10pt;
            text-align: center;
            color: #333;
            margin-bottom: 8px;
        }

        .top-line {
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 0;
        }

        /* ── Meta info row ────────────────────────────── */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .meta-table td {
            font-size: 9pt;
            padding: 3px 5px;
        }

        .meta-label {
            font-weight: bold;
        }

        .meta-value {
            border-bottom: 1px dotted #000;
            min-width: 120px;
        }

        /* ── Section headings ─────────────────────────── */
        .section-heading {
            font-size: 10pt;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding: 4px 0;
            margin: 12px 0 6px;
            text-transform: uppercase;
        }

        .section-heading span {
            font-weight: normal;
            font-size: 9pt;
        }

        .sub-heading {
            font-size: 9pt;
            font-weight: bold;
            margin: 8px 0 4px;
            text-decoration: underline;
        }

        /* ── Data tables ──────────────────────────────── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .data-table td {
            padding: 4px 6px;
            font-size: 9pt;
            vertical-align: top;
        }

        .data-table .label {
            font-weight: bold;
            width: 30%;
            color: #333;
        }

        .data-table .value {
            border-bottom: 1px dotted #999;
        }

        .data-table .colon {
            width: 10px;
            text-align: center;
        }

        /* ── Bordered table (for structured data) ────── */
        .bordered-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .bordered-table th,
        .bordered-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 9pt;
            text-align: left;
        }

        .bordered-table th {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        /* ── Features checklist ───────────────────────── */
        .features-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .features-table td {
            padding: 3px 6px;
            font-size: 9pt;
            width: 33.33%;
        }

        .check-box {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            text-align: center;
            line-height: 10px;
            font-size: 8pt;
            margin-right: 4px;
            vertical-align: middle;
        }

        .check-box.checked::after {
            content: "✓";
        }

        /* ── Declaration ──────────────────────────────── */
        .declaration-box {
            border: 1px solid #000;
            padding: 8px 10px;
            margin: 6px 0;
            font-size: 9pt;
            line-height: 1.6;
        }

        /* ── Signature area ───────────────────────────── */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .sig-table td {
            width: 50%;
            padding: 5px 10px;
            vertical-align: top;
            font-size: 9pt;
        }

        .sig-label {
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            margin-bottom: 30px;
            display: block;
        }

        .sig-line {
            border-bottom: 1px solid #000;
            margin-bottom: 3px;
            margin-top: 30px;
        }

        .sig-meta {
            font-size: 8pt;
            color: #333;
        }

        /* ── Office use box ───────────────────────────── */
        .office-box {
            border: 2px solid #000;
            padding: 8px;
            margin-top: 15px;
        }

        .office-title {
            font-weight: bold;
            font-size: 10pt;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        /* ── Footer ───────────────────────────────────── */
        .footer {
            margin-top: 15px;
            padding-top: 5px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 7pt;
            color: #555;
        }

        /* ── Utilities ────────────────────────────────── */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .mt-8 {
            margin-top: 8px;
        }

        .mb-4 {
            margin-bottom: 4px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- DOCUMENT HEADER --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <table class="header-table">
        <tr>
            <td style="width: 25%;">
                <span class="doc-ref">Document Code: AGJ-FRM-001<br>Version: 1.0</span>
            </td>
            <td style="width: 50%; text-align: center;">
                <div class="org-name">Api Ghar Jagga Pvt. Ltd.</div>
                <div class="org-sub np">अपि घर जग्गा प्रा. लि.</div>
            </td>
            <td style="width: 25%; text-align: right;">
                <span class="doc-ref">ANNEX – A</span>
            </td>
        </tr>
    </table>

    <div class="top-line"></div>

    <div class="form-title">Property Listing Application Form</div>
    <div class="form-title-np np">सम्पत्ति सूचीकरण आवेदन फाराम</div>

    {{-- Application meta --}}
    <table class="meta-table">
        <tr>
            <td class="meta-label">Application No.:</td>
            <td class="meta-value">{{ $listing->application_no }}</td>
            <td class="meta-label" style="text-align: right;">Date:</td>
            <td class="meta-value">
                {{ $listing->date_received ? $listing->date_received->format('Y-m-d') : now()->format('Y-m-d') }}</td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- SECTION 1: APPLICANT DETAILS --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">1. Applicant Details <span class="np">/ आवेदकको विवरण</span></div>

    <table class="data-table">
        <tr>
            <td class="label">Full Name (English)</td>
            <td class="colon">:</td>
            <td class="value">{{ $listing->applicant->full_name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Citizenship No.</td>
            <td class="colon">:</td>
            <td class="value">{{ $listing->applicant->citizenship_no ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Father/Mother's Name</td>
            <td class="colon">:</td>
            <td class="value">{{ $listing->applicant->father_mother_name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Grandfather's Name</td>
            <td class="colon">:</td>
            <td class="value">{{ $listing->applicant->grandfather_name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Date of Birth</td>
            <td class="colon">:</td>
            <td class="value">
                {{ $listing->applicant->date_of_birth ? $listing->applicant->date_of_birth->format('Y-m-d') : '—' }}
            </td>
        </tr>
        <tr>
            <td class="label">Mobile No.</td>
            <td class="colon">:</td>
            <td class="value">{{ $listing->applicant->mobile_no ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Telephone No.</td>
            <td class="colon">:</td>
            <td class="value">{{ $listing->applicant->telephone_no ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td class="colon">:</td>
            <td class="value">{{ $listing->applicant->email ?? '—' }}</td>
        </tr>
    </table>

    {{-- Address sub-section --}}
    <div class="sub-heading">Permanent Address / <span class="np">स्थायी ठेगाना</span></div>
    <table class="data-table">
        <tr>
            <td class="label">Address</td>
            <td class="colon">:</td>
            <td class="value">{{ $listing->applicant->permanentAddress->full_address_text ?? '—' }}</td>
        </tr>
    </table>

    <div class="sub-heading">Current Address / <span class="np">हालको ठेगाना</span></div>
    <table class="data-table">
        <tr>
            <td class="label">Address</td>
            <td class="colon">:</td>
            <td class="value">{{ $listing->applicant->currentAddress->full_address_text ?? '—' }}</td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- SECTION 2: OWNERSHIP ROLE --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">2. Property Owner Details <span class="np">/ सम्पत्ति धनीको विवरण</span></div>

    <table class="data-table">
        <tr>
            <td class="label">Ownership Role</td>
            <td class="colon">:</td>
            <td class="value">{{ ucwords(str_replace('_', ' ', $listing->property->ownership_role ?? '—')) }}</td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- SECTION 3: PROPERTY DETAILS --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">3. Property Details <span class="np">/ सम्पत्तिको विवरण</span></div>

    <table class="data-table">
        <tr>
            <td class="label">Property Type</td>
            <td class="colon">:</td>
            <td class="value">{{ ucwords(str_replace('_', ' ', $listing->property->property_type ?? '—')) }}</td>
        </tr>
    </table>

    <div class="sub-heading">Address of Property / <span class="np">सम्पत्तिको ठेगाना</span></div>

    <table class="bordered-table">
        <tr>
            <th>Province</th>
            <th>District</th>
            <th>Municipality</th>
            <th>Ward No.</th>
        </tr>
        <tr>
            <td>{{ $listing->property->address->province ?? '—' }}</td>
            <td>{{ $listing->property->address->district ?? '—' }}</td>
            <td>{{ $listing->property->address->municipality ?? '—' }}</td>
            <td>{{ $listing->property->address->ward_no ?? '—' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <tr>
            <td class="label">Tole / Locality</td>
            <td class="colon">:</td>
            <td class="value">{{ $listing->property->address->tole_locality ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">GPS Location</td>
            <td class="colon">:</td>
            <td class="value">
                @if($listing->property->address && $listing->property->address->gps_lat)
                    {{ $listing->property->address->gps_lat }}, {{ $listing->property->address->gps_lng }}
                @else
                    —
                @endif
            </td>
        </tr>
    </table>

    <div class="sub-heading">Land Information / <span class="np">जग्गाको विवरण</span></div>

    <table class="bordered-table">
        <tr>
            <th>Kitta No.</th>
            <th>Area</th>
            <th>Ownership Type</th>
        </tr>
        <tr>
            <td>{{ $listing->property->kitta_no ?? '—' }}</td>
            <td>{{ $listing->property->area ?? '—' }}</td>
            <td>{{ $listing->property->ownership_type ?? '—' }}</td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- SECTION 4: PURPOSE OF LISTING --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">4. Purpose of Listing <span class="np">/ सूचीकरणको उद्देश्य</span></div>

    <table class="data-table">
        <tr>
            <td class="label">Purpose</td>
            <td class="colon">:</td>
            <td class="value">{{ ucfirst($listing->purpose_of_listing) }}</td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- SECTION 5: EXPECTED PRICE --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">5. Expected Price <span class="np">/ अपेक्षित मूल्य</span></div>

    <table class="bordered-table">
        <tr>
            <th>Description</th>
            <th>Amount (NPR)</th>
        </tr>
        <tr>
            <td>Expected Selling Price</td>
            <td>{{ $listing->expected_selling_price ? 'Rs. ' . number_format($listing->expected_selling_price, 2) : '—' }}
            </td>
        </tr>
        <tr>
            <td>Minimum Acceptable Price</td>
            <td>{{ $listing->minimum_acceptable_price ? 'Rs. ' . number_format($listing->minimum_acceptable_price, 2) : '—' }}
            </td>
        </tr>
        <tr>
            <td>Rental Amount (per month)</td>
            <td>{{ $listing->rental_amount ? 'Rs. ' . number_format($listing->rental_amount, 2) . ' /month' : '—' }}
            </td>
        </tr>
        <tr>
            <td>Negotiable</td>
            <td>{{ $listing->negotiable ? 'Yes' : 'No' }}</td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- SECTION 6: DECLARATION --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">6. Owner's Declaration <span class="np">/ सम्पत्ति धनीको घोषणा</span></div>

    <div class="declaration-box">
        <p style="margin-bottom: 6px;">
            I hereby declare that the information provided in this application is true and correct to the best of my
            knowledge.
            I confirm that I am the lawful owner or authorized representative of the property and authorize
            <strong>Api Ghar Jagga Pvt. Ltd.</strong> to inspect, market, advertise, and facilitate the sale, rental,
            lease,
            or transfer of the property in accordance with the agreed terms and applicable laws of Nepal.
        </p>
        <p class="np" style="font-size: 8pt; color: #333; font-style: italic;">
            म यस आवेदनमा उल्लेख गरिएका सम्पूर्ण विवरणहरू सत्य तथा सही रहेको घोषणा गर्दछु। म सम्पत्तिको कानुनी धनी
            वा अधिकृत प्रतिनिधि भएको पुष्टि गर्दछु र अपि घर जग्गा प्रा. लि. लाई सम्पत्ति निरीक्षण, बजार, विज्ञापन
            तथा बिक्री, भाडा, लिज वा हस्तान्तरणमा सहजीकरण गर्न अख्तियारी दिन्छु।
        </p>
    </div>

    <p style="font-size: 9pt; margin-top: 6px;">
        <strong>☑</strong> Applicant has agreed to the above declaration.
    </p>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- SECTION 7: SIGNATURES --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">7. Signatures <span class="np">/ हस्ताक्षर</span></div>

    <table class="sig-table">
        <tr>
            <td>
                <span class="sig-label">Applicant / Property Owner</span>
                @php
                    $applicantSig = $listing->applicant_signature_path
                        ? storage_path('app/public/' . $listing->applicant_signature_path)
                        : null;
                @endphp
                @if($applicantSig && file_exists($applicantSig))
                    <div style="margin: 8px 0;">
                        <img src="{{ $applicantSig }}" style="height: 48px; max-width: 180px;">
                    </div>
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-meta">Name: {{ $listing->applicant->full_name ?? '____________________________' }}</div>
                <div class="sig-meta">Date: {{ now()->format('Y-m-d') }}</div>
            </td>
            <td>
                <span class="sig-label">Received By (Api Ghar Jagga)</span>
                <div class="sig-line"></div>
                <div class="sig-meta">Name: ____________________________</div>
                <div class="sig-meta">Date: ____________________________</div>
            </td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- SECTION 8: OFFICE USE ONLY --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="office-box">
        <div class="office-title">For Office Use Only / <span class="np">कार्यालय प्रयोजनका लागि मात्र</span></div>

        <table class="bordered-table">
            <tr>
                <th>Application No.</th>
                <th>Date Received</th>
                <th>Listing Status</th>
            </tr>
            <tr>
                <td>{{ $listing->application_no }}</td>
                <td>{{ $listing->date_received ? $listing->date_received->format('Y-m-d') : now()->format('Y-m-d') }}
                </td>
                <td>{{ ucfirst($listing->listing_status) }}</td>
            </tr>
        </table>

        <table class="data-table">
            <tr>
                <td class="label">Verified By</td>
                <td class="colon">:</td>
                <td class="value">____________________________</td>
            </tr>
            <tr>
                <td class="label">Approved By</td>
                <td class="colon">:</td>
                <td class="value">____________________________</td>
            </tr>
            <tr>
                <td class="label">Remarks</td>
                <td class="colon">:</td>
                <td class="value">{{ $listing->remarks ?? '' }}</td>
            </tr>
        </table>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- FOOTER --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="footer">
        © Api Ghar Jagga Pvt. Ltd. &nbsp;|&nbsp; Document Code: AGJ-FRM-001 &nbsp;|&nbsp; Version 1.0 &nbsp;|&nbsp;
        Generated: {{ now()->format('Y-m-d H:i') }}
    </div>

</body>

</html>