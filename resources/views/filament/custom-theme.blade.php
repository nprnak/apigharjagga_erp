<style>
    /* ── Pure Pitch Black Dark Mode ── */
    html.dark,
    .dark {
        --color-zinc-950: #050505;
        --color-zinc-900: #0a0a0c;
        --color-zinc-800: #18181b;
        color-scheme: dark;
    }

    .dark body,
    .dark .fi-layout,
    .dark .fi-main {
        background-color: #050505 !important;
        color: #f4f4f5 !important;
    }

    /* Animated mesh gradient background */
    .dark .fi-main-ctn {
        position: relative;
        overflow: hidden;
    }

    .dark .fi-main-ctn::before {
        content: '';
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background:
            radial-gradient(ellipse 80% 60% at 10% 20%, rgba(59, 130, 246, 0.06) 0%, transparent 50%),
            radial-gradient(ellipse 60% 50% at 90% 80%, rgba(99, 102, 241, 0.05) 0%, transparent 50%),
            radial-gradient(ellipse 50% 40% at 50% 50%, rgba(16, 185, 129, 0.03) 0%, transparent 50%);
        animation: mesh-drift 20s ease-in-out infinite alternate;
    }

    @keyframes mesh-drift {
        0% { transform: translate(0, 0) scale(1); }
        100% { transform: translate(-2%, 1%) scale(1.02); }
    }

    .dark .fi-main-ctn > * {
        position: relative;
        z-index: 1;
    }

    .dark .fi-sidebar {
        background-color: #09090b !important;
        border-right: 1px solid #1f1f23 !important;
    }

    .dark .fi-topbar {
        background-color: rgba(9, 9, 11, 0.85) !important;
        backdrop-filter: blur(16px) !important;
        border-bottom: 1px solid #1f1f23 !important;
    }

    .dark .fi-section,
    .dark .fi-widget,
    .dark .fi-ta-ctn,
    .dark .fi-card,
    .dark .fi-modal-window {
        background-color: #0c0c0f !important;
        border-color: #222227 !important;
    }

    .dark .fi-input-wrp {
        background-color: #121216 !important;
        border-color: #27272f !important;
    }

    .dark .fi-input-wrp:focus-within {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 1px #3b82f6 !important;
    }

    .dark .fi-sidebar-item-active > a,
    .dark .fi-sidebar-item-active > button {
        background-color: #1e1e24 !important;
        color: #ffffff !important;
    }

    .dark .fi-tabs-item-active {
        border-color: #3b82f6 !important;
        color: #ffffff !important;
    }

    .dark .fi-ta-row {
        transition: background-color 0.2s ease;
    }

    .dark .fi-ta-row:hover {
        background-color: #141418 !important;
    }

    /* KYC welcome card entrance */
    .dashboard-card-enter {
        animation: widget-fade-in 0.5s ease-out both;
    }

    /* ── Interactive card hover effects ── */
    .interactive-card {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .interactive-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 32px -12px rgba(0, 0, 0, 0.45);
    }

    /* ── Widget entrance animations ── */
    .fi-wi {
        animation: widget-fade-in 0.5s ease-out both;
    }

    .fi-wi:nth-child(1) { animation-delay: 0.05s; }
    .fi-wi:nth-child(2) { animation-delay: 0.1s; }
    .fi-wi:nth-child(3) { animation-delay: 0.15s; }
    .fi-wi:nth-child(4) { animation-delay: 0.2s; }
    .fi-wi:nth-child(5) { animation-delay: 0.25s; }

    @keyframes widget-fade-in {
        from {
            opacity: 0;
            transform: translateY(12px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ── Stats overview enhancements ── */
    .fi-wi-stats-overview-stat {
        position: relative;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        border-radius: 1rem !important;
        overflow: hidden;
    }

    .fi-wi-stats-overview-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px -10px rgba(0, 0, 0, 0.35);
        border-color: rgba(59, 130, 246, 0.35) !important;
    }

    .dark .fi-wi-stats-overview-stat:hover {
        box-shadow: 0 12px 28px -10px rgba(0, 0, 0, 0.6);
    }

    /* Leading icon rendered as a soft rounded badge */
    .fi-wi-stats-overview-stat-label-ctn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .fi-wi-stats-overview-stat-label-ctn > svg:first-child {
        flex-shrink: 0;
        width: 1.125rem;
        height: 1.125rem;
        padding: 0.375rem;
        box-sizing: content-box;
        border-radius: 0.625rem;
        background-color: rgba(59, 130, 246, 0.1);
        color: #3b82f6 !important;
    }

    .dark .fi-wi-stats-overview-stat-label-ctn > svg:first-child {
        background-color: rgba(59, 130, 246, 0.15);
    }

    .fi-wi-stats-overview-stat-value {
        font-size: 1.75rem !important;
        font-weight: 800 !important;
        letter-spacing: -0.02em;
        margin-top: 0.125rem;
    }

    .fi-wi-stats-overview-stat-description {
        margin-top: 0.375rem !important;
    }

    .fi-wi-stats-overview-stat-chart {
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }

    .fi-wi-stats-overview-stat:hover .fi-wi-stats-overview-stat-chart {
        opacity: 1;
    }

    /* ── Chart widget polish ── */
    .fi-wi-chart canvas {
        transition: transform 0.3s ease;
    }

    .fi-wi-chart:hover canvas {
        transform: scale(1.01);
    }

    /* ── KYC progress step connector ── */
    .kyc-step-connector {
        position: relative;
    }

    .kyc-step-connector::after {
        content: '';
        position: absolute;
        top: 1.5rem;
        left: 100%;
        width: 0.75rem;
        height: 2px;
        background: #d4d4d8;
        transform: translateY(-50%);
        z-index: 0;
    }

    .dark .kyc-step-connector::after {
        background: #27272f;
    }

    .kyc-step-connector--done::after {
        background: linear-gradient(90deg, #2563eb, #6366f1);
    }

    @media (max-width: 639px) {
        .kyc-step-connector::after {
            display: none;
        }
    }

    /* ── Custom scrollbar (dark) ── */
    .dark ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .dark ::-webkit-scrollbar-track {
        background: #0a0a0c;
    }

    .dark ::-webkit-scrollbar-thumb {
        background: #27272f;
        border-radius: 3px;
    }

    .dark ::-webkit-scrollbar-thumb:hover {
        background: #3f3f46;
    }

    /* ── Table empty state ── */
    .fi-ta-empty-state {
        padding: 2rem !important;
    }

    /* ── Light mode interactive cards ── */
    .interactive-card:active {
        transform: translateY(-1px) scale(0.99);
    }
</style>
