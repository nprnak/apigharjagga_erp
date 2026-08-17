<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sale/Purchase Agreement - AGJ-AGR-{{ str_pad($agreement->agreement_id, 5, '0', STR_PAD_LEFT) }}</title>
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
            width: 32%;
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

        /* ── Declaration / clause boxes ────────────────── */
        .declaration-box {
            border: 1px solid #000;
            padding: 8px 10px;
            margin: 6px 0;
            font-size: 9pt;
            line-height: 1.6;
        }

        .clause-box {
            border-left: 3px solid #333;
            padding: 4px 10px;
            margin: 6px 0;
            font-size: 9pt;
            line-height: 1.5;
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

        /* ── Footer ───────────────────────────────────── */
        .footer {
            margin-top: 15px;
            padding-top: 5px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 7pt;
            color: #555;
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
                <span class="doc-ref">Document Code: AGJ-FRM-002<br>Version: 1.0</span>
            </td>
            <td style="width: 50%; text-align: center;">
                <div class="org-name">Api Ghar Jagga Pvt. Ltd.</div>
                <div class="org-sub np">अपि घर जग्गा प्रा. लि.</div>
            </td>
            <td style="width: 25%; text-align: right;">
                <span class="doc-ref">ANNEX – B (PO-PB)</span>
            </td>
        </tr>
    </table>

    <div class="top-line"></div>

    <div class="form-title">House and Land Sale/Purchase Agreement</div>
    <div class="form-title-np np">घर जग्गा खरिद–बिक्री सम्झौता पत्र</div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Agreement No.:</td>
            <td class="meta-value">AGJ-AGR-{{ str_pad($agreement->agreement_id, 5, '0', STR_PAD_LEFT) }}</td>
            <td class="meta-label" style="text-align: right;">Date:</td>
            <td class="meta-value">{{ $agreement->agreement_date ? $agreement->agreement_date->format('Y-m-d') : now()->format('Y-m-d') }}</td>
        </tr>
    </table>

    <p style="font-size: 9pt; margin-bottom: 8px;">
        This House and Land Sale/Purchase Agreement ("Agreement") is made and entered into on
        <strong>{{ $agreement->agreement_date ? $agreement->agreement_date->format('jS F, Y') : '—' }}</strong>
        at <strong>{{ $agreement->place ?? '—' }}</strong>, between the following parties:
    </p>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- PARTIES --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">First Party (Seller) <span class="np">/ पहिलो पक्ष (विक्रेता)</span></div>
    <table class="data-table">
        <tr>
            <td class="label">Full Name</td>
            <td class="colon">:</td>
            <td class="value">{{ $seller?->client?->full_name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Father's/Mother's Name</td>
            <td class="colon">:</td>
            <td class="value">{{ $seller?->client?->father_mother_name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Citizenship No.</td>
            <td class="colon">:</td>
            <td class="value">{{ $seller?->client?->citizenship_no ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Permanent Address</td>
            <td class="colon">:</td>
            <td class="value">{{ $seller?->client?->permanentAddress?->full_address_text ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Contact No.</td>
            <td class="colon">:</td>
            <td class="value">{{ $seller?->client?->mobile_no ?? '—' }}</td>
        </tr>
    </table>

    <div class="section-heading">Second Party (Buyer) <span class="np">/ दोस्रो पक्ष (खरिदकर्ता)</span></div>
    <table class="data-table">
        <tr>
            <td class="label">Full Name</td>
            <td class="colon">:</td>
            <td class="value">{{ $buyer?->client?->full_name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Father's/Mother's Name</td>
            <td class="colon">:</td>
            <td class="value">{{ $buyer?->client?->father_mother_name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Citizenship No.</td>
            <td class="colon">:</td>
            <td class="value">{{ $buyer?->client?->citizenship_no ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Permanent Address</td>
            <td class="colon">:</td>
            <td class="value">{{ $buyer?->client?->permanentAddress?->full_address_text ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Contact No.</td>
            <td class="colon">:</td>
            <td class="value">{{ $buyer?->client?->mobile_no ?? '—' }}</td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- 1. PROPERTY DETAILS --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">1. Property Details <span class="np">/ सम्पत्तिको विवरण</span></div>

    <table class="bordered-table">
        <tr>
            <th>District</th>
            <th>Municipality</th>
            <th>Ward No.</th>
        </tr>
        <tr>
            <td>{{ $agreement->property?->address?->district ?? '—' }}</td>
            <td>{{ $agreement->property?->address?->municipality ?? '—' }}</td>
            <td>{{ $agreement->property?->address?->ward_no ?? '—' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <tr>
            <td class="label">Kitta (Parcel) No.</td>
            <td class="colon">:</td>
            <td class="value">{{ $agreement->property?->kitta_no ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Area</td>
            <td class="colon">:</td>
            <td class="value">{{ $agreement->property?->area ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">House Description (if any)</td>
            <td class="colon">:</td>
            <td class="value">{{ $agreement->house_description ?? '—' }}</td>
        </tr>
    </table>

    <div class="sub-heading">Boundaries / <span class="np">चार किल्ला</span></div>
    <table class="bordered-table">
        <tr>
            <th>East</th>
            <th>West</th>
            <th>North</th>
            <th>South</th>
        </tr>
        <tr>
            <td>{{ $agreement->boundary_east ?? '—' }}</td>
            <td>{{ $agreement->boundary_west ?? '—' }}</td>
            <td>{{ $agreement->boundary_north ?? '—' }}</td>
            <td>{{ $agreement->boundary_south ?? '—' }}</td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- 2. PURCHASE PRICE --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">2. Purchase Price <span class="np">/ बिक्री मूल्य</span></div>
    <table class="data-table">
        <tr>
            <td class="label">Total Agreed Price</td>
            <td class="colon">:</td>
            <td class="value">
                {{ $agreement->total_price ? 'Rs. ' . number_format($agreement->total_price, 2) : '—' }}
            </td>
        </tr>
        <tr>
            <td class="label">Amount in Words</td>
            <td class="colon">:</td>
            <td class="value">{{ $agreement->total_price_words ?? '—' }}</td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- 3. PAYMENT TERMS --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">3. Payment Terms <span class="np">/ भुक्तानीको व्यवस्था</span></div>
    <table class="bordered-table">
        <tr>
            <th>Advance Payment</th>
            <th>Balance Payment</th>
            <th>Final Payment Date</th>
        </tr>
        <tr>
            <td>{{ $agreement->advance_payment ? 'Rs. ' . number_format($agreement->advance_payment, 2) : '—' }}</td>
            <td>{{ $agreement->balance_payment ? 'Rs. ' . number_format($agreement->balance_payment, 2) : '—' }}</td>
            <td>{{ $agreement->final_payment_date ? $agreement->final_payment_date->format('Y-m-d') : '—' }}</td>
        </tr>
    </table>
    <p style="font-size: 8pt; color: #333;">The Buyer shall make the payment according to the mutually agreed schedule.</p>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- 4. TRANSFER OF OWNERSHIP --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">4. Transfer of Ownership <span class="np">/ स्वामित्व हस्तान्तरण</span></div>
    <p class="clause-box">
        Upon receipt of the full purchase price, the Seller shall appear before the concerned Land Revenue Office
        (Malpot Office) and complete the registration and transfer of ownership in accordance with the prevailing
        laws of Nepal.
    </p>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- 5 & 6. DECLARATIONS --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">5. Seller's Declaration <span class="np">/ विक्रेताको घोषणा</span></div>
    <div class="declaration-box">
        The Seller declares that: the property is under the Seller's lawful ownership; the property is free from
        mortgages, liens, encumbrances, disputes, or legal claims unless disclosed; and the Seller has full legal
        authority to sell the property.
    </div>
    <p style="font-size: 9pt;"><strong>☑</strong> Seller has agreed to the above declaration.</p>

    <div class="section-heading">6. Buyer's Declaration <span class="np">/ खरिदकर्ताको घोषणा</span></div>
    <div class="declaration-box">
        The Buyer confirms that the property has been inspected and is accepted in its present condition.
    </div>
    <p style="font-size: 9pt;"><strong>☑</strong> Buyer has agreed to the above declaration.</p>

    <div class="page-break"></div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- 7-11. GENERAL PROVISIONS --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">7. Taxes and Registration Fees <span class="np">/ कर तथा शुल्क</span></div>
    <p class="clause-box">
        All government taxes, registration fees, transfer charges, and other expenses shall be borne by the parties
        as mutually agreed.
    </p>

    <div class="section-heading">8. Breach of Agreement <span class="np">/ सम्झौता उल्लङ्घन</span></div>
    <p class="clause-box">
        If either party breaches this Agreement, the non-defaulting party shall be entitled to claim damages and
        seek legal remedies under the prevailing laws of Nepal.
    </p>

    <div class="section-heading">9. Dispute Resolution <span class="np">/ विवाद समाधान</span></div>
    <p class="clause-box">
        Any dispute arising from this Agreement shall first be settled through mutual negotiation. If unresolved, it
        shall be submitted to the competent court of Nepal.
    </p>

    <div class="section-heading">10. Governing Law <span class="np">/ लागू हुने कानून</span></div>
    <p class="clause-box">{{ $agreement->governing_law ?? 'This Agreement shall be governed by and construed in accordance with the prevailing laws of Nepal.' }}</p>

    <div class="section-heading">11. Final Provisions <span class="np">/ अन्तिम व्यवस्था</span></div>
    <p class="clause-box">
        Both parties have carefully read and understood this Agreement and have signed it voluntarily without any
        coercion or undue influence. This Agreement shall be binding upon both parties.
    </p>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- SIGNATURES --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">Signatures <span class="np">/ हस्ताक्षर</span></div>

    <table class="sig-table">
        <tr>
            <td>
                <span class="sig-label">First Party (Seller)</span>
                @php
                    $sellerSig = $agreement->seller_signature_path
                        ? storage_path('app/public/' . $agreement->seller_signature_path)
                        : null;
                @endphp
                @if($sellerSig && file_exists($sellerSig))
                    <div style="margin: 8px 0;">
                        <img src="{{ $sellerSig }}" style="height: 48px; max-width: 180px;">
                    </div>
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-meta">Name: {{ $seller?->client?->full_name ?? '____________________________' }}</div>
                <div class="sig-meta">Date: {{ now()->format('Y-m-d') }}</div>
            </td>
            <td>
                <span class="sig-label">Second Party (Buyer)</span>
                @php
                    $buyerSig = $agreement->buyer_signature_path
                        ? storage_path('app/public/' . $agreement->buyer_signature_path)
                        : null;
                @endphp
                @if($buyerSig && file_exists($buyerSig))
                    <div style="margin: 8px 0;">
                        <img src="{{ $buyerSig }}" style="height: 48px; max-width: 180px;">
                    </div>
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-meta">Name: {{ $buyer?->client?->full_name ?? '____________________________' }}</div>
                <div class="sig-meta">Date: {{ now()->format('Y-m-d') }}</div>
            </td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- WITNESSES --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section-heading">Witnesses <span class="np">/ साक्षीहरू</span></div>

    <table class="sig-table">
        <tr>
            <td>
                <span class="sig-label">Witness 1</span>
                <div class="sig-meta">Name: {{ $agreement->witnesses[0]->full_name ?? '____________________________' }}</div>
                <div class="sig-meta">Citizenship No.: {{ $agreement->witnesses[0]->citizenship_no ?? '____________________________' }}</div>
                @php
                    $w1Sig = isset($agreement->witnesses[0]) && $agreement->witnesses[0]->signature_path
                        ? storage_path('app/public/' . $agreement->witnesses[0]->signature_path)
                        : null;
                @endphp
                @if($w1Sig && file_exists($w1Sig))
                    <div style="margin: 8px 0;">
                        <img src="{{ $w1Sig }}" style="height: 40px; max-width: 160px;">
                    </div>
                @else
                    <div class="sig-line"></div>
                    <div class="sig-meta">Signature</div>
                @endif
            </td>
            <td>
                <span class="sig-label">Witness 2</span>
                <div class="sig-meta">Name: {{ $agreement->witnesses[1]->full_name ?? '____________________________' }}</div>
                <div class="sig-meta">Citizenship No.: {{ $agreement->witnesses[1]->citizenship_no ?? '____________________________' }}</div>
                @php
                    $w2Sig = isset($agreement->witnesses[1]) && $agreement->witnesses[1]->signature_path
                        ? storage_path('app/public/' . $agreement->witnesses[1]->signature_path)
                        : null;
                @endphp
                @if($w2Sig && file_exists($w2Sig))
                    <div style="margin: 8px 0;">
                        <img src="{{ $w2Sig }}" style="height: 40px; max-width: 160px;">
                    </div>
                @else
                    <div class="sig-line"></div>
                    <div class="sig-meta">Signature</div>
                @endif
            </td>
        </tr>
    </table>

    <p style="font-size: 9pt; margin-top: 15px;">
        <strong>Place:</strong> {{ $agreement->place ?? '—' }} &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Date of Agreement:</strong> {{ $agreement->agreement_date ? $agreement->agreement_date->format('Y-m-d') : '—' }}
    </p>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- FOOTER --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="footer">
        © Api Ghar Jagga Pvt. Ltd. &nbsp;|&nbsp; Document Code: AGJ-FRM-002 &nbsp;|&nbsp; Version 1.0 &nbsp;|&nbsp;
        Generated: {{ now()->format('Y-m-d H:i') }}
    </div>

</body>

</html>
