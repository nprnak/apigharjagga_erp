<script setup lang="ts">
import { ref } from 'vue';

const address = ref<string>('');
const showEstimate = ref<boolean>(false);

const MOCK_ESTIMATE = 'Rs. 4,75,00,000';

function estimate() {
    if (address.value.trim() === '') return;
    showEstimate.value = true;
}
</script>

<template>
    <section id="home-valuation" class="bg-white">
        <div class="mx-auto grid max-w-7xl grid-cols-1 lg:grid-cols-2">
            <!-- Left: lifestyle image + estimate card -->
            <div class="relative min-h-[320px] overflow-hidden sm:min-h-[420px]">
                <img
                    src="https://images.unsplash.com/photo-1556911220-e15b29be8c8f?w=900&q=80"
                    alt="Modern home interior"
                    class="absolute inset-0 h-full w-full object-cover"
                />

                <div
                    class="pointer-events-none absolute inset-x-0 bottom-0 h-40 bg-linear-to-t from-white/90 via-white/40 to-transparent"
                />

                <svg
                    class="pointer-events-none absolute inset-x-0 bottom-0 h-28 w-full text-slate-200/80"
                    viewBox="0 0 800 120"
                    preserveAspectRatio="none"
                    aria-hidden="true"
                >
                    <path
                        d="M0 80 Q120 40 240 70 T480 55 T720 75 T800 60 V120 H0 Z"
                        fill="currentColor"
                    />
                    <path
                        d="M60 95 Q180 65 300 88 T540 72 T780 90"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        opacity="0.5"
                    />
                </svg>

                <div
                    v-if="showEstimate"
                    class="absolute bottom-10 left-1/2 w-[85%] max-w-xs -translate-x-1/2 rounded-xl bg-white px-6 py-5 text-center shadow-xl"
                >
                    <p class="text-sm font-medium text-slate-800">Your estimate is in!</p>
                    <p class="mt-1 text-3xl font-bold text-brand-600">{{ MOCK_ESTIMATE }}</p>
                    <div class="mt-4 flex justify-center">
                        <span
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-600 text-white shadow-md"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M12 21s-6-5.2-6-10a6 6 0 1 1 12 0c0 4.8-6 10-6 10Z"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                />
                                <path
                                    d="M12 11.5a1.75 1.75 0 1 0 0-3.5 1.75 1.75 0 0 0 0 3.5Z"
                                    fill="currentColor"
                                />
                                <path
                                    d="M10 13.5h4v2.5a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-2.5Z"
                                    fill="currentColor"
                                />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right: form -->
            <div class="flex flex-col justify-center px-6 py-12 sm:px-10 lg:px-14 lg:py-16">
                <span class="text-xs font-semibold tracking-[0.14em] text-brand-600 uppercase">
                    Instant home value
                </span>

                <h2 class="mt-3 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Get an instant estimate on your home*
                </h2>

                <p class="mt-3 max-w-md text-sm leading-relaxed text-slate-600 sm:text-base">
                    Learn your home's value so you can tackle the real estate market with confidence.
                </p>

                <form class="mt-8" @submit.prevent="estimate">
                    <div class="relative max-w-lg">
                        <input
                            v-model="address"
                            placeholder="Enter a home address"
                            class="w-full rounded-full border border-slate-300 bg-white py-3.5 pr-14 pl-5 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
                        />
                        <button
                            type="submit"
                            aria-label="Get estimate"
                            class="absolute top-1/2 right-1.5 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-brand-600 text-white shadow-sm transition hover:bg-brand-700"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M5 12h14M13 6l6 6-6 6"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </button>
                    </div>
                </form>

                <p class="mt-6 max-w-lg text-[11px] leading-relaxed text-slate-400">
                    *This is an estimate only and is not an appraisal. It is not intended to be a
                    substitute for an appraisal and may not be used for any purpose other than as a
                    starting point for a discussion with a real estate professional.
                </p>
            </div>
        </div>
    </section>
</template>
