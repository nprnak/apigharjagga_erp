<script setup lang="ts">
import ValuePropCard from './ValuePropCard.vue';

const cards = [
    {
        icon: 'search',
        heading: 'Buy a home',
        pitch: 'Browse verified homes, land, and commercial property with transparent pricing and real listing records.',
        ctaLabel: 'Search homes',
        ctaHref: '/properties',
    },
    {
        icon: 'tag',
        heading: 'Sell a property',
        pitch: 'List your property with us and reach genuine buyers backed by proper documentation and verification.',
        ctaLabel: 'List a property',
        ctaHref: '/property-listing',
    },
    {
        icon: 'shield',
        heading: 'Verified listings',
        pitch: 'Every listing is checked against ownership certificates and land records before it goes live.',
        ctaLabel: 'Browse listings',
        ctaHref: '/properties',
    },
];
</script>

<template>
    <section class="bg-white">
        <div class="mx-auto max-w-6xl px-4 py-16">
            <div class="max-w-2xl">
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                    Everything you need to move
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    Whether you're buying, selling, or just exploring, we make the process simple.
                </p>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <ValuePropCard
                    v-for="card in cards"
                    :key="card.heading"
                    :icon="card.icon"
                    :heading="card.heading"
                    :pitch="card.pitch"
                    :cta-label="card.ctaLabel"
                    :cta-href="card.ctaHref"
                />
            </div>
        </div>
    </section>
</template>
