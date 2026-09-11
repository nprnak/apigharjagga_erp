<x-filament-panels::page>
    @php
        $k = $kycRecord;
        $statusMeta = [
            'approved' => ['label' => 'Approved', 'dot' => 'bg-emerald-500', 'text' => 'text-emerald-700 dark:text-emerald-400'],
            'verified' => ['label' => 'Verified — Awaiting Approval', 'dot' => 'bg-sky-500', 'text' => 'text-sky-700 dark:text-sky-400'],
            'pending' => ['label' => 'Submitted — Pending Review', 'dot' => 'bg-amber-500', 'text' => 'text-amber-700 dark:text-amber-400'],
            'rejected' => ['label' => 'Needs Correction', 'dot' => 'bg-rose-500', 'text' => 'text-rose-700 dark:text-rose-400'],
        ];
        $meta = $statusMeta[$k?->status] ?? ['label' => 'Not Submitted', 'dot' => 'bg-zinc-400', 'text' => 'text-zinc-500 dark:text-zinc-400'];
        $isLocked = $k && in_array($k->status, ['pending', 'verified', 'approved'], true);
        $stageOrder = ['pending' => 1, 'verified' => 2, 'approved' => 3];
        $currentStage = $stageOrder[$k?->status] ?? 0;

        $addressLine = fn ($tole, $ward, $muni, $dist, $prov) => collect([$tole, $ward ? 'Ward '.$ward : null, $muni, $dist, $prov])->filter()->implode(', ') ?: '—';
    @endphp

    <style>
        @media print {
            .no-print { display: none !important; }
            .fi-sidebar, .fi-topbar { display: none !important; }
        }
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
            {{-- One consolidated, printable page — everything submitted, in one place --}}
            <div id="kyc-summary" class="space-y-5 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-[#0c0c0f]">

                <div>
                    <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">1–3. Personal Information & Contact <span class="font-normal text-zinc-400">/ व्यक्तिगत तथा सम्पर्क विवरण</span></h2>
                    <dl class="mt-3 grid grid-cols-1 gap-x-8 gap-y-2 sm:grid-cols-2 text-sm">
                        <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Full Name</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->full_name ?: '—' }}</dd></div>
                        <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Father / Mother</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->father_mother_name ?: '—' }}</dd></div>
                        <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Grandfather</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->grandfather_name ?: '—' }}</dd></div>
                        <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Spouse</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->spouse_name ?: '—' }}</dd></div>
                        <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Citizenship No.</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->citizenship_no ?: '—' }}</dd></div>
                        <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Date of Birth</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ optional($k->date_of_birth)->format('d M Y') ?: '—' }}</dd></div>
                        <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Gender</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->gender ? ucfirst($k->gender) : '—' }}</dd></div>
                        <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Nationality</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->nationality ?: '—' }}</dd></div>
                        <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Occupation</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->occupation ?: '—' }}</dd></div>
                        <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Mobile</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->mobile_no ?: '—' }}</dd></div>
                        <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Alternate Contact</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->alt_contact_no ?: '—' }}</dd></div>
                        <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Telephone</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->telephone_no ?: '—' }}</dd></div>
                        <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Email</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->email ?: '—' }}</dd></div>
                    </dl>
                </div>

                <div>
                    <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Address <span class="font-normal text-zinc-400">/ ठेगाना</span></h2>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500 shrink-0 mr-4">Permanent</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200 text-right">{{ $addressLine($k->permanent_tole, $k->permanent_ward_no, $k->permanent_municipality, $k->permanent_district, $k->permanent_province) }}</dd></div>
                        <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500 shrink-0 mr-4">Current</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200 text-right">{{ $addressLine($k->current_tole, $k->current_ward_no, $k->current_municipality, $k->current_district, $k->current_province) }}</dd></div>
                    </dl>
                </div>

                @if($k->organization)
                    <div>
                        <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">4. Organization Details <span class="font-normal text-zinc-400">/ संस्था विवरण</span></h2>
                        <dl class="mt-3 grid grid-cols-1 gap-x-8 gap-y-2 sm:grid-cols-2 text-sm">
                            <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Organization</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->organization->organization_name ?: '—' }}</dd></div>
                            <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Registration No.</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->organization->registration_no ?: '—' }}</dd></div>
                            <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">PAN / VAT No.</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->organization->pan_vat_no ?: '—' }}</dd></div>
                            <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Authorized Person</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->organization->authorized_person ?: '—' }}</dd></div>
                            <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Designation</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->organization->designation ?: '—' }}</dd></div>
                            <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Office Address</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->organization->office_address ?: '—' }}</dd></div>
                        </dl>
                    </div>
                @endif

                @if($k->propertyRequirement)
                    <div>
                        <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">5. Property Requirement Details <span class="font-normal text-zinc-400">/ सम्पत्ति आवश्यकता</span></h2>
                        <dl class="mt-3 grid grid-cols-1 gap-x-8 gap-y-2 sm:grid-cols-2 text-sm">
                            <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Purpose</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->propertyRequirement->purpose ? ucfirst($k->propertyRequirement->purpose) : '—' }}</dd></div>
                            <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Property Type</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->propertyRequirement->property_type ? ucfirst($k->propertyRequirement->property_type) : '—' }}</dd></div>
                            <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Preferred Location</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->propertyRequirement->preferred_location ?: '—' }}</dd></div>
                            <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Required Area</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->propertyRequirement->required_area ?: '—' }}</dd></div>
                            <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Estimated Budget</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->propertyRequirement->estimated_budget ? 'Rs. '.number_format((float) $k->propertyRequirement->estimated_budget, 2) : '—' }}</dd></div>
                            <div class="flex justify-between border-b border-dashed border-zinc-200 py-1 dark:border-zinc-800"><dt class="text-zinc-500">Timeline</dt><dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $k->propertyRequirement->purchase_timeline ?: '—' }}</dd></div>
                        </dl>
                    </div>
                @endif

                @if($k->serviceRequests->isNotEmpty())
                    <div>
                        <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">7. Required Service Selection <span class="font-normal text-zinc-400">/ आवश्यक सेवा छनोट</span></h2>
                        <ul class="mt-3 flex flex-wrap gap-2 text-sm">
                            @foreach($k->serviceRequests as $sr)
                                <li class="rounded-full border border-zinc-200 px-3 py-1 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">{{ $sr->serviceType?->service_name ?? 'Service' }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Document Checklist <span class="font-normal text-zinc-400">/ कागजात सूची</span></h2>
                    <ul class="mt-3 space-y-1.5 text-sm">
                        @foreach($k->documents as $doc)
                            <li class="flex items-center gap-2">
                                <span class="{{ $doc->status === 'submitted' ? 'text-emerald-600' : 'text-zinc-400' }}">{{ $doc->status === 'submitted' ? '✓' : '○' }}</span>
                                <span class="text-zinc-700 dark:text-zinc-300">{{ $doc->docType?->doc_name ?? 'Document' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="grid grid-cols-1 gap-6 border-t border-zinc-100 pt-4 text-sm sm:grid-cols-3 dark:border-zinc-800">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-400">Submitted</div>
                        <div class="mt-1 text-zinc-700 dark:text-zinc-300">{{ optional($k->submitted_at)->format('d M Y, h:i A') ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-400">Verified By</div>
                        <div class="mt-1 text-zinc-700 dark:text-zinc-300">{{ $k->verifiedBy?->full_name ?? '—' }}{{ $k->verified_at ? ' · '.$k->verified_at->format('d M Y') : '' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-400">Approved By</div>
                        <div class="mt-1 text-zinc-700 dark:text-zinc-300">{{ $k->approvedBy?->full_name ?? '—' }}{{ $k->approved_at ? ' · '.$k->approved_at->format('d M Y') : '' }}</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
