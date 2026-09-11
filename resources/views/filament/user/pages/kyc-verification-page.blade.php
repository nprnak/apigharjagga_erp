<x-filament-panels::page>
    @php
        $k = $kycRecord;
        $statusMeta = [
            'approved' => ['label' => 'Approved', 'dot' => 'bg-emerald-500', 'text' => 'text-emerald-700 dark:text-emerald-400', 'seal' => 'APPROVED', 'sealNp' => 'स्वीकृत'],
            'verified' => ['label' => 'Verified — Awaiting Approval', 'dot' => 'bg-sky-500', 'text' => 'text-sky-700 dark:text-sky-400', 'seal' => 'VERIFIED', 'sealNp' => 'प्रमाणित'],
            'pending' => ['label' => 'Submitted — Pending Review', 'dot' => 'bg-amber-500', 'text' => 'text-amber-700 dark:text-amber-400', 'seal' => 'SUBMITTED', 'sealNp' => 'पेश गरिएको'],
            'rejected' => ['label' => 'Needs Correction', 'dot' => 'bg-rose-500', 'text' => 'text-rose-700 dark:text-rose-400', 'seal' => null, 'sealNp' => null],
        ];
        $meta = $statusMeta[$k?->status] ?? ['label' => 'Not Submitted', 'dot' => 'bg-zinc-400', 'text' => 'text-zinc-500 dark:text-zinc-400', 'seal' => null, 'sealNp' => null];
        $isLocked = $k && in_array($k->status, ['pending', 'verified', 'approved'], true);
        $stageOrder = ['pending' => 1, 'verified' => 2, 'approved' => 3];
        $currentStage = $stageOrder[$k?->status] ?? 0;

        $addressLine = fn ($tole, $ward, $muni, $dist, $prov) => collect([$tole, $ward ? 'Ward '.$ward : null, $muni, $dist, $prov])->filter()->implode(', ') ?: null;
        $clientTypeLabel = \App\Models\User::clientTypeOptions()[auth()->user()?->client_type] ?? null;
    @endphp

    <style>
        @media print {
            .no-print { display: none !important; }
            .fi-sidebar, .fi-topbar { display: none !important; }
            #kyc-sheet { box-shadow: none !important; border: 0 !important; }
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
        {{-- Simple, single status bar — no banners, no animation --}}
        <div class="no-print flex flex-wrap items-center justify-between gap-3 rounded-xl border border-zinc-200 bg-white px-5 py-3.5 dark:border-zinc-800 dark:bg-[#0c0c0f]">
            <div class="flex items-center gap-2.5">
                <span class="h-2.5 w-2.5 rounded-full {{ $meta['dot'] }}"></span>
                <span class="text-sm font-semibold {{ $meta['text'] }}">{{ $meta['label'] }}</span>
                @if($k && $k->status !== 'rejected')
                    <span class="text-xs text-zinc-400">· Stage {{ $currentStage }} of 3</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($k?->status === 'approved')
                    <a href="{{ route('kyc.my.pdf') }}" target="_blank" class="fi-btn inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 px-3 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                        Download PDF
                    </a>
                @endif
                @if($isLocked)
                    <button type="button" onclick="window.print()" class="fi-btn inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 px-3 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                        Print
                    </button>
                @endif
            </div>
        </div>

        @if($k?->status === 'rejected')
            <div class="rounded-xl border border-rose-200 bg-rose-50/60 px-5 py-3.5 text-sm text-rose-800 dark:border-rose-900/50 dark:bg-rose-950/20 dark:text-rose-300">
                <strong>Corrections needed before resubmitting:</strong>
                {{ $k->admin_note ?: 'Please review your details and documents, then resubmit.' }}
            </div>
        @endif

        @if(! $isLocked)
            {{-- Editable multi-step Annex-F form --}}
            <form wire:submit="submit">
                {{ $this->form }}
            </form>
        @else
            {{-- One consolidated, printable record — every Annex F section, in order --}}
            <div id="kyc-sheet" class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-8 dark:border-zinc-800 dark:bg-[#0c0c0f]">

                {{-- Registration / approval seal --}}
                @if($meta['seal'])
                    <div class="pointer-events-none absolute right-8 top-8 -rotate-6 rounded border-2 border-double px-4 py-2 text-center {{ $k->status === 'approved' ? 'border-emerald-700 text-emerald-700' : 'border-sky-700 text-sky-700' }} dark:opacity-90">
                        <div class="text-sm font-extrabold tracking-[0.2em]">{{ $meta['seal'] }}</div>
                        <div class="np text-xs font-semibold leading-none">{{ $meta['sealNp'] }}</div>
                        <div class="mt-1 border-t {{ $k->status === 'approved' ? 'border-emerald-700' : 'border-sky-700' }} pt-1 text-[9px]">
                            {{ $k->digital_client_id ?? ('#'.$k->id) }}
                        </div>
                    </div>
                @endif

                {{-- Masthead --}}
                <div class="flex items-start justify-between gap-4 border-b-2 border-zinc-900 pb-3 dark:border-zinc-100">
                    <div>
                        <div class="text-base font-bold text-zinc-900 dark:text-zinc-100">API Ghar Jagga Pvt. Ltd.</div>
                        <div class="np text-sm text-zinc-500">अपि घर जग्गा प्रा. लि.</div>
                    </div>
                    <div class="text-right text-[10px] leading-relaxed text-zinc-400">
                        <div class="font-bold text-zinc-600 dark:text-zinc-300">AGJ-FRM-07</div>
                        ANNEX &ndash; F<br>
                        Ref. {{ $k->digital_client_id ?? ('AGJ-KYC-'.str_pad((string) $k->id, 5, '0', STR_PAD_LEFT)) }}
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <h1 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Client KYC Verification Record</h1>
                    <p class="np text-sm text-zinc-500">ग्राहक पहिचान (के.वाई.सी.) प्रमाणीकरण अभिलेख</p>
                </div>

                {{-- Submission strip --}}
                <div class="mt-5 grid grid-cols-2 divide-x divide-zinc-100 rounded border border-zinc-200 sm:grid-cols-4 dark:divide-zinc-800 dark:border-zinc-800">
                    <div class="px-3 py-2">
                        <div class="text-[10px] text-zinc-400">Client ID</div>
                        <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $k->digital_client_id ?? 'Pending' }}</div>
                    </div>
                    <div class="px-3 py-2">
                        <div class="text-[10px] text-zinc-400">Submitted</div>
                        <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ optional($k->submitted_at)->format('d M Y') ?: '—' }}</div>
                    </div>
                    <div class="px-3 py-2">
                        <div class="text-[10px] text-zinc-400">Verified</div>
                        <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ optional($k->verified_at)->format('d M Y') ?: '—' }}</div>
                    </div>
                    <div class="px-3 py-2">
                        <div class="text-[10px] text-zinc-400">Approved</div>
                        <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ optional($k->approved_at)->format('d M Y') ?: '—' }}</div>
                    </div>
                </div>

                {{-- 1. Client Type --}}
                <div class="mt-6">
                    <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                        <span class="kyc-secnum">1</span>
                        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Client Type</h2>
                        <span class="np text-xs text-zinc-400">/ ग्राहकको प्रकार</span>
                    </div>
                    <div class="flex flex-wrap gap-2 py-1">
                        @foreach(\App\Models\User::clientTypeOptions() as $ctVal => $ctLabel)
                            <span class="kyc-pill {{ auth()->user()?->client_type === $ctVal ? 'on' : '' }}">{{ $ctLabel }}</span>
                        @endforeach
                    </div>
                </div>

                {{-- 2. Personal Information --}}
                <div class="mt-6">
                    <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                        <span class="kyc-secnum">2</span>
                        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Personal Information</h2>
                        <span class="np text-xs text-zinc-400">/ व्यक्तिगत विवरण</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-8">
                        @foreach([
                            ['Full Name', 'पूरा नाम', $k->full_name],
                            ['Father / Mother', 'बाबु/आमाको नाम', $k->father_mother_name],
                            ['Grandfather', 'बाजेको नाम', $k->grandfather_name],
                            ['Spouse', 'पति/पत्नीको नाम', $k->spouse_name],
                            ['Citizenship No.', 'नागरिकता नं.', $k->citizenship_no],
                            ['Date of Birth', 'जन्म मिति', optional($k->date_of_birth)->format('d M Y')],
                            ['Gender', 'लिङ्ग', $k->gender ? ucfirst($k->gender) : null],
                            ['Nationality', 'राष्ट्रियता', $k->nationality],
                            ['Occupation', 'पेशा', $k->occupation],
                        ] as [$label, $np, $value])
                            <div class="kyc-row"><div class="k">{{ $label }}<span class="np">{{ $np }}</span></div><div class="v {{ $value ? '' : 'blank' }}">{{ $value ?: 'Not provided' }}</div></div>
                        @endforeach
                    </div>
                </div>

                {{-- 3. Contact Details --}}
                <div class="mt-6">
                    <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                        <span class="kyc-secnum">3</span>
                        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Contact Details</h2>
                        <span class="np text-xs text-zinc-400">/ सम्पर्क विवरण</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-8">
                        @foreach([
                            ['Mobile No.', 'मोबाइल नं.', $k->mobile_no],
                            ['Alternate Contact', 'वैकल्पिक सम्पर्क', $k->alt_contact_no],
                            ['Telephone No.', 'टेलिफोन नं.', $k->telephone_no],
                            ['Email', 'इमेल', $k->email],
                            ['Permanent Address', 'स्थायी ठेगाना', $addressLine($k->permanent_tole, $k->permanent_ward_no, $k->permanent_municipality, $k->permanent_district, $k->permanent_province)],
                            ['Current Address', 'हालको ठेगाना', $addressLine($k->current_tole, $k->current_ward_no, $k->current_municipality, $k->current_district, $k->current_province)],
                        ] as [$label, $np, $value])
                            <div class="kyc-row"><div class="k">{{ $label }}<span class="np">{{ $np }}</span></div><div class="v {{ $value ? '' : 'blank' }}">{{ $value ?: 'Not provided' }}</div></div>
                        @endforeach
                    </div>
                </div>

                {{-- 4. Organization Details --}}
                <div class="mt-6">
                    <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                        <span class="kyc-secnum">4</span>
                        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Organization Details</h2>
                        <span class="np text-xs text-zinc-400">/ संस्था सम्बन्धी विवरण</span>
                        <span class="ml-auto text-[10px] italic text-zinc-400">If applicable</span>
                    </div>
                    @if($k->organization)
                        <div class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-8">
                            @foreach([
                                ['Organization Name', 'संस्थाको नाम', $k->organization->organization_name],
                                ['Registration No.', 'दर्ता नं.', $k->organization->registration_no],
                                ['PAN / VAT No.', 'प्यान/भ्याट नं.', $k->organization->pan_vat_no],
                                ['Authorized Person', 'अख्तियार प्राप्त व्यक्ति', $k->organization->authorized_person],
                                ['Designation', 'पद', $k->organization->designation],
                                ['Office Address', 'कार्यालयको ठेगाना', $k->organization->office_address],
                            ] as [$label, $np, $value])
                                <div class="kyc-row"><div class="k">{{ $label }}<span class="np">{{ $np }}</span></div><div class="v {{ $value ? '' : 'blank' }}">{{ $value ?: 'Not provided' }}</div></div>
                            @endforeach
                        </div>
                    @else
                        <p class="py-1 text-sm italic text-zinc-400">Not applicable — individual applicant.</p>
                    @endif
                </div>

                {{-- 5. Property Requirement Details --}}
                <div class="mt-6">
                    <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                        <span class="kyc-secnum">5</span>
                        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Property Requirement Details</h2>
                        <span class="np text-xs text-zinc-400">/ सम्पत्ति आवश्यकता</span>
                        <span class="ml-auto text-[10px] italic text-zinc-400">For Buyer / Investor / Tenant</span>
                    </div>
                    @php $req = $k->propertyRequirement; @endphp
                    @if($req)
                        <div class="space-y-2 py-1">
                            <div>
                                <div class="mb-1 text-xs text-zinc-500">Purpose <span class="np">/ उद्देश्य</span></div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['purchase' => 'Purchase', 'investment' => 'Investment', 'rent' => 'Rent'] as $val => $label)
                                        <span class="kyc-pill {{ in_array($val, (array) $req->purpose, true) ? 'on' : '' }}">{{ $label }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs text-zinc-500">Property Type <span class="np">/ सम्पत्तिको प्रकार</span></div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['land' => 'Land', 'house' => 'House', 'apartment' => 'Apartment', 'commercial' => 'Commercial', 'other' => 'Other'] as $val => $label)
                                        <span class="kyc-pill {{ in_array($val, (array) $req->property_type, true) ? 'on' : '' }}">{{ $label }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-8">
                            @foreach([
                                ['Preferred Location', 'रुचाइएको स्थान', $req->preferred_location],
                                ['Required Area', 'आवश्यक क्षेत्रफल', $req->required_area],
                                ['Estimated Budget', 'अनुमानित बजेट', $req->estimated_budget ? 'Rs. '.number_format((float) $req->estimated_budget, 2) : null],
                                ['Timeline', 'समयसीमा', $req->purchase_timeline],
                            ] as [$label, $np, $value])
                                <div class="kyc-row"><div class="k">{{ $label }}<span class="np">{{ $np }}</span></div><div class="v {{ $value ? '' : 'blank' }}">{{ $value ?: 'Not provided' }}</div></div>
                            @endforeach
                        </div>
                    @else
                        <p class="py-1 text-sm italic text-zinc-400">Not provided.</p>
                    @endif
                </div>

                {{-- 7. Required Service Selection --}}
                <div class="mt-6">
                    <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                        <span class="kyc-secnum">7</span>
                        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Required Service Selection</h2>
                        <span class="np text-xs text-zinc-400">/ आवश्यक सेवा छनोट</span>
                    </div>
                    @php $selectedServiceIds = $k->serviceRequests->pluck('service_type_id')->all(); @endphp
                    <div class="grid grid-cols-1 gap-2 py-1 sm:grid-cols-2">
                        @foreach(\App\Models\ServiceType::where('is_active', true)->get() as $svc)
                            <div class="kyc-pill justify-start {{ in_array($svc->service_type_id, $selectedServiceIds, true) ? 'on' : '' }}">
                                {{ $svc->service_name }}
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 8. Document Submission Checklist --}}
                <div class="mt-6">
                    <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                        <span class="kyc-secnum">8</span>
                        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Document Submission Checklist</h2>
                        <span class="np text-xs text-zinc-400">/ कागजात चेकलिस्ट</span>
                    </div>
                    <div class="divide-y divide-dashed divide-zinc-200 dark:divide-zinc-800">
                        @foreach($k->documents as $doc)
                            <div class="flex items-center justify-between py-1.5 text-sm">
                                <span class="text-zinc-700 dark:text-zinc-300">{{ $doc->docType?->doc_name ?? 'Document' }}</span>
                                @if($doc->status === 'submitted')
                                    <span class="rounded-full border border-emerald-300 bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-400">Submitted</span>
                                @else
                                    <span class="rounded-full border border-amber-300 bg-amber-50 px-2.5 py-0.5 text-[11px] font-semibold text-amber-700 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-400">Pending</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 9. Digital Registration Details --}}
                <div class="mt-6">
                    <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                        <span class="kyc-secnum">9</span>
                        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Digital Registration Details</h2>
                        <span class="np text-xs text-zinc-400">/ डिजिटल दर्ता विवरण</span>
                        <span class="ml-auto text-[10px] italic text-zinc-400">Completed by the verifying officer</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-8">
                        @foreach([
                            ['Client ID', 'ग्राहक आईडी', $k->digital_client_id],
                            ['Registration Date', 'दर्ता मिति', optional($k->verified_at)->format('d M Y')],
                            ['Registered By', 'दर्ता गर्ने', $k->verifiedBy?->full_name],
                            ['Mobile App User ID', 'मोबाइल एप प्रयोगकर्ता आईडी', $k->mobile_app_user_id],
                        ] as [$label, $np, $value])
                            <div class="kyc-row"><div class="k">{{ $label }}<span class="np">{{ $np }}</span></div><div class="v {{ $value ? '' : 'blank' }}">{{ $value ?: 'Pending verification' }}</div></div>
                        @endforeach
                    </div>
                </div>

                {{-- 10. Client Declaration --}}
                <div class="mt-6">
                    <div class="mb-2 flex items-baseline gap-2 border-b border-zinc-900 pb-1 dark:border-zinc-100">
                        <span class="kyc-secnum">10</span>
                        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Client Declaration</h2>
                        <span class="np text-xs text-zinc-400">/ ग्राहक घोषणा</span>
                    </div>
                    <div class="rounded border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-xs text-zinc-600 dark:border-zinc-800 dark:bg-zinc-900/40 dark:text-zinc-400">
                        <p>I hereby declare that the information and documents provided above are true and correct to the best of my knowledge, as required under Annex F of API GharJagga's client registration process.</p>
                        <p class="np mt-1.5">मैले माथि उल्लेखित विवरण र कागजातहरू साँचो र सही भएको घोषणा गर्दछु।</p>
                    </div>
                    @if($k->admin_note)
                        <div class="mt-2 rounded border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700 dark:border-rose-900/50 dark:bg-rose-950/20 dark:text-rose-300">
                            <strong>Review Note:</strong> {{ $k->admin_note }}
                        </div>
                    @endif
                </div>

                {{-- Signatures --}}
                <div class="mt-8 grid grid-cols-1 gap-6 border-t border-zinc-100 pt-4 sm:grid-cols-3 dark:border-zinc-800">
                    <div class="border-t border-zinc-800 pt-2 dark:border-zinc-200">
                        <h4 class="text-xs font-bold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">Applicant</h4>
                        <div class="mt-1 text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $k->full_name }}</div>
                        <div class="text-xs text-zinc-400">{{ optional($k->signature_date)->format('d M Y') ?: '—' }}</div>
                    </div>
                    <div class="border-t border-zinc-800 pt-2 dark:border-zinc-200">
                        <h4 class="text-xs font-bold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">Verified By</h4>
                        <div class="mt-1 text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $k->verifiedBy?->full_name ?? '—' }}</div>
                        <div class="text-xs text-zinc-400">{{ $k->verifiedBy?->designation }}{{ $k->verified_at ? ' · '.$k->verified_at->format('d M Y') : '' }}</div>
                    </div>
                    <div class="border-t border-zinc-800 pt-2 dark:border-zinc-200">
                        <h4 class="text-xs font-bold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">Approved By</h4>
                        <div class="mt-1 text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $k->approvedBy?->full_name ?? '—' }}</div>
                        <div class="text-xs text-zinc-400">{{ $k->approvedBy?->designation }}{{ $k->approved_at ? ' · '.$k->approved_at->format('d M Y') : '' }}</div>
                    </div>
                </div>

                <div class="mt-6 border-t border-zinc-100 pt-2 text-center text-[10px] text-zinc-400 dark:border-zinc-800">
                    &copy; Api Ghar Jagga | AGJ-FRM-07 | This is a system-generated Annex-F KYC record.
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
