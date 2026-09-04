@php
    $kycStatus = auth()->user()?->kycVerification?->status ?? 'unsubmitted';
    $isVerified = $kycStatus === 'approved';
    $valuationRequestCount = auth()->user()?->properties()
        ->whereHas('valuationRequests')
        ->count() ?? 0;
@endphp

<div class="dash-card" style="height: 100%;">
    <div style="margin-bottom: 1rem;">
        <h3 style="font-size: 0.875rem; font-weight: 700; color: #0f172a; margin: 0;">Quick Actions</h3>
        <p style="margin-top: 0.15rem; font-size: 0.75rem; color: #64748b; margin-bottom: 0;">Jump to common tasks in one click</p>
    </div>

    <div class="dashboard-quick-actions-grid">
        <!-- 1. Submit KYC -->
        <a href="{{ url('/dashboard/kyc-verification-page') }}"
           style="display: flex; flex-direction: column; justify-content: space-between; border-radius: 0.75rem; border: 1px solid #f1f5f9; background-color: #f8fafc; padding: 0.875rem; text-decoration: none; transition: all 0.2s;">
            <div>
                <div style="width: 32px; height: 32px; border-radius: 0.5rem; background-color: #dbeafe; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h4 style="margin-top: 0.5rem; font-size: 0.75rem; font-weight: 700; color: #0f172a; line-height: 1.2;">KYC Verification</h4>
                <p style="margin-top: 0.25rem; font-size: 0.65rem; color: #64748b; line-height: 1.2;">
                    {{ $isVerified ? 'Fully Verified' : ($kycStatus === 'pending' ? 'Under Review' : 'Complete Annex F') }}
                </p>
            </div>
            <span style="margin-top: 0.5rem; font-size: 0.65rem; font-weight: 600; color: #2563eb; display: inline-flex; align-items: center; gap: 2px;">
                Open &rarr;
            </span>
        </a>

        <!-- 2. My Properties -->
        <a href="{{ url('/dashboard/my-properties') }}"
           style="display: flex; flex-direction: column; justify-content: space-between; border-radius: 0.75rem; border: 1px solid #f1f5f9; background-color: #f8fafc; padding: 0.875rem; text-decoration: none; transition: all 0.2s;">
            <div>
                <div style="width: 32px; height: 32px; border-radius: 0.5rem; background-color: #d1fae5; color: #059669; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h4 style="margin-top: 0.5rem; font-size: 0.75rem; font-weight: 700; color: #0f172a; line-height: 1.2;">My Properties</h4>
                <p style="margin-top: 0.25rem; font-size: 0.65rem; color: #64748b; line-height: 1.2;">Manage listings</p>
            </div>
            <span style="margin-top: 0.5rem; font-size: 0.65rem; font-weight: 600; color: #059669; display: inline-flex; align-items: center; gap: 2px;">
                View &rarr;
            </span>
        </a>

        <!-- 3. Add Property (Guarded by KYC) -->
        <a href="{{ $isVerified ? url('/dashboard/my-properties/create') : url('/dashboard/kyc-verification-page') }}"
           style="display: flex; flex-direction: column; justify-content: space-between; border-radius: 0.75rem; border: 1px solid #f1f5f9; background-color: #f8fafc; padding: 0.875rem; text-decoration: none; transition: all 0.2s;">
            <div>
                <div style="width: 32px; height: 32px; border-radius: 0.5rem; background-color: {{ $isVerified ? '#e0e7ff' : '#fee2e2' }}; color: {{ $isVerified ? '#4f46e5' : '#dc2626' }}; display: flex; align-items: center; justify-content: center;">
                    @if($isVerified)
                        <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    @else
                        <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    @endif
                </div>
                <h4 style="margin-top: 0.5rem; font-size: 0.75rem; font-weight: 700; color: #0f172a; line-height: 1.2;">List Property</h4>
                <p style="margin-top: 0.25rem; font-size: 0.65rem; color: {{ $isVerified ? '#64748b' : '#dc2626' }}; line-height: 1.2;">
                    {{ $isVerified ? 'Submit new listing' : 'KYC Required to list' }}
                </p>
            </div>
            <span style="margin-top: 0.5rem; font-size: 0.65rem; font-weight: 600; color: {{ $isVerified ? '#4f46e5' : '#dc2626' }}; display: inline-flex; align-items: center; gap: 2px;">
                {{ $isVerified ? 'Create →' : 'Verify KYC →' }}
            </span>
        </a>

        <!-- 4. Client Agreements -->
        <a href="{{ url('/agreement') }}"
           style="display: flex; flex-direction: column; justify-content: space-between; border-radius: 0.75rem; border: 1px solid #f1f5f9; background-color: #f8fafc; padding: 0.875rem; text-decoration: none; transition: all 0.2s;">
            <div>
                <div style="width: 32px; height: 32px; border-radius: 0.5rem; background-color: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h4 style="margin-top: 0.5rem; font-size: 0.75rem; font-weight: 700; color: #0f172a; line-height: 1.2;">Agreements</h4>
                <p style="margin-top: 0.25rem; font-size: 0.65rem; color: #64748b; line-height: 1.2;">Annex legal forms</p>
            </div>
            <span style="margin-top: 0.5rem; font-size: 0.65rem; font-weight: 600; color: #d97706; display: inline-flex; align-items: center; gap: 2px;">
                Open &rarr;
            </span>
        </a>

        <!-- 5. Valuation Requests -->
        <a href="{{ $isVerified ? url('/dashboard/my-valuation-requests/create') : url('/dashboard/kyc-verification-page') }}"
           style="display: flex; flex-direction: column; justify-content: space-between; border-radius: 0.75rem; border: 1px solid #f1f5f9; background-color: #f8fafc; padding: 0.875rem; text-decoration: none; transition: all 0.2s;">
            <div>
                <div style="width: 32px; height: 32px; border-radius: 0.5rem; background-color: {{ $isVerified ? '#ede9fe' : '#fee2e2' }}; color: {{ $isVerified ? '#7c3aed' : '#dc2626' }}; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m-6 4h6m-6 4h3m8-8v10a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2h8l5 5z" />
                    </svg>
                </div>
                <h4 style="margin-top: 0.5rem; font-size: 0.75rem; font-weight: 700; color: #0f172a; line-height: 1.2;">Property Valuation</h4>
                <p style="margin-top: 0.25rem; font-size: 0.65rem; color: {{ $isVerified ? '#64748b' : '#dc2626' }}; line-height: 1.2;">
                    {{ $isVerified ? "{$valuationRequestCount} request(s) submitted" : 'KYC Required to request' }}
                </p>
            </div>
            <span style="margin-top: 0.5rem; font-size: 0.65rem; font-weight: 600; color: {{ $isVerified ? '#7c3aed' : '#dc2626' }}; display: inline-flex; align-items: center; gap: 2px;">
                {{ $isVerified ? 'Request / Track →' : 'Verify KYC →' }}
            </span>
        </a>
    </div>
</div>
