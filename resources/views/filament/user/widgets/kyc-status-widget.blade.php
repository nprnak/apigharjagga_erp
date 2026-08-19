@php
    $user = auth()->user();
    $status = $kyc?->status ?? 'unsubmitted';

    $step = match ($status) {
        'approved' => 4,
        'pending'  => 3,
        'rejected' => 2,
        default    => 1,
    };

    $meta = match ($status) {
        'approved' => [
            'accent' => 'from-emerald-500 via-emerald-500 to-teal-500',
            'dot' => 'bg-emerald-500',
            'ring' => 'ring-emerald-500/20',
        ],
        'pending' => [
            'accent' => 'from-amber-400 via-amber-500 to-orange-500',
            'dot' => 'bg-amber-500',
            'ring' => 'ring-amber-500/20',
        ],
        'rejected' => [
            'accent' => 'from-rose-500 via-rose-500 to-red-500',
            'dot' => 'bg-rose-500',
            'ring' => 'ring-rose-500/20',
        ],
        default => [
            'accent' => 'from-blue-600 via-blue-600 to-indigo-600',
            'dot' => 'bg-blue-600',
            'ring' => 'ring-blue-500/20',
        ],
    };
@endphp

<div class="space-y-4">
    <!-- Main Welcome & KYC Card -->
    <div class="dashboard-card-enter relative overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm transition-shadow duration-300 hover:shadow-md dark:border-zinc-800/80 dark:bg-[#0c0c0f] dark:hover:shadow-blue-900/10">

        <!-- Status accent bar -->
        <div class="h-1.5 w-full bg-gradient-to-r {{ $meta['accent'] }}"></div>

        <!-- Subtle gradient background glow in top right -->
        <div class="pointer-events-none absolute -right-16 -top-12 h-48 w-48 rounded-full bg-blue-500/5 blur-3xl dark:bg-blue-500/10"></div>

        <div class="relative z-10 p-6">

            <!-- User Identity -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                <div class="relative shrink-0">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-base font-bold text-white shadow-lg shadow-blue-500/25 ring-4 {{ $meta['ring'] }}">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <!-- Status indicator on avatar -->
                    <span class="absolute -bottom-1.5 -right-1.5 flex h-6 w-6 items-center justify-center rounded-full border-2 border-white shadow-sm dark:border-[#0c0c0f] {{ $meta['dot'] }}">
                        @if($status === 'approved')
                            <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        @elseif($status === 'pending')
                            <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3"/>
                            </svg>
                        @elseif($status === 'rejected')
                            <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        @else
                            <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01"/>
                            </svg>
                        @endif
                    </span>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                            {{ $user->name }}
                        </h2>
                        <!-- Live status indicator badge -->
                        @if($status === 'approved')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-950/60 dark:text-emerald-300 dark:ring-emerald-500/30">
                                <svg class="h-3 w-3 fill-emerald-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg>
                                KYC Verified
                            </span>
                        @elseif($status === 'pending')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-950/60 dark:text-amber-300 dark:ring-amber-500/30">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                                Under Review
                            </span>
                        @elseif($status === 'rejected')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-700 ring-1 ring-inset ring-rose-600/20 dark:bg-rose-950/60 dark:text-rose-300 dark:ring-rose-500/30">
                                <svg class="h-3 w-3 fill-rose-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg>
                                Needs Amendment
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-semibold text-zinc-700 ring-1 ring-inset ring-zinc-300/50 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-zinc-700">
                                <svg class="h-3 w-3 fill-zinc-400" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg>
                                Unverified
                            </span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $user->email }}
                        <span class="mx-1.5 text-zinc-300 dark:text-zinc-700">&bull;</span>
                        Account #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                        <span class="mx-1.5 text-zinc-300 dark:text-zinc-700">&bull;</span>
                        Nepal
                    </p>

                    <!-- Explanation Message -->
                    <p class="mt-3 max-w-2xl text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">
                        @if($status === 'approved')
                            Your identity has been fully verified according to <strong class="font-semibold text-zinc-900 dark:text-zinc-100">Annex F standards</strong>. You have full privileges to list properties, receive direct buyer inquiries, and generate legal deed paperwork.
                        @elseif($status === 'pending')
                            Your registration details and identity documents are currently being checked by our compliance team. Verification is typically completed within 24 business hours.
                        @elseif($status === 'rejected')
                            Your verification was reviewed and requires corrections before approval. Please read the note below and update your documents.
                        @else
                            Complete your <strong class="font-semibold text-zinc-900 dark:text-zinc-100">Annex F Client KYC Registration</strong> to unlock property listings, buyer leads, and verified ownership badges across Nepal.
                        @endif
                    </p>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="mt-5 flex flex-col gap-2.5 border-t border-zinc-100 pt-5 dark:border-zinc-800/80 sm:flex-row sm:items-center sm:gap-3">
                <a href="{{ url('/dashboard/kyc-verification-page') }}"
                   class="group inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold shadow-sm transition duration-200
                          {{ $status === 'approved'
                             ? 'bg-zinc-100 text-zinc-800 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700'
                             : 'bg-blue-600 text-white shadow-blue-500/20 hover:bg-blue-500 hover:shadow-blue-500/30' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    @if($status === 'approved')
                        View Verified KYC
                    @elseif($status === 'pending')
                        Check Review Status
                    @elseif($status === 'rejected')
                        Resubmit KYC Application
                    @else
                        Complete KYC Now
                    @endif
                    <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="{{ url('/dashboard/my-properties/create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-xs font-semibold text-zinc-800 transition duration-200 hover:border-zinc-400 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-200 dark:hover:bg-zinc-800">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Property Listing
                </a>

                @if($status === 'pending')
                    <span class="inline-flex items-center gap-1.5 text-xs text-zinc-400 dark:text-zinc-500 sm:ml-auto">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Usually completed within 24 hours
                    </span>
                @endif
            </div>

            <!-- Rejection Notice Banner -->
            @if($status === 'rejected' && $kyc?->admin_note)
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50/90 p-4 dark:border-rose-900/50 dark:bg-rose-950/40">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 shrink-0 mt-0.5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-rose-900 dark:text-rose-200">Correction Required from Administrator</h4>
                            <p class="mt-1 text-xs leading-relaxed text-rose-800 dark:text-rose-300">
                                "{{ $kyc->admin_note }}"
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Progress Steps Tracker -->
            <div class="mt-6 border-t border-zinc-100 pt-5 dark:border-zinc-800/80">
                <div class="mb-4 flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Verification Progress</p>
                    <span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">{{ min($step, 4) }} / 4 steps</span>
                </div>

                {{-- Animated progress bar --}}
                <div class="mb-6 h-1.5 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <div
                        class="h-full rounded-full bg-gradient-to-r from-blue-600 to-indigo-500 transition-all duration-1000 ease-out"
                        style="width: {{ ($step / 4) * 100 }}%"
                    ></div>
                </div>

                @php
                    $steps = [
                        ['label' => 'Personal Info', 'desc' => 'Name & Family', 'n' => 1],
                        ['label' => 'Residence', 'desc' => 'Permanent & Current', 'n' => 2],
                        ['label' => 'Verification Docs', 'desc' => 'ID Scan & Selfie', 'n' => 3],
                        ['label' => 'Officer Approval', 'desc' => $status === 'approved' ? 'Verified Member' : 'Pending Review', 'n' => 4],
                    ];
                @endphp

                <div class="grid grid-cols-2 gap-y-5 sm:grid-cols-4 sm:gap-x-3">
                    @foreach($steps as $index => $s)
                        @php
                            $done = $step >= $s['n'];
                            $isLast = $s['n'] === 4;
                        @endphp
                        <div class="group relative flex items-center gap-2.5 rounded-xl p-2 transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50 {{ !$isLast ? 'kyc-step-connector' : '' }} {{ $done && !$isLast ? 'kyc-step-connector--done' : '' }}">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold shadow-sm transition-transform duration-300 group-hover:scale-110
                                        {{ $done
                                           ? ($s['n'] === 4 ? 'bg-emerald-600 text-white shadow-emerald-500/30' : 'bg-blue-600 text-white shadow-blue-500/30')
                                           : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                @if($done)
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @else
                                    {{ $s['n'] }}
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-xs font-semibold text-zinc-900 dark:text-zinc-100">{{ $s['n'] }}. {{ $s['label'] }}</p>
                                <p class="truncate text-[10px] text-zinc-500 dark:text-zinc-400">{{ $s['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
