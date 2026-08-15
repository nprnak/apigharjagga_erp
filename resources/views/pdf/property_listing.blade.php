<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Listing Application - {{ $listing->application_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a1a1a; line-height: 1.5; }

        /* Header */
        .header { border-bottom: 2px solid #1e293b; padding-bottom: 12px; margin-bottom: 16px; }
        .header-meta { display: flex; justify-content: space-between; font-size: 8px; color: #6b7280; margin-bottom: 8px; }
        .header-title { text-align: center; }
        .header-title h1 { font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; }
        .header-title p { font-size: 11px; color: #475569; margin-top: 2px; }
        .header-info { display: flex; justify-content: space-between; margin-top: 10px; font-size: 9px; }
        .header-info .field { border-bottom: 1px solid #94a3b8; min-width: 200px; display: inline-block; padding-bottom: 2px; }

        /* Sections */
        .section { margin-bottom: 16px; page-break-inside: avoid; }
        .section-title { background: #1e293b; color: #fff; padding: 5px 10px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .section-subtitle { border-bottom: 1px solid #cbd5e1; padding-bottom: 3px; margin: 8px 0 6px; font-size: 9px; font-weight: bold; color: #374151; text-transform: uppercase; }

        /* Grid */
        .grid-2 { display: table; width: 100%; }
        .grid-3 { display: table; width: 100%; }
        .col { display: table-cell; padding-right: 12px; vertical-align: top; }
        .col:last-child { padding-right: 0; }
        .col-half { width: 50%; }
        .col-third { width: 33.33%; }
        .col-full { width: 100%; }

        /* Field */
        .field-group { margin-bottom: 8px; }
        .field-label { font-size: 8px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px; font-weight: bold; }
        .field-value { font-size: 10px; border-bottom: 1px solid #d1d5db; padding-bottom: 3px; min-height: 16px; color: #0f172a; }
        .field-value.empty { color: #9ca3af; font-style: italic; }

        /* Checkboxes */
        .checkbox-grid { display: table; width: 100%; }
        .checkbox-item { display: table-cell; width: 25%; font-size: 9px; padding: 2px 0; }
        .checkbox-box { display: inline-block; width: 10px; height: 10px; border: 1px solid #374151; vertical-align: middle; margin-right: 4px; text-align: center; line-height: 10px; font-size: 8px; }
        .checkbox-box.checked { background: #1e293b; color: #fff; }

        /* Declaration */
        .declaration-box { border: 1px solid #cbd5e1; background: #f8fafc; padding: 10px; margin: 8px 0; font-size: 9px; line-height: 1.6; color: #374151; }

        /* Signatures */
        .sig-table { display: table; width: 100%; border-top: 1px solid #cbd5e1; margin-top: 12px; }
        .sig-col { display: table-cell; width: 50%; padding-right: 20px; vertical-align: top; }
        .sig-col:last-child { padding-right: 0; }
        .sig-label { font-size: 8px; color: #6b7280; text-transform: uppercase; font-weight: bold; margin-bottom: 20px; }
        .sig-line { border-bottom: 1px solid #374151; margin-bottom: 4px; }
        .sig-name { font-size: 8px; color: #6b7280; }

        /* Office Use */
        .office-box { border: 2px solid #1e293b; padding: 10px; margin-top: 12px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }

        /* Footer */
        .footer { border-top: 1px solid #e2e8f0; margin-top: 20px; padding-top: 8px; text-align: center; font-size: 8px; color: #9ca3af; }

        .mt-4 { margin-top: 12px; }
        .row { display: table; width: 100%; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-meta">
            <span>Document Code: AGJ-FRM-001 &nbsp;|&nbsp; Version: 1.0</span>
            <span>ANNEX – A</span>
        </div>
        <div class="header-title">
            <h1>Property Listing Application Form</h1>
            <p>सम्पत्ति सूचीकरण आवेदन फाराम</p>
        </div>
        <div class="header-info">
            <div>
                <strong>Effective Date:</strong>
                <span class="field">{{ $listing->effective_date ? $listing->effective_date->format('Y-m-d') : '___________________' }}</span>
            </div>
            <div>
                <strong>Application No.:</strong>
                <span class="field">{{ $listing->application_no }}</span>
            </div>
        </div>
    </div>

    {{-- SECTION 1: APPLICANT DETAILS --}}
    <div class="section">
        <div class="section-title">1. Applicant Details &nbsp; १. आवेदकको विवरण</div>
        <div class="row">
            <div class="col col-half">
                <div class="field-group">
                    <div class="field-label">Full Name / पूरा नाम</div>
                    <div class="field-value">{{ $listing->applicant->full_name ?? '—' }}</div>
                </div>
            </div>
            <div class="col col-half">
                <div class="field-group">
                    <div class="field-label">Citizenship No. / नागरिकता नं.</div>
                    <div class="field-value">{{ $listing->applicant->citizenship_no ?? '—' }}</div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">Father's Name</div>
                    <div class="field-value">{{ $listing->applicant->father_mother_name ?? '—' }}</div>
                </div>
            </div>
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">Grandfather's Name</div>
                    <div class="field-value">{{ $listing->applicant->grandfather_name ?? '—' }}</div>
                </div>
            </div>
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">Date of Birth</div>
                    <div class="field-value">{{ $listing->applicant->date_of_birth ? $listing->applicant->date_of_birth->format('Y-m-d') : '—' }}</div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">Mobile No.</div>
                    <div class="field-value">{{ $listing->applicant->mobile_no ?? '—' }}</div>
                </div>
            </div>
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">Telephone No.</div>
                    <div class="field-value">{{ $listing->applicant->telephone_no ?? '—' }}</div>
                </div>
            </div>
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">E-mail</div>
                    <div class="field-value">{{ $listing->applicant->email ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: OWNERSHIP ROLE --}}
    <div class="section">
        <div class="section-title">2. Property Owner Details &nbsp; २. सम्पत्ति धनीको विवरण</div>
        <div class="field-group">
            <div class="field-label">Ownership Role / स्वामित्व भूमिका</div>
            <div class="field-value">{{ ucwords(str_replace('_', ' ', $listing->property->ownership_role ?? '—')) }}</div>
        </div>
    </div>

    {{-- SECTION 3: PROPERTY DETAILS --}}
    <div class="section">
        <div class="section-title">3. Property Details &nbsp; ३. सम्पत्तिको विवरण</div>
        <div class="field-group">
            <div class="field-label">Property Type</div>
            <div class="field-value">{{ ucwords(str_replace('_', ' ', $listing->property->property_type ?? '—')) }}</div>
        </div>

        <div class="section-subtitle">Address of Property / सम्पत्तिको ठेगाना</div>
        <div class="row">
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">Province</div>
                    <div class="field-value">{{ $listing->property->address->province ?? '—' }}</div>
                </div>
            </div>
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">District</div>
                    <div class="field-value">{{ $listing->property->address->district ?? '—' }}</div>
                </div>
            </div>
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">Municipality</div>
                    <div class="field-value">{{ $listing->property->address->municipality ?? '—' }}</div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">Ward No.</div>
                    <div class="field-value">{{ $listing->property->address->ward_no ?? '—' }}</div>
                </div>
            </div>
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">Tole/Locality</div>
                    <div class="field-value">{{ $listing->property->address->tole_locality ?? '—' }}</div>
                </div>
            </div>
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">GPS Location</div>
                    <div class="field-value">
                        @if($listing->property->address && $listing->property->address->gps_lat)
                            {{ $listing->property->address->gps_lat }}, {{ $listing->property->address->gps_lng }}
                        @else
                            —
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="section-subtitle">Land Information / जग्गाको विवरण</div>
        <div class="row">
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">Kitta No.</div>
                    <div class="field-value">{{ $listing->property->kitta_no ?? '—' }}</div>
                </div>
            </div>
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">Area</div>
                    <div class="field-value">{{ $listing->property->area ?? '—' }}</div>
                </div>
            </div>
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">Ownership Type</div>
                    <div class="field-value">{{ $listing->property->ownership_type ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 4: PURPOSE --}}
    <div class="section">
        <div class="section-title">4. Purpose of Listing &nbsp; ४. सूचीकरणको उद्देश्य</div>
        <div class="field-value">{{ ucfirst($listing->purpose_of_listing) }}</div>
    </div>

    {{-- SECTION 5: PRICE --}}
    <div class="section">
        <div class="section-title">5. Expected Price &nbsp; ५. अपेक्षित मूल्य</div>
        <div class="row">
            <div class="col col-half">
                <div class="field-group">
                    <div class="field-label">Expected Selling Price</div>
                    <div class="field-value">{{ $listing->expected_selling_price ? 'Rs. ' . number_format($listing->expected_selling_price, 2) : '—' }}</div>
                </div>
            </div>
            <div class="col col-half">
                <div class="field-group">
                    <div class="field-label">Negotiable</div>
                    <div class="field-value">{{ $listing->negotiable ? 'Yes' : 'No' }}</div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col col-half">
                <div class="field-group">
                    <div class="field-label">Minimum Acceptable Price</div>
                    <div class="field-value">{{ $listing->minimum_acceptable_price ? 'Rs. ' . number_format($listing->minimum_acceptable_price, 2) : '—' }}</div>
                </div>
            </div>
            <div class="col col-half">
                <div class="field-group">
                    <div class="field-label">Rental Amount</div>
                    <div class="field-value">{{ $listing->rental_amount ? 'Rs. ' . number_format($listing->rental_amount, 2) . '/mo' : '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 8: DECLARATION --}}
    <div class="section">
        <div class="section-title">8. Owner's Declaration &nbsp; ८. सम्पत्ति धनीको घोषणा</div>
        <div class="declaration-box">
            I hereby declare that the information provided in this application is true and correct to the best of my knowledge.
            I confirm that I am the lawful owner or authorized representative of the property and authorize Api Ghar Jagga
            to inspect, market, advertise, and facilitate the sale, rental, lease, or transfer of the property.
        </div>
        <div style="margin-top: 6px; font-size: 9px;">
            <span style="font-weight: bold;">✓</span> Applicant has agreed to the above declaration.
        </div>
    </div>

    {{-- SECTION 9: SIGNATURES --}}
    <div class="section">
        <div class="section-title">9. Signatures &nbsp; ९. हस्ताक्षर</div>
        <div class="sig-table">
            <div class="sig-col">
                <div class="sig-label">Applicant / Property Owner</div>
                <br><br>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $listing->applicant->full_name ?? '____________________________' }}</div>
                <div class="sig-name" style="margin-top:4px;">Date: {{ now()->format('Y-m-d') }}</div>
            </div>
            <div class="sig-col">
                <div class="sig-label">Received By (Api Ghar Jagga)</div>
                <br><br>
                <div class="sig-line"></div>
                <div class="sig-name">____________________________</div>
                <div class="sig-name" style="margin-top:4px;">Date: ___________________</div>
            </div>
        </div>
    </div>

    {{-- OFFICE USE ONLY --}}
    <div class="office-box">
        <div class="section-title" style="margin-bottom:10px;">10. Office Use Only &nbsp; १०. कार्यालय प्रयोजनका लागि मात्र</div>
        <div class="row">
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">Application No.</div>
                    <div class="field-value">{{ $listing->application_no }}</div>
                </div>
            </div>
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">Date Received</div>
                    <div class="field-value">{{ $listing->date_received ? $listing->date_received->format('Y-m-d') : now()->format('Y-m-d') }}</div>
                </div>
            </div>
            <div class="col col-third">
                <div class="field-group">
                    <div class="field-label">Listing Status</div>
                    <div class="field-value">
                        <span class="badge badge-{{ $listing->listing_status }}">{{ ucfirst($listing->listing_status) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        © Api Ghar Jagga Pvt. Ltd. &nbsp;|&nbsp; Document Code: AGJ-FRM-001 &nbsp;|&nbsp; Version 1.0 &nbsp;|&nbsp;
        Generated: {{ now()->format('Y-m-d H:i') }}
    </div>

</body>
</html>
