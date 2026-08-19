<script setup lang="ts">
import type { Listing } from '../../types/marketplace';
import { computed } from 'vue';

const props = defineProps<{ listing: Listing }>();

const purposeLabel = computed(() => {
    const p = props.listing.purpose;
    if (p === 'sale') return 'For Sale';
    if (p === 'rent') return 'For Rent';
    if (p === 'lease') return 'For Lease';
    if (!p) return 'Listed';
    return p.charAt(0).toUpperCase() + p.slice(1);
});

const statusLabel = computed(() => {
    const s = props.listing.status;
    if (s === 'listed') return 'Listed';
    if (!s) return 'Listed';
    return s
        .split('_')
        .map((w) => w.charAt(0).toUpperCase() + w.slice(1))
        .join(' ');
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

const detailsLabel = computed(() => {
    const parts: string[] = [];
    if (props.listing.no_of_floors) parts.push(`${props.listing.no_of_floors} Floors`);
    if (props.listing.covered_area) parts.push(props.listing.covered_area);
    if (props.listing.area) parts.push(props.listing.area);
    parts.push(typeLabel.value);
    return parts.join(' · ');
});

const addressLabel = computed(() => {
    const parts = [
        props.listing.municipality,
        props.listing.district,
        props.listing.province,
    ].filter(Boolean);
    return parts.length ? parts.join(', ') : 'Location not set';
});

const imageUrl = computed(() => {
    if (props.listing.photo_url) return props.listing.photo_url;
    return `https://picsum.photos/seed/listing-${props.listing.listing_id}/480/320`;
});
</script>

<template>
    <article
        class="flex w-[260px] shrink-0 snap-start flex-col overflow-hidden rounded-xl border border-slate-100 bg-white shadow-md sm:w-[280px]"
    >
        <div class="relative aspect-[4/3] bg-slate-100">
            <img
                :src="imageUrl"
                :alt="typeLabel"
                class="h-full w-full object-cover"
                loading="lazy"
            />

            <span
                class="absolute top-3 left-3 rounded-full bg-[#1a365d] px-3 py-1 text-[11px] font-semibold text-white"
            >
                {{ purposeLabel }}
            </span>

            <button
                type="button"
                aria-label="Save to favorites"
                class="absolute top-3 right-3 flex h-8 w-8 items-center justify-center rounded-full bg-black/20 text-white backdrop-blur-sm transition hover:bg-black/30"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path
                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
                        stroke="currentColor"
                        stroke-width="1.8"
                        fill="none"
                    />
                </svg>
            </button>

            <span
                class="absolute bottom-3 left-3 flex items-center gap-1 rounded-md bg-black/55 px-2 py-0.5 text-[11px] font-medium text-white"
            >
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path
                        d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                    <circle cx="12" cy="13" r="4" stroke="currentColor" stroke-width="1.8" />
                </svg>
                1
            </span>
        </div>

        <div class="flex flex-col gap-1.5 p-3.5">
            <div class="flex items-center justify-between gap-2">
                <div class="text-lg font-bold text-[#1a365d]">{{ priceLabel }}</div>
                <div class="flex shrink-0 items-center gap-1.5 text-xs text-slate-500">
                    <span class="h-2 w-2 rounded-full bg-brand-500" />
                    {{ statusLabel }}
                </div>
            </div>

            <p class="truncate text-xs text-slate-500">{{ detailsLabel }}</p>
            <p class="truncate text-xs text-slate-500">{{ addressLabel }}</p>

            <div class="mt-1 flex items-center justify-between gap-2">
                <span class="truncate text-[10px] text-slate-400">MLS#{{ listing.application_no }}</span>
                <span
                    class="flex h-5 w-5 shrink-0 items-center justify-center rounded bg-brand-600 text-[8px] font-bold text-white"
                >
                    CB
                </span>
            </div>
        </div>
    </article>
</template>
