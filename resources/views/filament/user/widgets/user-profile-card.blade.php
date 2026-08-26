@php
    $user = auth()->user();
    $initials = strtoupper(substr($user->name ?? 'U', 0, 2));
    $accountNo = str_pad($user->id ?? 1, 5, '0', STR_PAD_LEFT);
    $kycStatus = $user?->kycVerification?->status ?? 'unsubmitted';
    $isVerified = $kycStatus === 'approved';
@endphp

<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm text-center" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; text-align: center;">
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <!-- Large Avatar with Badge (Strict Circular Container) -->
        <div style="position: relative; width: 68px; height: 68px; display: inline-block; margin: 0 auto;">
            <div style="width: 68px; height: 68px; min-width: 68px; min-height: 68px; max-width: 68px; max-height: 68px; border-radius: 50%; background-color: #2563eb; color: #ffffff; font-size: 1.35rem; font-weight: 700; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(37,99,235,0.2);">
                {{ $initials }}
            </div>
            <span style="position: absolute; bottom: 0; right: 0; width: 20px; height: 20px; border-radius: 50%; background-color: {{ $isVerified ? '#10b981' : '#3b82f6' }}; color: #ffffff; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                {{ $isVerified ? '✓' : '1' }}
            </span>
        </div>

        <!-- User Info -->
        <h3 class="mt-4 text-base font-bold text-slate-900" style="margin-top: 1rem; font-size: 1.05rem; font-weight: 700; color: #0f172a;">
            {{ $user->name ?? 'User' }}
        </h3>
        <p class="mt-1 text-xs text-slate-500 font-normal" style="margin-top: 0.25rem; font-size: 0.75rem; color: #64748b;">
            {{ $user->email ?? 'user@example.com' }} &bull; Account #{{ $accountNo }}
        </p>

        <!-- KYC Status Pill -->
        <div style="margin-top: 0.75rem;">
            @if($isVerified)
                <span style="display: inline-flex; align-items: center; gap: 0.375rem; border-radius: 9999px; border: 1px solid #a7f3d0; background-color: #ecfdf5; padding: 0.2rem 0.75rem; font-size: 0.75rem; font-weight: 600; color: #047857;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #10b981;"></span>
                    KYC Verified
                </span>
            @elseif($kycStatus === 'pending')
                <span style="display: inline-flex; align-items: center; gap: 0.375rem; border-radius: 9999px; border: 1px solid #fde68a; background-color: #fef3c7; padding: 0.2rem 0.75rem; font-size: 0.75rem; font-weight: 600; color: #92400e;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #f59e0b;"></span>
                    KYC Under Review
                </span>
            @elseif($kycStatus === 'rejected')
                <span style="display: inline-flex; align-items: center; gap: 0.375rem; border-radius: 9999px; border: 1px solid #fecdd3; background-color: #ffe4e6; padding: 0.2rem 0.75rem; font-size: 0.75rem; font-weight: 600; color: #be123c;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #e11d48;"></span>
                    KYC Action Required
                </span>
            @else
                <span style="display: inline-flex; align-items: center; gap: 0.375rem; border-radius: 9999px; border: 1px solid #f1f5f9; background-color: #f8fafc; padding: 0.2rem 0.75rem; font-size: 0.75rem; font-weight: 600; color: #64748b;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #94a3b8;"></span>
                    KYC Unverified
                </span>
            @endif
        </div>
    </div>
</div>
