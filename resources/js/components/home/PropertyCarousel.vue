<script setup lang="ts">
import type { Listing } from '../../types/marketplace';
import { ref } from 'vue';
import PropertyCard from './PropertyCard.vue';

withDefaults(
    defineProps<{
        title?: string;
        listings?: Listing[];
    }>(),
    {
        listings: () => [],
    },
);

const track = ref<HTMLElement | null>(null);

function scrollBy(direction: 1 | -1) {
    const el = track.value;
    if (!el) return;
    el.scrollBy({ left: direction * 320, behavior: 'smooth' });
}
</script>

<template>
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                {{ title ?? 'Current Listings' }}
            </h2>

            <button
                type="button"
                aria-label="Scroll right"
                @click="scrollBy(1)"
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>

        <div
            v-if="listings.length > 0"
            ref="track"
            class="mt-5 flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
        >
            <PropertyCard v-for="item in listings" :key="item.listing_id" :listing="item" />
        </div>

        <p v-else class="mt-8 py-10 text-center text-sm text-slate-500">
            No approved listings available yet.
        </p>
    </section>
</template>
