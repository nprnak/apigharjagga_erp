<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onUnmounted, ref, watch } from 'vue';

type Tab = 'find' | 'value';
type Suggestion = { label: string; type: string };

const tabs: { id: Tab; label: string }[] = [
    { id: 'find', label: 'Find a home' },
    { id: 'value', label: 'My home value' },
];

const activeTab = ref<Tab>('find');
const query = ref<string>('');

// Typeahead state
const suggestions = ref<Suggestion[]>([]);
const showSuggestions = ref<boolean>(false);
const activeIndex = ref<number>(-1);
let debounceTimer: ReturnType<typeof setTimeout> | null = null;

async function fetchSuggestions(term: string) {
    try {
        const res = await fetch(`/locations/suggest?q=${encodeURIComponent(term)}`, {
            headers: { Accept: 'application/json' },
        });
        if (!res.ok) return;
        const data = (await res.json()) as Suggestion[];
        // Ignore stale responses if the user kept typing.
        if (query.value.trim() !== term) return;
        suggestions.value = data;
        activeIndex.value = -1;
        showSuggestions.value = activeTab.value === 'find' && data.length > 0;
    } catch {
        // Network hiccup — silently keep the box usable.
    }
}

watch(query, (val) => {
    const term = val.trim();
    if (debounceTimer) clearTimeout(debounceTimer);

    if (activeTab.value !== 'find' || term.length < 1) {
        suggestions.value = [];
        showSuggestions.value = false;
        return;
    }

    debounceTimer = setTimeout(() => fetchSuggestions(term), 200);
});

function goToResults(term: string) {
    const clean = term.trim();
    router.get('/properties', clean ? { q: clean } : {});
}

function submit() {
    if (activeTab.value === 'value') {
        document.getElementById('home-valuation')?.scrollIntoView({ behavior: 'smooth' });
        return;
    }
    showSuggestions.value = false;
    goToResults(query.value);
}

function selectSuggestion(s: Suggestion) {
    query.value = s.label;
    showSuggestions.value = false;
    goToResults(s.label);
}

function selectTab(id: Tab) {
    activeTab.value = id;
    if (id !== 'find') showSuggestions.value = false;
}

function onFocus() {
    if (activeTab.value === 'find' && suggestions.value.length > 0) {
        showSuggestions.value = true;
    }
}

function onBlur() {
    // Delay so a click on a suggestion registers before the list hides.
    setTimeout(() => {
        showSuggestions.value = false;
    }, 150);
}

function onArrowDown() {
    if (!showSuggestions.value || suggestions.value.length === 0) return;
    activeIndex.value = (activeIndex.value + 1) % suggestions.value.length;
}

function onArrowUp() {
    if (!showSuggestions.value || suggestions.value.length === 0) return;
    activeIndex.value =
        (activeIndex.value - 1 + suggestions.value.length) % suggestions.value.length;
}

function onEnter(e: KeyboardEvent) {
    if (showSuggestions.value && activeIndex.value >= 0) {
        e.preventDefault();
        selectSuggestion(suggestions.value[activeIndex.value]);
    }
    // Otherwise the form's @submit.prevent handler runs submit().
}

onUnmounted(() => {
    if (debounceTimer) clearTimeout(debounceTimer);
});

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
                background-image: url('/images/home.jpg');
            "
        ></div>
        <div class="absolute inset-0 bg-slate-900/35"></div>

        <div class="relative w-full max-w-3xl px-4 pt-20 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white drop-shadow-sm sm:text-6xl">
                Your next home is here
            </h1>

            <div class="mx-auto mt-10 max-w-2xl">
                <!-- Relative wrapper so the suggestions panel can sit BELOW the
                     card without being clipped by the card's overflow-hidden. -->
                <div class="relative">
                    <div class="overflow-hidden rounded-2xl bg-white shadow-2xl shadow-black/25">
                        <div class="flex items-center justify-center gap-8 border-b border-slate-100 px-4 pt-4">
                            <button
                                v-for="tab in tabs"
                                :key="tab.id"
                                type="button"
                                @click="selectTab(tab.id)"
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
                                autocomplete="off"
                                role="combobox"
                                aria-autocomplete="list"
                                :aria-expanded="showSuggestions"
                                @focus="onFocus"
                                @blur="onBlur"
                                @keydown.down.prevent="onArrowDown"
                                @keydown.up.prevent="onArrowUp"
                                @keydown.enter="onEnter"
                                @keydown.esc="showSuggestions = false"
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

                    <!-- Location suggestions dropdown -->
                    <ul
                        v-if="showSuggestions && suggestions.length"
                        class="absolute inset-x-0 top-full z-50 mt-2 overflow-hidden rounded-2xl bg-white py-2 text-left shadow-2xl shadow-black/25 ring-1 ring-black/5"
                    >
                        <li
                            v-for="(s, i) in suggestions"
                            :key="s.type + s.label"
                            @mousedown.prevent="selectSuggestion(s)"
                            @mouseenter="activeIndex = i"
                            :class="[
                                'flex cursor-pointer items-center gap-3 px-4 py-2.5 transition-colors',
                                activeIndex === i ? 'bg-brand-50' : 'hover:bg-slate-50',
                            ]"
                        >
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                            </span>
                            <span class="flex-1 text-sm font-semibold text-slate-800">{{ s.label }}</span>
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ s.type }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</template>
