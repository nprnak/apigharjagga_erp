<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

type Tab = 'find' | 'value';

const tabs: { id: Tab; label: string }[] = [
    { id: 'find', label: 'Find a home' },
    { id: 'value', label: 'My home value' },
];

const activeTab = ref<Tab>('find');
const query = ref<string>('');

function submit() {
    if (activeTab.value === 'value') {
        document.getElementById('home-valuation')?.scrollIntoView({ behavior: 'smooth' });
        return;
    }
    router.get('/properties', { q: query.value });
}

const placeholder: Record<Tab, string> = {
    find: 'Enter an address, city, or zip',
    value: 'Enter your property address',
};
</script>

<template>
    <section class="relative flex min-h-[88vh] items-center justify-center overflow-hidden">
        <div
            class="absolute inset-0 bg-cover bg-center"
            style="
                background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=2000&q=80');
            "
        ></div>
        <div class="absolute inset-0 bg-slate-900/35"></div>

        <div class="relative w-full max-w-3xl px-4 pt-20 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white drop-shadow-sm sm:text-6xl">
                Your next home is here
            </h1>

            <div class="mx-auto mt-10 max-w-2xl">
                <div class="overflow-hidden rounded-2xl bg-white shadow-2xl shadow-black/25">
                    <div class="flex items-center justify-center gap-8 border-b border-slate-100 px-4 pt-4">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            type="button"
                            @click="activeTab = tab.id"
                            :class="[
                                'relative pb-3 text-sm font-semibold transition-colors',
                                activeTab === tab.id
                                    ? 'text-brand-600'
                                    : 'text-slate-500 hover:text-slate-800',
                            ]"
                        >
                            {{ tab.label }}
                            <span
                                v-if="activeTab === tab.id"
                                class="absolute inset-x-0 -bottom-px h-0.5 rounded-full bg-brand-600"
                            ></span>
                        </button>
                    </div>

                    <form class="relative flex items-center p-3" @submit.prevent="submit">
                        <input
                            v-model="query"
                            :placeholder="placeholder[activeTab]"
                            class="w-full rounded-xl bg-white py-3 pr-14 pl-4 text-left text-sm text-slate-900 outline-none placeholder:text-slate-400"
                        />
                        <button
                            type="submit"
                            aria-label="Search"
                            class="absolute right-3 flex h-10 w-10 items-center justify-center rounded-full bg-brand-600 text-white shadow-md transition hover:bg-brand-700"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>
