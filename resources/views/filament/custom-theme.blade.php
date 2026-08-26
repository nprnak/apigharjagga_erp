<style>
    /* ── Clean Classic Light Theme Polish ── */
    :root {
        --color-slate-50: #f8fafc;
        --color-slate-100: #f1f5f9;
        --color-slate-200: #e2e8f0;
        --color-slate-300: #cbd5e1;
        --color-slate-600: #475569;
        --color-slate-700: #334155;
        --color-slate-800: #1e293b;
        --color-slate-900: #0f172a;
    }

    body,
    .fi-layout,
    .fi-main {
        background-color: #f8fafc !important;
        color: #0f172a !important;
    }

    /* Topbar styling */
    .fi-topbar {
        background-color: #ffffff !important;
        border-bottom: 1px solid #e2e8f0 !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02) !important;
    }

    .fi-topbar-start .fi-logo {
        font-weight: 700 !important;
        font-size: 1.125rem !important;
        color: #0f172a !important;
        letter-spacing: -0.02em !important;
    }

    /* Sidebar styling */
    .fi-sidebar {
        background-color: #ffffff !important;
        border-right: 1px solid #e2e8f0 !important;
    }

    .fi-sidebar-item-active > a,
    .fi-sidebar-item-active > button {
        background-color: #eff6ff !important;
        color: #2563eb !important;
        font-weight: 600 !important;
    }

    .fi-sidebar-item-active svg {
        color: #2563eb !important;
    }

    /* Card styling */
    .fi-section,
    .fi-wi,
    .fi-card {
        border-radius: 1rem !important;
        border: 1px solid #e2e8f0 !important;
        background-color: #ffffff !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04) !important;
    }

    /* Global Search styling */
    .fi-global-search-field .fi-input-wrp {
        border-radius: 0.75rem !important;
        background-color: #f8fafc !important;
        border-color: #e2e8f0 !important;
    }

    .fi-global-search-field .fi-input-wrp:focus-within {
        background-color: #ffffff !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 1px #3b82f6 !important;
    }

    /* Interactive hover effects */
    .interactive-card {
        transition: all 0.2s ease-in-out;
    }

    .interactive-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
    }

    /* Remove empty header whitespace */
    .fi-header:empty,
    .fi-page-header-main-ctn:has(h1:empty) {
        display: none !important;
    }

    /* Force circular avatars */
    .fi-avatar,
    .fi-user-avatar {
        border-radius: 9999px !important;
        aspect-ratio: 1 / 1 !important;
    }
</style>
