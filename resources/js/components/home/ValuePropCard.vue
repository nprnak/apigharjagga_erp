<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    icon: string;
    heading: string;
    pitch: string;
    ctaLabel: string;
    ctaHref: string;
    theme: 'blue' | 'green' | 'purple';
    illustration: 'house' | 'phone' | 'document';
}>();

const iconPaths: Record<string, string> = {
    search: 'M10.5 3a7.5 7.5 0 1 0 4.55 13.46l4.24 4.24 1.42-1.42-4.24-4.24A7.5 7.5 0 0 0 10.5 3Zm0 2a5.5 5.5 0 1 1 0 11 5.5 5.5 0 0 1 0-11Z',
    tag: 'M12 2H5a3 3 0 0 0-3 3v7l9.5 9.5a2 2 0 0 0 2.83 0l6.17-6.17a2 2 0 0 0 0-2.83L12 2Zm-4.5 6A1.5 1.5 0 1 1 7.5 5 1.5 1.5 0 0 1 7.5 8Z',
    shield: 'M12 2 4 5v6c0 5 3.4 8.7 8 11 4.6-2.3 8-6 8-11V5l-8-3Zm-1 13-3.5-3.5 1.4-1.4L11 12.2l4.1-4.1 1.4 1.4L11 15Z',
};

const themeStyles = computed(() => {
    const map = {
        blue: {
            iconBg: 'bg-blue-50 text-blue-600',
            blob: 'bg-blue-100/70',
            button: 'bg-blue-600 hover:bg-blue-700',
        },
        green: {
            iconBg: 'bg-emerald-50 text-emerald-600',
            blob: 'bg-emerald-100/70',
            button: 'bg-emerald-600 hover:bg-emerald-700',
        },
        purple: {
            iconBg: 'bg-violet-50 text-violet-600',
            blob: 'bg-violet-100/70',
            button: 'bg-violet-600 hover:bg-violet-700',
        },
    };
    return map[props.theme];
});
</script>

<template>
    <div
        class="relative flex min-h-[300px] overflow-hidden rounded-3xl bg-white shadow-[0_4px_24px_rgba(15,23,42,0.06)] transition hover:shadow-[0_8px_32px_rgba(15,23,42,0.1)]"
    >
        <div class="relative z-10 flex flex-1 flex-col p-6 sm:p-7">
            <div
                class="flex h-11 w-11 items-center justify-center rounded-xl"
                :class="themeStyles.iconBg"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path :d="iconPaths[icon] ?? iconPaths.search" />
                </svg>
            </div>

            <h3 class="mt-4 text-lg font-bold text-slate-900">{{ heading }}</h3>
            <p class="mt-2 max-w-[220px] flex-1 text-sm leading-relaxed text-slate-600">{{ pitch }}</p>

            <a
                :href="ctaHref"
                class="mt-5 inline-flex w-fit items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition"
                :class="themeStyles.button"
            >
                {{ ctaLabel }}
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path
                        d="M5 12h14M13 6l6 6-6 6"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </a>
        </div>

        <div class="relative hidden w-[42%] shrink-0 sm:block">
            <div
                class="absolute top-1/2 right-[-10%] h-44 w-44 -translate-y-1/2 rounded-full blur-sm"
                :class="themeStyles.blob"
            />

            <!-- House illustration -->
            <div
                v-if="illustration === 'house'"
                class="absolute inset-0 flex items-end justify-center pb-4 pr-2"
            >
                <svg class="h-40 w-40 drop-shadow-md" viewBox="0 0 160 160" fill="none" aria-hidden="true">
                    <rect x="30" y="70" width="100" height="70" rx="4" fill="#f8fafc" stroke="#cbd5e1" stroke-width="2" />
                    <path d="M20 72 L80 30 L140 72" stroke="#94a3b8" stroke-width="3" fill="#e2e8f0" stroke-linejoin="round" />
                    <rect x="55" y="95" width="22" height="18" rx="2" fill="#bfdbfe" />
                    <rect x="85" y="95" width="22" height="18" rx="2" fill="#bfdbfe" />
                    <rect x="68" y="115" width="24" height="25" rx="2" fill="#64748b" />
                    <rect x="95" y="55" width="18" height="30" rx="2" fill="#94a3b8" />
                    <ellipse cx="120" cy="130" rx="18" ry="6" fill="#86efac" opacity="0.6" />
                    <ellipse cx="45" cy="132" rx="14" ry="5" fill="#86efac" opacity="0.6" />
                </svg>
            </div>

            <!-- Phone illustration -->
            <div
                v-else-if="illustration === 'phone'"
                class="absolute inset-0 flex items-end justify-center pb-6 pr-1"
            >
                <svg class="h-44 w-36 drop-shadow-lg" viewBox="0 0 120 160" fill="none" aria-hidden="true">
                    <rect x="20" y="10" width="80" height="140" rx="14" fill="#f1f5f9" stroke="#cbd5e1" stroke-width="2" />
                    <rect x="28" y="28" width="64" height="90" rx="6" fill="#fff" stroke="#e2e8f0" stroke-width="1.5" />
                    <rect x="36" y="58" width="48" height="32" rx="3" fill="#dbeafe" />
                    <path d="M36 58 L60 42 L84 58" stroke="#93c5fd" stroke-width="1.5" fill="#eff6ff" />
                    <rect x="44" y="68" width="10" height="8" rx="1" fill="#bfdbfe" />
                    <rect x="58" y="68" width="10" height="8" rx="1" fill="#bfdbfe" />
                    <circle cx="60" cy="18" r="3" fill="#cbd5e1" />
                    <rect x="48" y="130" width="24" height="4" rx="2" fill="#cbd5e1" />
                    <rect x="88" y="50" width="28" height="16" rx="8" fill="#10b981" />
                    <text x="94" y="62" fill="white" font-size="7" font-weight="bold">Verified</text>
                </svg>
            </div>

            <!-- Document illustration -->
            <div
                v-else
                class="absolute inset-0 flex items-end justify-center pb-5 pr-2"
            >
                <svg class="h-40 w-36 drop-shadow-md" viewBox="0 0 140 150" fill="none" aria-hidden="true">
                    <rect x="30" y="20" width="70" height="90" rx="6" fill="#fff" stroke="#e2e8f0" stroke-width="2" />
                    <line x1="42" y1="40" x2="88" y2="40" stroke="#e2e8f0" stroke-width="3" stroke-linecap="round" />
                    <line x1="42" y1="52" x2="80" y2="52" stroke="#e2e8f0" stroke-width="3" stroke-linecap="round" />
                    <line x1="42" y1="64" x2="84" y2="64" stroke="#e2e8f0" stroke-width="3" stroke-linecap="round" />
                    <line x1="42" y1="76" x2="72" y2="76" stroke="#e2e8f0" stroke-width="3" stroke-linecap="round" />
                    <circle cx="88" cy="88" r="24" fill="#7c3aed" />
                    <path d="M78 88 L85 95 L98 80" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
    </div>
</template>
