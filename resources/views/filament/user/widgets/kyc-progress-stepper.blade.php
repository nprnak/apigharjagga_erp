@php
    $user = auth()->user();
    $kyc = $user?->kycVerification;
    $status = $kyc?->status ?? 'unsubmitted';

    // Calculate step state
    $step1Done = true; // Registered
    $step2Done = !empty($kyc?->id_document_path) || in_array($status, ['approved', 'pending']);
    $step2Active = !$step2Done && $status !== 'approved';
    $step3Done = (!empty($kyc?->permanent_municipality) && !empty($kyc?->id_document_path)) || in_array($status, ['approved', 'pending']);
    $step3Active = $step2Done && !$step3Done && $status !== 'approved';
    $step4Done = $status === 'approved';
    $step4Active = $status === 'pending';

    $progress = match($status) {
        'approved' => 100,
        'pending' => 75,
        default => ($step2Done ? 50 : 25),
    };

    $nextStepText = match(true) {
        $status === 'approved' => 'All steps verified',
        $status === 'pending' => '4. Review pending',
        $status === 'rejected' => 'Corrections requested',
        $step2Done => '3. Ownership (Lalpurja)',
        default => '2. Citizenship/ID',
    };
@endphp

<div class="dash-card">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h3 style="font-size: 0.875rem; font-weight: 700; color: #0f172a; margin: 0;">Interactive KYC Progress</h3>
            <p style="margin-top: 0.15rem; font-size: 0.75rem; color: {{ $status === 'approved' ? '#059669' : '#64748b' }}; font-weight: 600; margin-bottom: 0;">
                {{ $status === 'approved' ? '✓ Fully Verified & Approved' : 'Next Step: ' . $nextStepText }}
            </p>
        </div>
        @if($status === 'approved')
            <span style="border-radius: 9999px; background-color: #d1fae5; padding: 0.2rem 0.6rem; font-size: 0.7rem; font-weight: 700; color: #065f46;">
                100%
            </span>
        @else
            <span style="border-radius: 9999px; background-color: #f1f5f9; padding: 0.2rem 0.6rem; font-size: 0.7rem; font-weight: 700; color: #475569;">
                {{ $progress }}%
            </span>
        @endif
    </div>

    <!-- Progress Bar -->
    <div style="margin-top: 0.875rem; height: 6px; width: 100%; overflow: hidden; border-radius: 9999px; background-color: #f1f5f9;">
        <div style="height: 100%; border-radius: 9999px; background-color: {{ $status === 'approved' ? '#059669' : '#2563eb' }}; width: {{ $progress }}%; transition: width 0.4s ease;"></div>
    </div>

    <!-- Steps List -->
    <div style="margin-top: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;">
        <!-- Step 1: Personal Info -->
        <div style="display: flex; align-items: center; justify-content: space-between; border-radius: 0.75rem; border: 1px solid {{ $status === 'approved' ? '#a7f3d0' : '#dbeafe' }}; background-color: {{ $status === 'approved' ? '#f0fdf4' : '#eff6ff' }}; padding: 0.625rem 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 24px; height: 24px; border-radius: 50%; background-color: {{ $status === 'approved' ? '#059669' : '#2563eb' }}; color: #ffffff; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span style="font-size: 0.75rem; font-weight: 600; color: #1e293b;">1. Personal Info</span>
            </div>
            <svg style="width: 16px; height: 16px; color: {{ $status === 'approved' ? '#059669' : '#2563eb' }};" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <!-- Step 2: ID Document -->
        <a href="{{ url('/dashboard/kyc-verification-page') }}"
           style="display: flex; align-items: center; justify-content: space-between; border-radius: 0.75rem; border: 1px solid {{ $step2Done ? ($status === 'approved' ? '#a7f3d0' : '#dbeafe') : '#e2e8f0' }}; background-color: {{ $step2Done ? ($status === 'approved' ? '#f0fdf4' : '#eff6ff') : '#ffffff' }}; padding: 0.625rem 0.875rem; text-decoration: none;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                @if($step2Done)
                    <div style="width: 24px; height: 24px; border-radius: 50%; background-color: {{ $status === 'approved' ? '#059669' : '#2563eb' }}; color: #ffffff; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                @else
                    <div style="width: 24px; height: 24px; border-radius: 50%; border: 2px solid #2563eb; background-color: #ffffff; display: flex; align-items: center; justify-content: center;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #2563eb;"></div>
                    </div>
                @endif
                <span style="font-size: 0.75rem; font-weight: 600; color: #1e293b;">2. ID Document</span>
            </div>
            @if(!$step2Done)
                <span style="border-radius: 0.25rem; background-color: #dbeafe; padding: 0.125rem 0.5rem; font-size: 0.65rem; font-weight: 600; color: #1d4ed8;">In Progress</span>
            @else
                <svg style="width: 16px; height: 16px; color: {{ $status === 'approved' ? '#059669' : '#2563eb' }};" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            @endif
        </a>

        <!-- Step 3: Ownership (Lalpurja) -->
        <a href="{{ url('/dashboard/kyc-verification-page') }}"
           style="display: flex; align-items: center; justify-content: space-between; border-radius: 0.75rem; border: 1px solid {{ $step3Done ? ($status === 'approved' ? '#a7f3d0' : '#dbeafe') : '#e2e8f0' }}; background-color: {{ $step3Done ? ($status === 'approved' ? '#f0fdf4' : '#eff6ff') : '#ffffff' }}; padding: 0.625rem 0.875rem; text-decoration: none;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                @if($step3Done)
                    <div style="width: 24px; height: 24px; border-radius: 50%; background-color: {{ $status === 'approved' ? '#059669' : '#2563eb' }}; color: #ffffff; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                @else
                    <div style="width: 24px; height: 24px; border-radius: 50%; border: 2px solid #cbd5e1; background-color: #ffffff;"></div>
                @endif
                <span style="font-size: 0.75rem; font-weight: {{ $step3Done ? '600' : '500' }}; color: {{ $step3Done ? '#1e293b' : '#475569' }};">3. Ownership (Lalpurja)</span>
            </div>
            @if($step3Done)
                <svg style="width: 16px; height: 16px; color: {{ $status === 'approved' ? '#059669' : '#2563eb' }};" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            @endif
        </a>

        <!-- Step 4: Review -->
        <div style="display: flex; align-items: center; justify-content: space-between; border-radius: 0.75rem; border: 1px solid {{ $step4Done ? '#a7f3d0' : ($step4Active ? '#fde68a' : '#e2e8f0') }}; background-color: {{ $step4Done ? '#f0fdf4' : ($step4Active ? '#fffbeb' : '#ffffff') }}; padding: 0.625rem 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                @if($step4Done)
                    <div style="width: 24px; height: 24px; border-radius: 50%; background-color: #059669; color: #ffffff; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                @elseif($step4Active)
                    <div style="width: 24px; height: 24px; border-radius: 50%; border: 2px solid #f59e0b; background-color: #ffffff; display: flex; align-items: center; justify-content: center;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #f59e0b;"></div>
                    </div>
                @else
                    <div style="width: 24px; height: 24px; border-radius: 50%; border: 2px solid #cbd5e1; background-color: #ffffff;"></div>
                @endif
                <span style="font-size: 0.75rem; font-weight: {{ $step4Done ? '600' : '500' }}; color: {{ $step4Done ? '#1e293b' : '#475569' }};">4. Review</span>
            </div>
            @if($step4Done)
                <span style="border-radius: 0.25rem; background-color: #d1fae5; padding: 0.125rem 0.5rem; font-size: 0.65rem; font-weight: 700; color: #065f46;">Approved</span>
            @elseif($step4Active)
                <span style="border-radius: 0.25rem; background-color: #fef3c7; padding: 0.125rem 0.5rem; font-size: 0.65rem; font-weight: 600; color: #92400e;">Reviewing</span>
            @endif
        </div>
    </div>
</div>
