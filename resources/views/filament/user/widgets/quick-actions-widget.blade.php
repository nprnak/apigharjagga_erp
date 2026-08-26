@php
    $kycStatus = $kycStatus ?? 'unsubmitted';
    $propertyCount = $propertyCount ?? 0;
    $canList = $canList ?? false;
@endphp

<div
    class="h-full"
    x-data="{
        hovered: null,
        actions: ['kyc', 'list', 'browse', 'profile']
    }"
>
    <div class="relative h-full overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800/80 dark:bg-[#0c0c0f]">
        <div class="pointer-events-none absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-indigo-500/5 blur-3xl dark:bg-indigo-500/10"></div>

        <div class="relative z-10 flex h-full flex-col">
            <div class="mb-4">
                <h3 class="text-sm font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Quick Actions</h3>
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Jump to common tasks in one click</p>
            </div>

            <div class="grid flex-1 grid-cols-2 gap-3">
                {{-- KYC Action --}}
                <a
                    href="{{ url('/dashboard/kyc-verification-page') }}"
                    @mouseenter="hovered = 'kyc'"
                    @mouseleave="hovered = null"
                    class="interactive-card group relative flex flex-col justify-between overflow-hidden rounded-xl border p-4 transition-all duration-300
                           {{ $kycStatus === 'approved'
                              ? 'border-emerald-200/60 bg-emerald-50/50 dark:border-emerald-900/40 dark:bg-emerald-950/20'
                              : 'border-blue-200/60 bg-blue-50/50 dark:border-blue-900/40 dark:bg-blue-950/20' }}"
                >
                    <div class="absolute inset-0 bg-gradient-to-br from-transparent to-blue-500/5 opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-500/25 transition-transform duration-300 group-hover:scale-110">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100">KYC Verification</p>
                        <p class="mt-1 text-[10px] leading-relaxed text-zinc-500 dark:text-zinc-400">
                            @if($kycStatus === 'approved')
                                Identity verified
                            @elseif($kycStatus === 'pending')
                                Review in progress
                            @else
                                Complete Annex F form
                            @endif
                        </p>
                    </div>
                    <span class="relative mt-3 inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 dark:text-blue-400">
                        Open
                        <svg class="h-3 w-3 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </a>

                {{-- Add Listing --}}
                <a
                    href="{{ url('/dashboard/my-properties/create') }}"
                    @mouseenter="hovered = 'list'"
                    @mouseleave="hovered = null"
                    class="interactive-card group relative flex flex-col justify-between overflow-hidden rounded-xl border p-4 transition-all duration-300
                           {{ $canList
                              ? 'border-emerald-200/60 bg-emerald-50/50 dark:border-emerald-900/40 dark:bg-emerald-950/20'
                              : 'border-zinc-200/60 bg-zinc-50/50 opacity-75 dark:border-zinc-800 dark:bg-zinc-900/30' }}"
                    @if(!$canList) aria-disabled="true" @endif
                >
                    <div class="relative">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl {{ $canList ? 'bg-emerald-600 shadow-emerald-500/25' : 'bg-zinc-400 dark:bg-zinc-600' }} text-white shadow-lg transition-transform duration-300 group-hover:scale-110">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Add Listing</p>
                        <p class="mt-1 text-[10px] leading-relaxed text-zinc-500 dark:text-zinc-400">
                            {{ $canList ? "{$propertyCount} submitted so far" : 'Requires KYC approval' }}
                        </p>
                    </div>
                    <span class="relative mt-3 inline-flex items-center gap-1 text-[10px] font-semibold {{ $canList ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-400' }}">
                        {{ $canList ? 'Create' : 'Locked' }}
                        @if($canList)
                            <svg class="h-3 w-3 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        @else
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        @endif
                    </span>
                </a>

                {{-- Browse Marketplace --}}
                <a
                    href="{{ url('/properties') }}"
                    target="_blank"
                    @mouseenter="hovered = 'browse'"
                    @mouseleave="hovered = null"
                    class="interactive-card group relative flex flex-col justify-between overflow-hidden rounded-xl border border-indigo-200/60 bg-indigo-50/50 p-4 transition-all duration-300 dark:border-indigo-900/40 dark:bg-indigo-950/20"
                >
                    <div class="relative">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-500/25 transition-transform duration-300 group-hover:scale-110">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Browse Marketplace</p>
                        <p class="mt-1 text-[10px] leading-relaxed text-zinc-500 dark:text-zinc-400">Explore listed properties across Nepal</p>
                    </div>
                    <span class="relative mt-3 inline-flex items-center gap-1 text-[10px] font-semibold text-indigo-600 dark:text-indigo-400">
                        View listings
                        <svg class="h-3 w-3 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </span>
                </a>

                {{-- My Properties --}}
                <a
                    href="{{ url('/dashboard/my-properties') }}"
                    @mouseenter="hovered = 'profile'"
                    @mouseleave="hovered = null"
                    class="interactive-card group relative flex flex-col justify-between overflow-hidden rounded-xl border border-violet-200/60 bg-violet-50/50 p-4 transition-all duration-300 dark:border-violet-900/40 dark:bg-violet-950/20"
                >
                    <div class="relative">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-violet-600 text-white shadow-lg shadow-violet-500/25 transition-transform duration-300 group-hover:scale-110">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Manage Listings</p>
                        <p class="mt-1 text-[10px] leading-relaxed text-zinc-500 dark:text-zinc-400">View and edit all your property records</p>
                    </div>
                    <span class="relative mt-3 inline-flex items-center gap-1 text-[10px] font-semibold text-violet-600 dark:text-violet-400">
                        Open table
                        <svg class="h-3 w-3 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>
