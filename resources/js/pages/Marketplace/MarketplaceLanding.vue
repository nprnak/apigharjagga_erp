<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import type { Listing, ProvinceLocations } from '../../types/marketplace';
import AppHeader from '../../components/home/AppHeader.vue';
import HeroSearch from '../../components/home/HeroSearch.vue';
import PropertyCarousel from '../../components/home/PropertyCarousel.vue';
import ValuePropGrid from '../../components/home/ValuePropGrid.vue';
import HomeValuation from '../../components/home/HomeValuation.vue';
import LocationDirectory from '../../components/home/LocationDirectory.vue';
import AppFooter from '../../components/home/AppFooter.vue';

const props = withDefaults(
    defineProps<{
        featuredListings: Listing[];
        locations?: ProvinceLocations[];
    }>(),
    {
        locations: () => [],
    },
);
</script>

<template>
    <Head title="Real Estate Nepal" />

    <div class="min-h-screen bg-slate-50 text-slate-900">
        <AppHeader />
        <HeroSearch />
        <PropertyCarousel :listings="props.featuredListings" />
        <ValuePropGrid />
        <HomeValuation />
        <LocationDirectory :locations="props.locations" />
        <AppFooter />
    </div>
</template>
