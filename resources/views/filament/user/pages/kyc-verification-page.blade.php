<x-filament-panels::page>
    <div class="space-y-6">

        <!-- Status Notification Banner -->
        @if($kycRecord?->status === 'approved')
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-5 dark:border-emerald-900/50 dark:bg-emerald-950/30">
                <div class="flex items-center gap-3.5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm shrink-0">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-emerald-900 dark:text-emerald-200">KYC Verification Completed & Approved</h3>
                        <p class="text-xs text-emerald-700 dark:text-emerald-400 mt-0.5">
                            Approved on {{ $kycRecord->reviewed_at?->format('d M Y, h:i A') ?? 'Verified Record' }}. Your identity record is fully registered under Annex F standards.
                        </p>
                    </div>
                </div>
            </div>
        @elseif($kycRecord?->status === 'pending')
            <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-5 dark:border-amber-900/50 dark:bg-amber-950/30">
                <div class="flex items-center gap-3.5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-600 text-white animate-pulse shrink-0">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-amber-900 dark:text-amber-200">Documents Under Review</h3>
                        <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">
                            Submitted on {{ $kycRecord->submitted_at?->format('d M Y, h:i A') ?? 'Recent' }}. Verification officers are currently validating your details. Editing is paused during review.
                        </p>
                    </div>
                </div>
            </div>
        @elseif($kycRecord?->status === 'rejected')
            <div class="rounded-2xl border border-rose-200 bg-rose-50/80 p-5 dark:border-rose-900/50 dark:bg-rose-950/30">
                <div class="flex items-start gap-3.5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-600 text-white shrink-0 mt-0.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="space-y-1.5 flex-1">
                        <h3 class="text-sm font-bold text-rose-900 dark:text-rose-200">Verification Corrections Requested</h3>
                        <p class="text-xs text-rose-700 dark:text-rose-400">
                            Please review the administrator remarks below, make the necessary corrections to your form or re-upload clearer document photos, then click <strong>Resubmit KYC Application</strong>.
                        </p>
                        @if($kycRecord->admin_note)
                            <div class="mt-2 rounded-xl bg-white p-3.5 text-xs text-rose-950 dark:bg-[#121216] dark:text-rose-200 border border-rose-200 dark:border-rose-900/50">
                                <strong>Admin Remarks:</strong> {{ $kycRecord->admin_note }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-[#0c0c0f]">
                <div class="flex items-center gap-3.5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white shrink-0">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Annex F Client Registration Form</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Please provide accurate personal and address details matching your official Government-issued citizenship or National ID.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Interactive Form -->
        <form wire:submit="submit" class="space-y-6">
            {{ $this->form }}

            @if($kycRecord?->status !== 'approved' && $kycRecord?->status !== 'pending')
                <div class="flex items-center justify-end gap-3 pt-5 border-t border-zinc-200 dark:border-zinc-800">
                    <x-filament::button type="submit" size="lg" icon="heroicon-m-check">
                        {{ $kycRecord?->status === 'rejected' ? 'Resubmit KYC Application' : 'Submit KYC for Verification' }}
                    </x-filament::button>
                </div>
            @endif
        </form>

    </div>
</x-filament-panels::page>
