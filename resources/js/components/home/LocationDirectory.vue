<script setup lang="ts">
import type { ProvinceLocations } from '../../types/marketplace';
import { computed, ref } from 'vue';

const props = defineProps<{
    locations: ProvinceLocations[];
}>();

const expanded = ref(true);

const allCities = computed(() => {
    const seen = new Set<string>();
    const cities: { name: string; province: string }[] = [];

    for (const group of props.locations) {
        for (const city of group.cities) {
            const key = city.name.toLowerCase();
            if (seen.has(key)) continue;
            seen.add(key);
            cities.push({ name: city.name, province: group.province });
        }
    }

    return cities.sort((a, b) => a.name.localeCompare(b.name));
});
</script>

<template>
    <section class="border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-3">
                <h2 class="text-[11px] font-medium tracking-[0.18em] text-slate-600 uppercase sm:text-xs">
                    Find a home by location in Nepal
                </h2>

                <button
                    type="button"
                    :aria-expanded="expanded"
                    aria-label="Toggle location list"
                    class="flex h-6 w-6 shrink-0 items-center justify-center text-slate-500 transition hover:text-slate-800"
                    @click="expanded = !expanded"
                >
                    <svg v-if="expanded" class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <svg v-else class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>
            </div>

            <div
                v-if="allCities.length && expanded"
                class="mt-6 grid grid-cols-2 gap-x-6 gap-y-2.5 sm:grid-cols-3 lg:grid-cols-4"
            >
                <a
                    v-for="city in allCities"
                    :key="city.name"
                    :href="`/properties?q=${encodeURIComponent(city.name)}`"
                    class="text-sm text-slate-800 transition hover:text-brand-600 hover:underline"
                >
                    {{ city.name }}
                </a>
            </div>

            <p v-else-if="!allCities.length" class="mt-6 text-sm text-slate-500">
                No location data available yet.
            </p>
        </div>
    </section>
</template>
