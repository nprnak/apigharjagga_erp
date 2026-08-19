@php
    $userId = auth()->id();
    $activeListings = \App\Models\Property::where('user_id', $userId)->where('approval_status', 'approved')->count();
    $pendingListings = \App\Models\Property::where('user_id', $userId)->where('approval_status', 'pending')->count();
    $rejectedListings = \App\Models\Property::where('user_id', $userId)->where('approval_status', 'rejected')->count();
@endphp

<div class="dashboard-stats-grid">
    <!-- 1. Active Listings -->
    <a href="{{ url('/dashboard/my-properties') }}"
       class="dash-card"
       style="display: block; text-decoration: none; padding: 1.25rem;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 0.625rem; background-color: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                </div>
                <span style="font-size: 0.875rem; font-weight: 600; color: #1e293b;">Active Listings</span>
            </div>
        </div>

        <div style="margin-top: 1rem;">
            <span style="font-size: 1.875rem; font-weight: 800; color: #0f172a; line-height: 1;">{{ $activeListings }}</span>
            <p style="margin-top: 0.35rem; font-size: 0.75rem; font-weight: 500; color: #64748b;">Click to manage your listings</p>
            <p style="margin-top: 0.15rem; font-size: 0.7rem; color: #94a3b8;">Publicly visible on marketplace</p>
        </div>
    </a>

    <!-- 2. In Review -->
    <a href="{{ url('/dashboard/my-properties') }}"
       class="dash-card"
       style="display: block; text-decoration: none; padding: 1.25rem;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 0.625rem; background-color: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span style="font-size: 0.875rem; font-weight: 600; color: #1e293b;">In Review</span>
            </div>
            <svg style="width: 16px; height: 16px; color: #94a3b8;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <div style="margin-top: 1rem;">
            <span style="font-size: 1.875rem; font-weight: 800; color: #0f172a; line-height: 1;">{{ $pendingListings }}</span>
            <p style="margin-top: 0.35rem; font-size: 0.75rem; font-weight: 500; color: #64748b;">Pending administrator approval</p>
            <p style="margin-top: 0.15rem; font-size: 0.7rem; color: #94a3b8;">Verification in progress</p>
        </div>
    </a>

    <!-- 3. Inquiries & Leads -->
    <div class="dash-card" style="padding: 1.25rem;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 0.625rem; background-color: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <span style="font-size: 0.875rem; font-weight: 600; color: #1e293b;">Inquiries & Leads</span>
            </div>
            <svg style="width: 16px; height: 16px; color: #94a3b8;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
        </div>

        <div style="margin-top: 1rem;">
            <span style="font-size: 1.875rem; font-weight: 800; color: #0f172a; line-height: 1;">0</span>
            <p style="margin-top: 0.35rem; font-size: 0.75rem; font-weight: 500; color: #64748b;">New messages from potential buyers</p>
            <p style="margin-top: 0.15rem; font-size: 0.7rem; color: #94a3b8;">Direct buyer inquiries</p>
        </div>
    </div>

    <!-- 4. Drafts & Rejected -->
    <a href="{{ url('/dashboard/my-properties') }}"
       class="dash-card"
       style="display: block; text-decoration: none; padding: 1.25rem;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 0.625rem; background-color: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span style="font-size: 0.875rem; font-weight: 600; color: #1e293b;">Drafts & Rejected</span>
            </div>
            <svg style="width: 16px; height: 16px; color: #94a3b8;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>

        <div style="margin-top: 1rem;">
            <span style="font-size: 1.875rem; font-weight: 800; color: #0f172a; line-height: 1;">{{ $rejectedListings }}</span>
            <p style="margin-top: 0.35rem; font-size: 0.75rem; font-weight: 500; color: #64748b;">Manage and resubmit properties</p>
            <p style="margin-top: 0.15rem; font-size: 0.7rem; color: #94a3b8;">Items requiring revision</p>
        </div>
    </a>
</div>
