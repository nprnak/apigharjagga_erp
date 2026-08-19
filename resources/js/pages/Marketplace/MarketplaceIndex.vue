<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type Listing = {
    listing_id: number;
    application_no: string;
    purpose: string;
    price: number | string | null;
    negotiable: boolean;
    property_code: string | null;
    property_type: string | null;
    area: string | null;
    municipality: string | null;
    photo_url?: string | null;
};

type CityOption = {
    value: string;
    label: string;
    type: string;
};

const props = defineProps<{
    listings: Listing[];
    cityOptions: CityOption[];
    filters: { q: string; city: string; purpose: string };
}>();

const q = ref<string>(props.filters.q ?? '');
const city = ref<string>(props.filters.city ?? '');
const purpose = ref<string>(props.filters.purpose ?? '');

const purposeOptions = [
    { value: '', label: 'All purposes' },
    { value: 'sale', label: 'Sale' },
    { value: 'rent', label: 'Rent' },
    { value: 'lease', label: 'Lease' },
    { value: 'exchange', label: 'Exchange' },
] as const;

const sortedCityOptions = computed(() =>
    props.cityOptions.slice().sort((a, b) => a.label.localeCompare(b.label)),
);

function formatPrice(value: Listing['price']): string {
    if (value === null || value === undefined || value === '') return 'Contact for price';
    const n = Number(value);
    if (Number.isNaN(n)) return String(value);
    return 'Rs. ' + new Intl.NumberFormat('en-IN').format(n);
}

function search() {
    router.get('/properties', {
        q: q.value,
        city: city.value,
        purpose: purpose.value,
    });
}
</script>

<template>
    <Head title="Properties" />

    <div class="min-h-screen bg-[#F7FAFC] text-slate-900">
        <!-- Header -->
        <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M3 10.5L12 3l9 7.5V21a1 1 0 0 1-1 1h-6v-7H10v7H4a1 1 0 0 1-1-1V10.5Z"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <div class="text-sm font-semibold">Api Ghar Jagga</div>
                        <div class="text-xs text-slate-500">Property Marketplace</div>
                    </div>
                </div>

                <nav class="hidden items-center gap-6 sm:flex">
                    <a
                        href="/"
                        class="text-sm font-medium text-slate-700 hover:text-slate-900"
                        >Home</a
                    >
                    <a
                        href="/signin"
                        class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >Sign In</a
                    >
                </nav>
            </div>
        </header>

        <section class="mx-auto max-w-6xl px-4 py-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
                <!-- Filters -->
                <aside class="w-full lg:w-72">
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="text-base font-extrabold text-slate-900">Search</h2>
                        <p class="mt-1 text-sm text-slate-500">Find listings by city and purpose.</p>

                        <div class="mt-5 space-y-4">
                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">
                                    City (Metro/Sub-Metro)
                                </label>
                                <select
                                    v-model="city"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 outline-none focus:border-emerald-400 focus:ring-4 focus:ring-emerald-500/10"
                                >
                                    <option value="">All cities</option>
                                    <option
                                        v-for="opt in sortedCityOptions"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Search
                                </label>
                                <input
                                    v-model="q"
                                    placeholder="Property code or municipality"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none placeholder:text-slate-400 focus:border-emerald-400 focus:ring-4 focus:ring-emerald-500/10"
                                />
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Purpose
                                </label>
                                <select
                                    v-model="purpose"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 outline-none focus:border-emerald-400 focus:ring-4 focus:ring-emerald-500/10"
                                >
                                    <option v-for="opt in purposeOptions" :key="opt.value" :value="opt.value">
                                        {{ opt.label }}
                                    </option>
                                </select>
                            </div>

                            <button
                                type="button"
                                @click="search"
                                class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-slate-900/20 transition hover:-translate-y-0.5 hover:bg-slate-800"
                            >
                                Search
                            </button>

                            <a
                                href="/properties"
                                class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-center text-sm font-bold text-slate-700 hover:bg-slate-50"
                            >
                                Clear
                            </a>
                        </div>
                    </div>
                </aside>

                <!-- Results -->
                <div class="flex-1">
                    <div class="mb-6 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-extrabold tracking-tight text-slate-900">Property Listings</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Showing {{ listings.length }} approved listing(s)
                            </p>
                        </div>
                        <div class="hidden sm:block">
                            <a
                                href="/property-listing"
                                class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-700"
                            >
                                Submit a new listing
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <article
                            v-for="item in listings"
                            :key="item.listing_id"
                            class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow"
                        >
                            <div class="relative h-40 bg-slate-100 overflow-hidden">
                                <img
                                    :src="item.photo_url || `https://picsum.photos/seed/property-${item.property_code || item.listing_id}/480/320`"
                                    :alt="item.property_code ?? 'Property'"
                                    class="h-full w-full object-cover"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                <div class="absolute inset-x-0 bottom-3 flex items-center justify-between px-4">
                                    <span
                                        class="rounded-full bg-emerald-600 px-3 py-1 text-xs font-bold text-white shadow"
                                    >
                                        {{ item.purpose ? (item.purpose.charAt(0).toUpperCase() + item.purpose.slice(1)) : 'For Sale' }}
                                    </span>
                                    <span class="text-xs font-bold text-white drop-shadow">AGJ Listing</span>
                                </div>
                            </div>

                            <div class="p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-extrabold text-slate-900">
                                            {{ item.property_code ?? 'Property' }}
                                        </div>
                                        <div class="mt-1 text-sm font-semibold text-slate-600">
                                            {{ item.property_type ?? 'Property' }} • {{ item.area ?? 'Area N/A' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="font-semibold text-slate-800">
                                        {{ item.municipality ?? 'Location not set' }}
                                    </div>
                                    <div class="mt-1 text-sm text-slate-500">
                                        App No: {{ item.application_no }}
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center justify-between gap-3">
                                    <div class="text-sm font-extrabold text-slate-900">
                                        {{ formatPrice(item.price) }}
                                    </div>
                                    <div
                                        class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-bold text-slate-700"
                                    >
                                        {{ item.negotiable ? 'Negotiable' : 'Fixed' }}
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <p v-if="listings.length === 0" class="mt-12 text-center text-sm text-slate-500">
                        No approved listings matched your filters yet.
                    </p>
                </div>
            </div>
        </section>
    </div>
</template>

