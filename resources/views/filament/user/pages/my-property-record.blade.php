<x-filament-panels::page>
    @php
        $p = $this->getRecord();
        $listing = $p->listings->sortByDesc('listing_id')->first();
        $statusMeta = [
            'approved' => ['label' => 'Approved', 'dot' => 'bg-emerald-500', 'text' => 'text-emerald-700 dark:text-emerald-400', 'seal' => 'APPROVED', 'sealNp' => 'स्वीकृत'],
            'pending' => ['label' => 'Submitted — Pending Review', 'dot' => 'bg-amber-500', 'text' => 'text-amber-700 dark:text-amber-400', 'seal' => 'SUBMITTED', 'sealNp' => 'पेश गरिएको'],
            'rejected' => ['label' => 'Needs Correction', 'dot' => 'bg-rose-500', 'text' => 'text-rose-700 dark:text-rose-400', 'seal' => null, 'sealNp' => null],
        ];
        $meta = $statusMeta[$p->approval_status] ?? ['label' => 'Unknown', 'dot' => 'bg-zinc-400', 'text' => 'text-zinc-500', 'seal' => null, 'sealNp' => null];
        $canEdit = \App\Filament\User\Resources\MyPropertyResource::canEdit($p);

        $propertyTypeOptions = [
            'land' => 'Land', 'house' => 'House', 'apartment' => 'Apartment',
            'commercial_building' => 'Commercial Building', 'office_space' => 'Office Space',
            'industrial_property' => 'Industrial Property', 'agricultural_land' => 'Agricultural Land', 'other' => 'Other',
        ];
        $ownershipRoleOptions = [
            'self' => 'Sole Owner (Self)', 'family_member' => 'Family Member',
            'authorized_representative' => 'Authorized Representative', 'company' => 'Company',
        ];
        $purposeOptions = [
            'sale' => 'For Sale', 'rent' => 'For Rent', 'lease' => 'Long-Term Lease',
            'exchange' => 'Exchange', 'investment' => 'Joint Investment', 'other' => 'Other',
        ];
    @endphp

    <style>
        @media print {
            .no-print { display: none !important; }
            .fi-sidebar, .fi-topbar { display: none !important; }
            html, body { background: #fff !important; }
            #property-sheet {
                box-shadow: none !important; border: 1px solid #999 !important; background: #fff !important;
                color: #18242E !important; -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            #property-sheet * { color: inherit !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            #property-sheet .kyc-row .k { color: #5C6B76 !important; }
            #property-sheet .kyc-row .v.blank { color: #767676 !important; }
            #property-sheet .kyc-pill { border-color: #C3CBD1 !important; color: #767676 !important; }
            #property-sheet .kyc-pill.on { color: #125E4A !important; background: #EEF4F0 !important; border-color: #9BC4B4 !important; }
            #property-sheet .kyc-secnum { background: #18242E !important; color: #fff !important; }
            #property-sheet [class*="border-emerald"] { border-color: #9BC4B4 !important; color: #14532d !important; }
            #property-sheet [class*="bg-emerald"] { background: #EEF4F0 !important; }
            #property-sheet [class*="border-amber"] { border-color: #E0C480 !important; }
            #property-sheet [class*="bg-amber"] { background: #FBF4E3 !important; }
            #property-sheet [class*="text-amber"] { color: #8A6206 !important; }
        }
        .np { font-family: 'Noto Sans Devanagari', 'Kalimati', sans-serif; }
        .kyc-row { display: grid; grid-template-columns: 40% 1fr; gap: 0; border-bottom: 1px dashed #E2E7EA; padding: 5px 0; }
        .dark .kyc-row { border-color: rgba(255,255,255,.08); }
        .kyc-row .k { color: #5C6B76; font-size: .8rem; }
        .dark .kyc-row .k { color: #9CA6AC; }
        .kyc-row .k .np { display: block; font-size: .72rem; }
        .kyc-row .v { font-weight: 600; font-size: .85rem; }
        .kyc-row .v.blank { font-weight: 400; font-style: italic; color: #93A1A9; }
        .kyc-pill { display: inline-flex; align-items: center; gap: 5px; font-size: .78rem; border: 1px solid #E2E7EA; border-radius: 999px; padding: 3px 10px; color: #93A1A9; }
        .dark .kyc-pill { border-color: rgba(255,255,255,.12); }
        .kyc-pill.on { color: #125E4A; font-weight: 600; border-color: #9BC4B4; background: #EEF4F0; }
        .dark .kyc-pill.on { color: #6FCF9E; background: rgba(18,94,74,.15); border-color: rgba(111,207,158,.3); }
        .kyc-secnum { display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 4px; background: #18242E; color: #fff; font-size: .7rem; font-weight: 700; flex-shrink: 0; }
        .dark .kyc-secnum { background: #E2E7EA; color: #18242E; }
    </style>

    <div class="space-y-4">
        <div class="no-print flex flex-wrap items-center justify-between gap-3 rounded-xl border border-zinc-200 bg-white px-5 py-3.5 dark:border-zinc-800 dark:bg-[#0c0c0f]">
            <div class="flex items-center gap-2.5">
                <span class="h-2.5 w-2.5 rounded-full {{ $meta['dot'] }}"></span>
                <span class="text-sm font-semibold {{ $meta['text'] }}">{{ $meta['label'] }}</span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('my-properties.pdf', $p) }}" target="_blank" class="fi-btn inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 px-3 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                    Download PDF
                </a>
                <button type="button" onclick="window.print()" class="fi-btn inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 px-3 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                    Print
                </button>
                @if($canEdit)
                    <a href="{{ \App\Filament\User\Resources\MyPropertyResource::getUrl('edit', ['record' => $p]) }}" class="fi-btn inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary-500">
                        Edit
                    </a>
                @endif
            </div>
        </div>

        @if($p->approval_status === 'rejected')
            <div class="rounded-xl border border-rose-200 bg-rose-50/60 px-5 py-3.5 text-sm text-rose-800 dark:border-rose-900/50 dark:bg-rose-950/20 dark:text-rose-300">
                <strong>Corrections needed before resubmitting:</strong>
                {{ $listing?->remarks ?: 'Please review your details and documents, then resubmit.' }}
            </div>
        @endif

        <div id="property-sheet" class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-8 dark:border-zinc-800 dark:bg-[#0c0c0f]">

            @if($meta['seal'])
                <div class="pointer-events-none absolute right-8 top-8 -rotate-6 rounded border-2 border-double px-4 py-2 text-center {{ $p->approval_status === 'approved' ? 'border-emerald-700 text-emerald-700' : 'border-sky-700 text-sky-700' }} dark:opacity-90">
                    <div class="text-sm font-extrabold tracking-[0.2em]">{{ $meta['seal'] }}</div>
                    <div class="np text-xs font-semibold leading-none">{{ $meta['sealNp'] }}</div>
                    <div class="mt-1 border-t {{ $p->approval_status === 'approved' ? 'border-emerald-700' : 'border-sky-700' }} pt-1 text-[9px]">
                        {{ $p->property_code }}
                    </div>
                </div>
            @endif

            <div class="flex items-start justify-between gap-4 border-b-2 border-zinc-900 pb-3 dark:border-zinc-100">
                <div>
                    <div class="text-base font-bold text-zinc-900 dark:text-zinc-100">API Ghar Jagga Pvt. Ltd.</div>
                    <div class="np text-sm text-zinc-500">अपि घर जग्गा प्रा. लि.</div>
                </div>
                <div class="text-right text-[10px] leading-relaxed text-zinc-400">
                    <div class="font-bold text-zinc-600 dark:text-zinc-300">AGJ-FRM-001</div>
                    ANNEX &ndash; A<br>
                    Ref. {{ $listing?->application_no ?? $p->property_code }}
                </div>
            </div>

            <div class="mt-4 text-center">
                <h1 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Property Listing Application Record</h1>
                <p class="np text-sm text-zinc-500">सम्पत्ति सूचीकरण आवेदन अभिलेख</p>
            </div>

            <div class="mt-5 grid grid-cols-2 divide-x divide-zinc-100 rounded border border-zinc-200 sm:grid-cols-4 dark:divide-zinc-800 dark:border-zinc-800">
                <div class="px-3 py-2">
                    <div class="text-[10px] text-zinc-400">Property Code</div>
                    <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $p->property_code }}</div>
                </div>
                <div class="px-3 py-2">
                    <div class="text-[10px] text-zinc-400">Application No.</div>
                    <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $listing?->application_no ?? '—' }}</div>
                </div>
                <div class="px-3 py-2">
                    <div class="text-[10px] text-zinc-400">Submitted</div>
                    <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ optional($p->created_at)->format('d M Y') ?: '—' }}</div>
                </div>
                <div class="px-3 py-2">
                    <div class="text-[10px] text-zinc-400">Marketplace Status</div>
                    <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ ucwords(str_replace('_', ' ', $p->status)) }}</div>
                </div>
            </div>

            {{-- 1. Applicant Details --}}
            @php $kyc = $p->user?->kycVerification; @endphp
            <div class="mt-6">
                <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                    <span class="kyc-secnum">1</span>
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Applicant Details</h2>
                    <span class="np text-xs text-zinc-400">/ आवेदक विवरण</span>
                    <span class="ml-auto text-[10px] italic text-zinc-400">From approved KYC</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-8">
                    @foreach([
                        ['Full Name', 'पूरा नाम', $kyc?->full_name],
                        ['Citizenship No.', 'नागरिकता नं.', $kyc?->citizenship_no],
                        ['Mobile No.', 'मोबाइल नं.', $kyc?->mobile_no],
                        ['Email', 'इमेल', $kyc?->email],
                    ] as [$label, $np, $value])
                        <div class="kyc-row"><div class="k">{{ $label }}<span class="np">{{ $np }}</span></div><div class="v {{ $value ? '' : 'blank' }}">{{ $value ?: 'Not provided' }}</div></div>
                    @endforeach
                </div>
            </div>

            {{-- 2. Property Owner Details --}}
            <div class="mt-6">
                <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                    <span class="kyc-secnum">2</span>
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Property Owner Details</h2>
                    <span class="np text-xs text-zinc-400">/ धनी विवरण</span>
                </div>
                <div class="flex flex-wrap gap-2 py-1">
                    @foreach($ownershipRoleOptions as $val => $label)
                        <span class="kyc-pill {{ $p->ownership_role === $val ? 'on' : '' }}">{{ $label }}</span>
                    @endforeach
                </div>
                @if($p->ownership_role !== 'self')
                    <div class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-8">
                        @foreach([
                            ['Owner Full Name', 'धनीको नाम', $p->owner_full_name],
                            ['Owner Citizenship No.', 'नागरिकता नं.', $p->owner_citizenship_no],
                            ['Relationship / Authority', 'सम्बन्ध', $p->owner_relation],
                        ] as [$label, $np, $value])
                            <div class="kyc-row"><div class="k">{{ $label }}<span class="np">{{ $np }}</span></div><div class="v {{ $value ? '' : 'blank' }}">{{ $value ?: 'Not provided' }}</div></div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- 3. Property Details --}}
            <div class="mt-6">
                <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                    <span class="kyc-secnum">3</span>
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Property Details</h2>
                    <span class="np text-xs text-zinc-400">/ सम्पत्ति विवरण</span>
                </div>
                <div class="flex flex-wrap gap-2 py-1">
                    @foreach($propertyTypeOptions as $val => $label)
                        <span class="kyc-pill {{ $p->property_type === $val ? 'on' : '' }}">{{ $label }}</span>
                    @endforeach
                </div>

                <h3 class="mt-3 text-xs font-bold uppercase tracking-wide text-zinc-500">Address of Property <span class="np font-normal">/ सम्पत्तिको ठेगाना</span></h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-8">
                    @php
                        $addressLine = collect([$p->address?->tole_locality, $p->address?->ward_no ? 'Ward '.$p->address->ward_no : null, $p->address?->municipality, $p->address?->district, $p->address?->province])->filter()->implode(', ') ?: null;
                    @endphp
                    <div class="kyc-row sm:col-span-2"><div class="k">Full Address<span class="np">पूरा ठेगाना</span></div><div class="v {{ $addressLine ? '' : 'blank' }}">{{ $addressLine ?: 'Not provided' }}</div></div>
                </div>

                <h3 class="mt-3 text-xs font-bold uppercase tracking-wide text-zinc-500">Land Information <span class="np font-normal">/ जग्गा विवरण</span></h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-8">
                    @foreach([
                        ['Kitta No.', 'कित्ता नं.', $p->kitta_no],
                        ['Land Area', 'क्षेत्रफल', $p->area],
                        ['Map Sheet No.', 'नक्सा पाना नं.', $p->map_sheet_no],
                        ['Ownership Type', 'स्वामित्व प्रकार', $p->ownership_type ? ucfirst($p->ownership_type) : null],
                        ['Ownership Certificate No.', 'लालपुर्जा नं.', $p->ownership_certificate_no],
                        ['Road Access', 'सडक पहुँच', $p->road_access],
                        ['Road Width', 'सडकको चौडाई', $p->road_width],
                        ['Facing Direction', 'दिशा', $p->facing_direction],
                    ] as [$label, $np, $value])
                        <div class="kyc-row"><div class="k">{{ $label }}<span class="np">{{ $np }}</span></div><div class="v {{ $value ? '' : 'blank' }}">{{ $value ?: 'Not provided' }}</div></div>
                    @endforeach
                </div>

                @if($p->isBuildingType())
                    <h3 class="mt-3 text-xs font-bold uppercase tracking-wide text-zinc-500">Building Details <span class="np font-normal">/ घर विवरण</span></h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-8">
                        @foreach([
                            ['Year Built', 'निर्माण वर्ष', $p->year_of_construction],
                            ['No. of Floors', 'तल्ला संख्या', $p->no_of_floors],
                            ['Covered Area', 'ओगटेको क्षेत्रफल', $p->covered_area],
                            ['Structure System', 'संरचना प्रकार', $p->structure_type],
                            ['Roof Type', 'छानाको प्रकार', $p->roof_type],
                            ['Parking', 'पार्किङ', $p->parking],
                            ['Water Supply', 'पानीको आपूर्ति', $p->water_supply],
                            ['Electricity', 'बिजुली', $p->electricity],
                            ['Internet', 'इन्टरनेट', $p->internet],
                            ['Drainage', 'ढल निकास', $p->drainage],
                            ['Building Permit No.', 'नक्सा पास नं.', $p->building_permit_no],
                            ['Current Condition', 'हालको अवस्था', $p->current_building_condition ? ucfirst($p->current_building_condition) : null],
                        ] as [$label, $np, $value])
                            <div class="kyc-row"><div class="k">{{ $label }}<span class="np">{{ $np }}</span></div><div class="v {{ $value ? '' : 'blank' }}">{{ $value ?: 'Not provided' }}</div></div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- 4. Purpose of Listing --}}
            <div class="mt-6">
                <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                    <span class="kyc-secnum">4</span>
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Purpose of Listing</h2>
                    <span class="np text-xs text-zinc-400">/ उद्देश्य</span>
                </div>
                <div class="flex flex-wrap gap-2 py-1">
                    @foreach($purposeOptions as $val => $label)
                        <span class="kyc-pill {{ $listing?->purpose_of_listing === $val ? 'on' : '' }}">{{ $label }}</span>
                    @endforeach
                </div>
            </div>

            {{-- 5. Expected Price --}}
            <div class="mt-6">
                <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                    <span class="kyc-secnum">5</span>
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Expected Price</h2>
                    <span class="np text-xs text-zinc-400">/ अपेक्षित मूल्य</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-8">
                    @foreach([
                        ['Expected Selling Price', 'अपेक्षित मूल्य', $listing?->expected_selling_price ? 'Rs. '.number_format((float) $listing->expected_selling_price, 2) : null],
                        ['Negotiable', 'मोलमोलाइ', $listing ? ($listing->negotiable ? 'Yes' : 'No') : null],
                        ['Minimum Acceptable Price', 'न्यूनतम मूल्य', $listing?->minimum_acceptable_price ? 'Rs. '.number_format((float) $listing->minimum_acceptable_price, 2) : null],
                        ['Monthly Rent', 'मासिक भाडा', $listing?->rental_amount ? 'Rs. '.number_format((float) $listing->rental_amount, 2) : null],
                    ] as [$label, $np, $value])
                        <div class="kyc-row"><div class="k">{{ $label }}<span class="np">{{ $np }}</span></div><div class="v {{ $value ? '' : 'blank' }}">{{ $value ?: 'Not provided' }}</div></div>
                    @endforeach
                </div>
            </div>

            {{-- 6. Property Documents Submitted --}}
            <div class="mt-6">
                <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                    <span class="kyc-secnum">6</span>
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Property Documents Submitted</h2>
                    <span class="np text-xs text-zinc-400">/ कागजात</span>
                    <span class="ml-auto text-[10px] italic text-zinc-400">Excludes documents already verified with KYC</span>
                </div>
                <div class="divide-y divide-dashed divide-zinc-200 dark:divide-zinc-800">
                    @forelse($p->documents as $doc)
                        <div class="flex items-center justify-between py-1.5 text-sm">
                            <span class="text-zinc-700 dark:text-zinc-300">{{ $doc->docType?->doc_name ?? 'Document' }}</span>
                            @if($doc->status === 'submitted')
                                <span class="rounded-full border border-emerald-300 bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-400">Submitted</span>
                            @else
                                <span class="rounded-full border border-amber-300 bg-amber-50 px-2.5 py-0.5 text-[11px] font-semibold text-amber-700 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-400">Pending</span>
                            @endif
                        </div>
                    @empty
                        <p class="py-1 text-sm italic text-zinc-400">No documents recorded.</p>
                    @endforelse
                </div>
            </div>

            {{-- 7. Property Features --}}
            <div class="mt-6">
                <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                    <span class="kyc-secnum">7</span>
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Property Features</h2>
                    <span class="np text-xs text-zinc-400">/ विशेषताहरू</span>
                </div>
                @php $selectedFeatureIds = $p->features->pluck('feature_id')->all(); @endphp
                <div class="grid grid-cols-1 gap-2 py-1 sm:grid-cols-2">
                    @foreach(\App\Models\PropertyFeatureType::orderBy('feature_name')->get() as $feature)
                        <div class="kyc-pill justify-start {{ in_array($feature->feature_id, $selectedFeatureIds, true) ? 'on' : '' }}">
                            {{ $feature->feature_name }}
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 8. Photographs of Property --}}
            <div class="mt-6">
                <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                    <span class="kyc-secnum">8</span>
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Photographs of Property</h2>
                    <span class="np text-xs text-zinc-400">/ तस्विरहरू</span>
                </div>
                @if($p->photos->isNotEmpty())
                    <div class="grid grid-cols-2 gap-3 py-1 sm:grid-cols-4">
                        @foreach($p->photos as $photo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($photo->file_ref) }}" alt="Property photo" class="h-24 w-full rounded border border-zinc-200 object-cover dark:border-zinc-700">
                        @endforeach
                    </div>
                @else
                    <p class="py-1 text-sm italic text-zinc-400">No photographs uploaded.</p>
                @endif
            </div>

            {{-- 9. Declaration & Signature --}}
            <div class="mt-6">
                <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                    <span class="kyc-secnum">9</span>
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Declaration &amp; Signature</h2>
                    <span class="np text-xs text-zinc-400">/ घोषणा</span>
                </div>
                <div class="rounded border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-xs text-zinc-600 dark:border-zinc-800 dark:bg-zinc-900/40 dark:text-zinc-400">
                    <p>I hereby declare that the information provided above about this property is true and correct to the best of my knowledge, as required under Annex A of API GharJagga's property listing process.</p>
                    <p class="np mt-1.5">मैले माथि उल्लेखित सम्पत्ति सम्बन्धी विवरण साँचो र सही भएको घोषणा गर्दछु।</p>
                </div>
                <div class="mt-3 border-t border-zinc-800 pt-2 dark:border-zinc-200">
                    @if($listing?->applicant_signature_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($listing->applicant_signature_path) }}" alt="Signature" class="h-10 max-w-[140px] object-contain">
                    @endif
                    <div class="mt-1 text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $kyc?->full_name ?? $p->user?->name }}</div>
                    <div class="text-xs text-zinc-400">{{ optional($p->created_at)->format('d M Y') ?: '—' }}</div>
                </div>
            </div>

            {{-- 10. Office Use Only --}}
            <div class="mt-6">
                <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                    <span class="kyc-secnum">10</span>
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Office Use Only</h2>
                    <span class="np text-xs text-zinc-400">/ कार्यालय प्रयोजनका लागि मात्र</span>
                    <span class="ml-auto text-[10px] italic text-zinc-400">Completed by the reviewing officer</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-8">
                    @foreach([
                        ['Assigned / Reviewing Officer', 'जिम्मेवार कर्मचारी', $listing?->assignedOfficer?->full_name],
                        ['Legal Verification Status', 'कानूनी प्रमाणीकरण', $listing?->legal_verification_status ? ucfirst($listing->legal_verification_status) : null],
                        ['Listing Status', 'सूचीकरण अवस्था', $listing?->listing_status ? ucfirst($listing->listing_status) : null],
                        ['Reviewed On', 'समीक्षा मिति', optional($p->updated_at)->format('d M Y')],
                    ] as [$label, $np, $value])
                        <div class="kyc-row"><div class="k">{{ $label }}<span class="np">{{ $np }}</span></div><div class="v {{ $value ? '' : 'blank' }}">{{ $value ?: 'Pending review' }}</div></div>
                    @endforeach
                </div>
                @if($listing?->remarks)
                    <div class="mt-2 rounded border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700 dark:border-rose-900/50 dark:bg-rose-950/20 dark:text-rose-300">
                        <strong>Remarks:</strong> {{ $listing->remarks }}
                    </div>
                @endif
            </div>

            <div class="mt-6 border-t border-zinc-100 pt-2 text-center text-[10px] text-zinc-400 dark:border-zinc-800">
                &copy; Api Ghar Jagga | AGJ-FRM-001 | This is a system-generated Annex-A property listing record.
            </div>
        </div>
    </div>
</x-filament-panels::page>
