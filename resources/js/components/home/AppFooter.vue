<script setup lang="ts">
const companyLinks = [
    { label: 'About', href: '#' },
    { label: 'Contact', href: '/signin' },
    { label: 'Press', href: '#' },
    { label: 'Leadership', href: '#' },
    { label: 'Diversity', href: '#' },
    { label: 'Giving Back', href: '#' },
];

const resourceLinks = [
    { label: 'Buy a home', href: '/properties' },
    { label: 'Home valuation', href: '#home-valuation' },
    { label: 'Sell a property', href: '/property-listing' },
    { label: 'All listings', href: '/properties' },
];

const joinLinks = [
    { label: 'Careers', href: '#' },
    { label: 'Sign up', href: '/signup' },
    { label: 'Culture', href: '#' },
    { label: 'Partner with us', href: '#' },
];

const serviceLinks = [
    { label: 'Verified listings', href: '/properties' },
    { label: 'Land & plots', href: '/properties' },
    { label: 'Commercial property', href: '/properties' },
];

const legalLinks = [
    { label: 'Terms', href: '#' },
    { label: 'Privacy', href: '#' },
    { label: 'Fair housing', href: '#' },
    { label: 'Cookie preferences', href: '#' },
    { label: 'Fraud alert', href: '#' },
    { label: 'Disclaimer', href: '#' },
];

const socials = [
    {
        label: 'Facebook',
        path: 'M13 22v-8h3l1-4h-4V8a1 1 0 0 1 1-1h3V3h-3a5 5 0 0 0-5 5v2H7v4h2v8h4Z',
    },
    {
        label: 'Instagram',
        path: 'M12 2c2.7 0 3 0 4.1.1 1 0 1.7.2 2.3.5a4.6 4.6 0 0 1 1.7 1.1 4.6 4.6 0 0 1 1.1 1.7c.3.6.5 1.3.5 2.3.1 1.1.1 1.4.1 4.1s0 3-.1 4.1c0 1-.2 1.7-.5 2.3a4.6 4.6 0 0 1-1.1 1.7 4.6 4.6 0 0 1-1.7 1.1c-.6.3-1.3.5-2.3.5-1.1.1-1.4.1-4.1.1s-3 0-4.1-.1c-1 0-1.7-.2-2.3-.5a4.6 4.6 0 0 1-1.7-1.1 4.6 4.6 0 0 1-1.1-1.7c-.3-.6-.5-1.3-.5-2.3C2 15 2 14.7 2 12s0-3 .1-4.1c0-1 .2-1.7.5-2.3a4.6 4.6 0 0 1 1.1-1.7 4.6 4.6 0 0 1 1.7-1.1c.6-.3 1.3-.5 2.3-.5C9 2 9.3 2 12 2Zm0 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm0 8.2a3.2 3.2 0 1 1 0-6.4 3.2 3.2 0 0 1 0 6.4Zm5.2-8.4a1.2 1.2 0 1 1-2.4 0 1.2 1.2 0 0 1 2.4 0Z',
    },
    {
        label: 'YouTube',
        path: 'M21.8 8.001a2.5 2.5 0 0 0-1.76-1.77C18.36 6 12 6 12 6s-6.36 0-8.04.231A2.5 2.5 0 0 0 2.2 8c-.231 1.68-.231 5.2-.231 5.2s0 3.52.231 5.2a2.5 2.5 0 0 0 1.76 1.77C5.64 20 12 20 12 20s6.36 0 8.04-.231a2.5 2.5 0 0 0 1.76-1.77c.231-1.68.231-5.2.231-5.2s0-3.52-.231-5.2ZM10 15.5v-7l6 3.5-6 3.5Z',
    },
    {
        label: 'LinkedIn',
        path: 'M6.9 8.8H3.6V21h3.3V8.8Zm.2-4A1.9 1.9 0 1 0 3.3 4.8a1.9 1.9 0 0 0 3.8 0ZM21 21v-6.7c0-3.2-1.7-4.7-4-4.7a3.5 3.5 0 0 0-3.1 1.7V8.8H10.6c0 .9 0 12.2 0 12.2h3.3v-6.8c0-.4 0-.7.1-1a1.8 1.8 0 0 1 1.7-1.2c1.2 0 1.7.9 1.7 2.3V21H21Z',
    },
];
</script>

<template>
    <footer class="text-white">
        <!-- Top footer -->
        <div class="bg-[#1a365d]">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:py-14">
                <div class="grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-8">
                    <!-- Branding -->
                    <div class="lg:col-span-4">
                        <div class="flex items-center gap-4">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded bg-white p-1">
                                <img src="/images/logo.png" alt="Api Ghar Jagga" class="h-full w-full object-contain" />
                            </span>
                            <div class="h-10 w-px bg-white/30" aria-hidden="true" />
                            <div class="text-lg font-bold tracking-tight">Api Ghar Jagga</div>
                        </div>

                        <p class="mt-5 max-w-sm text-[11px] leading-relaxed tracking-[0.12em] text-white/80 uppercase">
                            Nepal's trusted marketplace for verified homes, land, and commercial property.
                        </p>

                        <div class="mt-6 flex items-center gap-4">
                            <a
                                v-for="social in socials"
                                :key="social.label"
                                :href="'#'"
                                :aria-label="social.label"
                                class="text-white/90 transition hover:text-white"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path :d="social.path" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Link columns -->
                    <div class="grid grid-cols-2 gap-8 sm:grid-cols-4 lg:col-span-8">
                        <div>
                            <h4 class="text-xs font-bold tracking-[0.14em] uppercase">Company</h4>
                            <ul class="mt-4 space-y-2">
                                <li v-for="link in companyLinks" :key="link.label">
                                    <a
                                        :href="link.href"
                                        class="text-sm text-white/75 transition hover:text-white hover:underline"
                                    >
                                        {{ link.label }}
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold tracking-[0.14em] uppercase">Resources</h4>
                            <ul class="mt-4 space-y-2">
                                <li v-for="link in resourceLinks" :key="link.label">
                                    <a
                                        :href="link.href"
                                        class="text-sm text-white/75 transition hover:text-white hover:underline"
                                    >
                                        {{ link.label }}
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold tracking-[0.14em] uppercase">Join Api Ghar Jagga</h4>
                            <ul class="mt-4 space-y-2">
                                <li v-for="link in joinLinks" :key="link.label">
                                    <a
                                        :href="link.href"
                                        class="text-sm text-white/75 transition hover:text-white hover:underline"
                                    >
                                        {{ link.label }}
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <ul class="mt-6 space-y-2 sm:mt-0 lg:mt-[1.85rem]">
                                <li v-for="link in serviceLinks" :key="link.label">
                                    <a
                                        :href="link.href"
                                        class="text-sm text-white/75 transition hover:text-white hover:underline"
                                    >
                                        {{ link.label }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom legal footer -->
        <div class="bg-[#0f1f35]">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
                <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-white/70">
                    <a href="#" class="transition hover:text-white hover:underline">Terms of use</a>
                    <a href="#" class="transition hover:text-white hover:underline">Privacy notice</a>
                    <a href="#" class="transition hover:text-white hover:underline">Privacy center</a>
                </div>

                <div class="my-5 border-t border-white/15" />

                <div class="flex flex-wrap gap-x-4 gap-y-2">
                    <a
                        v-for="link in legalLinks"
                        :key="link.label"
                        :href="link.href"
                        class="text-[11px] text-white/60 transition hover:text-white hover:underline"
                    >
                        {{ link.label }}
                    </a>
                </div>

                <p class="mt-6 text-[11px] leading-relaxed text-white/50">
                    Api Ghar Jagga provides a platform for verified property listings across Nepal. Information
                    displayed on this site is supplied by property owners and agents and has not been verified
                    independently unless marked as verified. All property details, prices, and availability are
                    subject to change without notice.
                </p>

                <p class="mt-4 text-[11px] text-white/45">
                    &copy; {{ new Date().getFullYear() }} Api Ghar Jagga. All rights reserved. Api Ghar Jagga
                    and related marks are trademarks of Api Ghar Jagga.
                </p>
            </div>
        </div>
    </footer>
</template>
