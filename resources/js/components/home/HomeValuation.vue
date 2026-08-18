<script setup lang="ts">
import { ref } from 'vue';

const address = ref<string>('');
const showEstimate = ref<boolean>(false);

// Mock instant estimate — static demo values only, no real calculation logic.
const MOCK_ESTIMATE = 'Rs. 1,85,00,000';
const MOCK_RANGE = 'Rs. 1,70,00,000 – Rs. 2,05,00,000';

function estimate() {
    if (address.value.trim() === '') return;
    showEstimate.value = true;
}
</script>

<template>
    <section id="home-valuation" class="bg-slate-900">
        <div class="mx-auto grid max-w-6xl grid-cols-1 gap-10 px-4 py-16 lg:grid-cols-2 lg:items-center">
            <div>
                <span class="inline-block rounded-full bg-brand-600/20 px-3 py-1 text-xs font-bold text-brand-300">
                    Instant home value
                </span>
                <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-white">
                    What's your home worth?
                </h2>
                <p class="mt-3 text-base text-slate-300">
                    Get an instant, no-obligation estimate of your property's value based on comparable
                    listings in your area.
                </p>

                <form class="mt-6 flex flex-col gap-3 sm:flex-row" @submit.prevent="estimate">
                    <input
                        v-model="address"
                        placeholder="Enter your property address"
                        class="flex-1 rounded-xl border border-slate-700 bg-slate-800 px-4 py-3.5 text-sm text-white outline-none placeholder:text-slate-400 focus:border-brand-400 focus:ring-4 focus:ring-brand-500/20"
                    />
                    <button
                        type="submit"
                        class="rounded-xl bg-brand-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700"
                    >
                        Get estimate
                    </button>
                </form>
                <p class="mt-2 text-xs text-slate-500">Demo estimate for illustration only.</p>
            </div>

            <div class="rounded-2xl border border-slate-700 bg-slate-800/60 p-8 text-center">
                <template v-if="showEstimate">
                    <div class="text-sm font-semibold text-slate-400">Estimated value</div>
                    <div class="mt-2 text-4xl font-extrabold text-white">{{ MOCK_ESTIMATE }}</div>
                    <div class="mt-3 text-sm text-slate-400">
                        Estimated range
                        <div class="mt-1 font-semibold text-brand-300">{{ MOCK_RANGE }}</div>
                    </div>
                    <div class="mt-4 truncate text-xs text-slate-500">for {{ address }}</div>
                </template>
                <template v-else>
                    <div class="flex h-full min-h-40 flex-col items-center justify-center text-slate-400">
                        <svg class="h-12 w-12 text-slate-600" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-6v-7H10v7H4a1 1 0 0 1-1-1V10.5Z"
                                stroke="currentColor"
                                stroke-width="1.6"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                        <p class="mt-3 text-sm">Enter an address to see an instant estimate.</p>
                    </div>
                </template>
            </div>
        </div>
    </section>
</template>
