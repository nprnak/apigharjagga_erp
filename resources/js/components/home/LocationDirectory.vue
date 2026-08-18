<script setup lang="ts">
import type { ProvinceLocations } from '../../types/marketplace';
import { computed } from 'vue';

const props = defineProps<{
    locations: ProvinceLocations[];
}>();

const sortedProvinces = computed(() =>
    props.locations
        .slice()
        .sort((a, b) => a.province.localeCompare(b.province))
        .map((group) => ({
            province: group.province,
            cities: group.cities.slice().sort((a, b) => a.name.localeCompare(b.name)),
        })),
);
</script>

<template>
    <section class="bg-slate-50">
        <div class="mx-auto max-w-6xl px-4 py-16">
            <div class="max-w-2xl">
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                    Browse by location
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    Explore listed properties by province and city across Nepal.
                </p>
            </div>

            <div
                v-if="sortedProvinces.length"
                class="mt-8 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3"
            >
                <div v-for="group in sortedProvinces" :key="group.province">
                    <h3 class="border-b border-slate-200 pb-2 text-sm font-bold tracking-wide text-slate-900 uppercase">
                        {{ group.province }}
                    </h3>
                    <ul class="mt-3 space-y-1.5">
                        <li v-for="city in group.cities" :key="city.name">
                            <a
                                :href="`/properties?q=${encodeURIComponent(city.name)}`"
                                class="flex items-center justify-between gap-2 text-sm text-slate-600 transition hover:text-brand-700"
                            >
                                <span>{{ city.name }}</span>
                                <span class="text-xs font-semibold text-slate-400">{{ city.count }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <p v-else class="mt-8 text-sm text-slate-500">
                No location data available yet.
            </p>
        </div>
    </section>
</template>
