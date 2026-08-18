<script setup lang="ts">
import type { Listing } from '../../types/marketplace';
import { computed } from 'vue';

const props = defineProps<{ listing: Listing }>();

const purposeLabel = computed(() => {
    const p = props.listing.purpose;
    if (!p) return 'Listed';
    return p.charAt(0).toUpperCase() + p.slice(1);
});

const typeLabel = computed(() => {
    const t = props.listing.property_type;
    if (!t) return 'Property';
    return t
        .split('_')
        .map((w) => w.charAt(0).toUpperCase() + w.slice(1))
        .join(' ');
});

const priceLabel = computed(() => {
    const value = props.listing.price;
    if (value === null || value === undefined || value === '') return 'Contact for price';
    const n = Number(value);
    if (Number.isNaN(n)) return String(value);
    return 'Rs. ' + new Intl.NumberFormat('en-IN').format(n);
});

const locationLabel = computed(() => {
    const parts = [props.listing.municipality, props.listing.district].filter(Boolean);
    return parts.length ? parts.join(', ') : 'Location not set';
});
</script>

<template>
    <article
        class="flex h-full w-72 shrink-0 snap-start flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg sm:w-80"
    >
        <div class="relative h-44 bg-slate-100">
            <img
                v-if="listing.photo_url"
                :src="listing.photo_url"
                :alt="typeLabel"
                class="h-full w-full object-cover"
                loading="lazy"
            />
            <div
                v-else
                class="flex h-full w-full items-center justify-center bg-linear-to-br from-brand-500/15 to-slate-900/10 text-slate-400"
            >
                <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path
                        d="M3 10.5L12 3l9 7.5V21a1 1 0 0 1-1 1h-6v-7H10v7H4a1 1 0 0 1-1-1V10.5Z"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </div>
            <span
                class="absolute top-3 left-3 rounded-full bg-brand-600 px-3 py-1 text-xs font-bold text-white shadow-sm"
            >
                For {{ purposeLabel }}
            </span>
        </div>

        <div class="flex flex-1 flex-col p-4">
            <div class="text-lg font-extrabold text-slate-900">{{ priceLabel }}</div>

            <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-semibold text-slate-600">
                <span v-if="listing.no_of_floors">{{ listing.no_of_floors }} floors</span>
                <span v-if="listing.covered_area" class="text-slate-300">•</span>
                <span v-if="listing.covered_area">{{ listing.covered_area }} covered</span>
                <span v-if="listing.area" class="text-slate-300">•</span>
                <span v-if="listing.area">{{ listing.area }}</span>
            </div>

            <div class="mt-3 text-sm font-semibold text-slate-800">{{ typeLabel }}</div>
            <div class="text-sm text-slate-500">{{ locationLabel }}</div>

            <div class="mt-auto flex items-center justify-between gap-2 pt-4 text-xs">
                <span class="font-medium text-slate-400">MLS #{{ listing.application_no }}</span>
                <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 font-bold text-slate-600">
                    {{ listing.negotiable ? 'Negotiable' : 'Fixed price' }}
                </span>
            </div>
        </div>
    </article>
</template>
