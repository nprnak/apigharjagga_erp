<script setup lang="ts">
defineProps<{
    icon: string;
    heading: string;
    pitch: string;
    ctaLabel: string;
    ctaHref: string;
}>();

const iconPaths: Record<string, string> = {
    search: 'M10.5 3a7.5 7.5 0 1 0 4.55 13.46l4.24 4.24 1.42-1.42-4.24-4.24A7.5 7.5 0 0 0 10.5 3Zm0 2a5.5 5.5 0 1 1 0 11 5.5 5.5 0 0 1 0-11Z',
    tag: 'M12 2H5a3 3 0 0 0-3 3v7l9.5 9.5a2 2 0 0 0 2.83 0l6.17-6.17a2 2 0 0 0 0-2.83L12 2Zm-4.5 6A1.5 1.5 0 1 1 7.5 5 1.5 1.5 0 0 1 7.5 8Z',
    shield: 'M12 2 4 5v6c0 5 3.4 8.7 8 11 4.6-2.3 8-6 8-11V5l-8-3Zm-1 13-3.5-3.5 1.4-1.4L11 12.2l4.1-4.1 1.4 1.4L11 15Z',
};
</script>

<template>
    <div class="flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path :d="iconPaths[icon] ?? iconPaths.search" />
            </svg>
        </div>

        <h3 class="mt-4 text-lg font-bold text-slate-900">{{ heading }}</h3>
        <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600">{{ pitch }}</p>

        <a
            :href="ctaHref"
            class="mt-5 inline-flex w-fit items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-brand-700"
        >
            {{ ctaLabel }}
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>
    </div>
</template>
