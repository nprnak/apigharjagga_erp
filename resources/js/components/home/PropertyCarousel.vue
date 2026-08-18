<script setup lang="ts">
import type { Listing } from '../../types/marketplace';
import { ref } from 'vue';
import PropertyCard from './PropertyCard.vue';

defineProps<{
    listings: Listing[];
    title?: string;
    subtitle?: string;
}>();

const track = ref<HTMLElement | null>(null);

function scrollBy(direction: 1 | -1) {
    const el = track.value;
    if (!el) return;
    el.scrollBy({ left: direction * (el.clientWidth * 0.8), behavior: 'smooth' });
}
</script>

<template>
    <section class="mx-auto max-w-6xl px-4 py-14">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                    {{ title ?? 'Featured listings' }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    {{ subtitle ?? 'Newly approved properties across Nepal' }}
                </p>
            </div>

            <div class="hidden items-center gap-2 sm:flex">
                <button
                    type="button"
                    aria-label="Scroll left"
                    @click="scrollBy(-1)"
                    class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <button
                    type="button"
                    aria-label="Scroll right"
                    @click="scrollBy(1)"
                    class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

        <div
            ref="track"
            class="mt-6 flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth pb-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
        >
            <PropertyCard v-for="item in listings" :key="item.listing_id" :listing="item" />

            <p
                v-if="listings.length === 0"
                class="w-full py-10 text-center text-sm text-slate-500"
            >
                No approved listings available yet.
            </p>
        </div>
    </section>
</template>
