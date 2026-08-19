<script setup lang="ts">
import ValuePropCard from './ValuePropCard.vue';

const cards = [
    {
        icon: 'search',
        heading: 'Buy a home',
        pitch: 'Browse verified homes, land, and commercial property with transparent pricing and real listing records.',
        ctaLabel: 'Search homes',
        ctaHref: '/properties',
        theme: 'blue' as const,
        illustration: 'house' as const,
    },
    {
        icon: 'tag',
        heading: 'Sell a property',
        pitch: 'List your property with us and reach genuine buyers backed by proper documentation and verification.',
        ctaLabel: 'List a property',
        ctaHref: '/property-listing',
        theme: 'green' as const,
        illustration: 'phone' as const,
    },
    {
        icon: 'shield',
        heading: 'Verified listings',
        pitch: 'Every listing is checked against ownership certificates and land records before it goes live.',
        ctaLabel: 'Browse listings',
        ctaHref: '/properties',
        theme: 'purple' as const,
        illustration: 'document' as const,
    },
];
</script>

<template>
    <section class="bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:py-20">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="font-serif text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    Everything you need to move
                </h2>
                <p class="mt-3 text-sm text-slate-500 sm:text-base">
                    Whether you're buying, selling, or just exploring, we make the process simple.
                </p>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-6 lg:grid-cols-3">
                <ValuePropCard
                    v-for="card in cards"
                    :key="card.heading"
                    :icon="card.icon"
                    :heading="card.heading"
                    :pitch="card.pitch"
                    :cta-label="card.ctaLabel"
                    :cta-href="card.ctaHref"
                    :theme="card.theme"
                    :illustration="card.illustration"
                />
            </div>
        </div>
    </section>
</template>
