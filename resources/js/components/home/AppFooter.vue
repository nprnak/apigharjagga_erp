<script setup lang="ts">
const columns = [
    {
        title: 'Company',
        links: [
            { label: 'About us', href: '#' },
            { label: 'Careers', href: '#' },
            { label: 'Sign up', href: '/signup' },
            { label: 'Contact', href: '/signin' },
        ],
    },
    {
        title: 'Resources',
        links: [
            { label: 'Buy a home', href: '/properties' },
            { label: 'Sell a property', href: '/property-listing' },
            { label: 'Home valuation', href: '#home-valuation' },
            { label: 'All listings', href: '/properties' },
        ],
    },
    {
        title: 'Legal',
        links: [
            { label: 'Privacy policy', href: '#' },
            { label: 'Terms of service', href: '#' },
            { label: 'Cookie policy', href: '#' },
            { label: 'Disclaimer', href: '#' },
        ],
    },
];

const socials = [
    {
        label: 'Facebook',
        path: 'M13 22v-8h3l1-4h-4V8a1 1 0 0 1 1-1h3V3h-3a5 5 0 0 0-5 5v2H7v4h2v8h4Z',
    },
    {
        label: 'Twitter',
        path: 'M22 5.9c-.7.3-1.5.6-2.3.7.8-.5 1.4-1.3 1.7-2.2-.8.5-1.7.8-2.6 1a4 4 0 0 0-6.9 3.6A11.3 11.3 0 0 1 3.6 4.8a4 4 0 0 0 1.2 5.3c-.6 0-1.2-.2-1.8-.5a4 4 0 0 0 3.2 3.9c-.5.2-1.1.2-1.7.1a4 4 0 0 0 3.7 2.8A8 8 0 0 1 2 18.6a11.3 11.3 0 0 0 6.1 1.8c7.3 0 11.4-6.1 11.4-11.4v-.5c.8-.6 1.5-1.3 2-2.1Z',
    },
    {
        label: 'Instagram',
        path: 'M12 2c2.7 0 3 0 4.1.1 1 0 1.7.2 2.3.5a4.6 4.6 0 0 1 1.7 1.1 4.6 4.6 0 0 1 1.1 1.7c.3.6.5 1.3.5 2.3.1 1.1.1 1.4.1 4.1s0 3-.1 4.1c0 1-.2 1.7-.5 2.3a4.6 4.6 0 0 1-1.1 1.7 4.6 4.6 0 0 1-1.7 1.1c-.6.3-1.3.5-2.3.5-1.1.1-1.4.1-4.1.1s-3 0-4.1-.1c-1 0-1.7-.2-2.3-.5a4.6 4.6 0 0 1-1.7-1.1 4.6 4.6 0 0 1-1.1-1.7c-.3-.6-.5-1.3-.5-2.3C2 15 2 14.7 2 12s0-3 .1-4.1c0-1 .2-1.7.5-2.3a4.6 4.6 0 0 1 1.1-1.7 4.6 4.6 0 0 1 1.7-1.1c.6-.3 1.3-.5 2.3-.5C9 2 9.3 2 12 2Zm0 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm0 8.2a3.2 3.2 0 1 1 0-6.4 3.2 3.2 0 0 1 0 6.4Zm5.2-8.4a1.2 1.2 0 1 1-2.4 0 1.2 1.2 0 0 1 2.4 0Z',
    },
    {
        label: 'LinkedIn',
        path: 'M6.9 8.8H3.6V21h3.3V8.8Zm.2-4A1.9 1.9 0 1 0 3.3 4.8a1.9 1.9 0 0 0 3.8 0ZM21 21v-6.7c0-3.2-1.7-4.7-4-4.7a3.5 3.5 0 0 0-3.1 1.7V8.8H10.6c0 .9 0 12.2 0 12.2h3.3v-6.8c0-.4 0-.7.1-1a1.8 1.8 0 0 1 1.7-1.2c1.2 0 1.7.9 1.7 2.3V21H21Z',
    },
];
</script>

<template>
    <footer class="bg-slate-950 text-slate-300">
        <div class="mx-auto max-w-6xl px-4 py-14">
            <div class="grid grid-cols-1 gap-10 lg:grid-cols-4">
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white p-1">
                            <img src="/images/logo.png" alt="Api Ghar Jagga" class="h-full w-full object-contain" />
                        </span>
                        <div class="text-base font-bold text-white">Api Ghar Jagga</div>
                    </div>
                    <p class="mt-4 max-w-xs text-sm text-slate-400">
                        Nepal's trusted marketplace for verified homes, land, and commercial property.
                    </p>

                    <div class="mt-5 flex items-center gap-3">
                        <a
                            v-for="social in socials"
                            :key="social.label"
                            :href="'#'"
                            :aria-label="social.label"
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-800 text-slate-300 transition hover:bg-brand-600 hover:text-white"
                        >
                            <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path :d="social.path" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8 sm:grid-cols-3 lg:col-span-3">
                    <div v-for="col in columns" :key="col.title">
                        <h4 class="text-sm font-bold tracking-wide text-white uppercase">{{ col.title }}</h4>
                        <ul class="mt-4 space-y-2.5">
                            <li v-for="link in col.links" :key="link.label">
                                <a :href="link.href" class="text-sm text-slate-400 transition hover:text-white">
                                    {{ link.label }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-12 border-t border-slate-800 pt-6 text-center text-xs text-slate-500">
                &copy; {{ new Date().getFullYear() }} Api Ghar Jagga. All rights reserved.
            </div>
        </div>
    </footer>
</template>
