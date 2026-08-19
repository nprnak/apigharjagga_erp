@php
    $user = auth()->user();
    $kyc = $user?->kycVerification;
    $status = $kyc?->status ?? 'unsubmitted';
    $isVerified = $status === 'approved';
    $initials = strtoupper(substr($user->name ?? 'U', 0, 2));
@endphp

<div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md"
     style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem;">
    <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-center"
         style="display: flex; justify-content: space-between; align-items: center; gap: 1.5rem;">
        <!-- Left Banner Info -->
        <div class="space-y-3" style="flex: 1;">
            <div class="flex flex-wrap items-center gap-2.5" style="display: flex; align-items: center; gap: 0.625rem; flex-wrap: wrap;">
                <h2 class="text-xl font-bold tracking-tight text-slate-900" style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin: 0;">
                    Welcome back, {{ $user->name ?? 'User' }}! 👋
                </h2>
                @if($isVerified)
                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; border-radius: 9999px; border: 1px solid #a7f3d0; background-color: #ecfdf5; padding: 0.125rem 0.625rem; font-size: 0.75rem; font-weight: 600; color: #047857;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #10b981;"></span>
                        Account Verified
                    </span>
                @else
                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; border-radius: 9999px; border: 1px solid #fde68a; background-color: #fef3c7; padding: 0.125rem 0.625rem; font-size: 0.75rem; font-weight: 600; color: #92400e;">
                        Account Unverified
                    </span>
                @endif
            </div>

            <p style="margin-top: 0.5rem; font-size: 0.875rem; color: #475569; line-height: 1.5; max-width: 580px;">
                Complete your Annex F Client KYC Registration to unlock listing capabilities and connect with buyers.
            </p>

            <div style="margin-top: 0.75rem;">
                <a href="{{ url('/dashboard/kyc-verification-page') }}"
                   style="display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 0.5rem; background-color: #2563eb; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 600; color: #ffffff; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <span>Complete KYC Now</span>
                    <svg style="width: 1rem; height: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Right User Pill Preview (Guaranteed Circle) -->
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; border-radius: 0.75rem; background-color: #f8fafc; padding: 1rem 1.5rem; border: 1px solid #f1f5f9; min-width: 140px; flex-shrink: 0;">
            <div style="position: relative; width: 50px; height: 50px; display: inline-block;">
                <div style="width: 50px; height: 50px; min-width: 50px; min-height: 50px; border-radius: 50%; background-color: #2563eb; color: #ffffff; font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; justify-content: center;">
                    {{ $initials }}
                </div>
                <span style="position: absolute; bottom: -2px; right: -2px; width: 18px; height: 18px; border-radius: 50%; background-color: #3b82f6; color: #ffffff; font-size: 9px; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff;">
                    1
                </span>
            </div>
            <div style="margin-top: 0.5rem; text-align: center;">
                <p style="font-size: 0.875rem; font-weight: 600; color: #0f172a; margin: 0;">{{ $user->name ?? 'User' }}</p>
                <div style="margin-top: 0.25rem; display: flex; align-items: center; justify-content: center; gap: 0.25rem; font-size: 0.75rem; color: #64748b;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background-color: {{ $isVerified ? '#10b981' : '#94a3b8' }};"></span>
                    <span>{{ $isVerified ? 'Verified' : 'Unverified' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
