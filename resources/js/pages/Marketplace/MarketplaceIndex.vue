<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

type Listing = {
    listing_id: number;
    application_no: string;
    purpose: string;
    price: number | string | null;
    negotiable: boolean;
    property_code: string | null;
    property_type: string | null;
    area: string | null;
    covered_area?: string | null;
    no_of_floors?: number | null;
    status?: string | null;
    municipality: string | null;
    district?: string | null;
    province?: string | null;
    photo_url?: string | null;
    photos?: string[];
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

// Search & Filter State
const q = ref<string>(props.filters.q ?? '');
const city = ref<string>(props.filters.city ?? '');
const purpose = ref<string>(props.filters.purpose ?? '');
const selectedPropertyType = ref<string>('');
const minPrice = ref<number | null>(null);
const maxPrice = ref<number | null>(null);
const sortBy = ref<string>('newest');

// UI Dropdown states
const activeDropdown = ref<'price' | 'type' | 'sort' | null>(null);
const isAllFiltersOpen = ref(false);
const favorites = ref<Set<number>>(new Set());
const activeMobileView = ref<'list' | 'map'>('list');

// Toast feedback
const toastMessage = ref<string | null>(null);
let toastTimer: ReturnType<typeof setTimeout> | null = null;
function showToast(msg: string) {
    toastMessage.value = msg;
    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
        toastMessage.value = null;
    }, 3000);
}

function openPropertyDetails(item: Listing) {
    router.visit(`/properties/${item.listing_id}`);
}

function toggleFavorite(id: number) {
    if (favorites.value.has(id)) {
        favorites.value.delete(id);
        showToast('Removed from saved properties');
    } else {
        favorites.value.add(id);
        showToast('Saved to your favorite properties!');
    }
}

function toggleDropdown(name: 'price' | 'type' | 'sort') {
    activeDropdown.value = activeDropdown.value === name ? null : name;
}

function closeDropdowns() {
    activeDropdown.value = null;
}

const propertyTypes = [
    { value: '', label: 'All Property Types' },
    { value: 'house', label: 'House / Residential' },
    { value: 'land', label: 'Land / Plot' },
    { value: 'apartment', label: 'Apartment / Flat' },
    { value: 'commercial_building', label: 'Commercial Building' },
    { value: 'office_space', label: 'Office Space' },
    { value: 'agricultural_land', label: 'Agricultural Land' },
];

const purposeOptions = [
    { value: '', label: 'All Purposes' },
    { value: 'sale', label: 'For Sale' },
    { value: 'rent', label: 'For Rent' },
    { value: 'lease', label: 'For Lease' },
    { value: 'exchange', label: 'Exchange' },
];

const sortedCityOptions = computed(() =>
    props.cityOptions.slice().sort((a, b) => a.label.localeCompare(b.label)),
);

const activeFilterCount = computed(() => {
    let count = 0;
    if (city.value) count++;
    if (purpose.value) count++;
    if (selectedPropertyType.value) count++;
    if (minPrice.value || maxPrice.value) count++;
    if (q.value) count++;
    return count;
});

// Client-side filtering & sorting on top of backend results
const processedListings = computed(() => {
    let list = [...props.listings];

    if (selectedPropertyType.value) {
        list = list.filter((item) =>
            item.property_type?.toLowerCase().includes(selectedPropertyType.value.toLowerCase()),
        );
    }

    if (minPrice.value !== null && minPrice.value > 0) {
        list = list.filter((item) => {
            const p = Number(item.price);
            return !isNaN(p) && p >= (minPrice.value ?? 0);
        });
    }

    if (maxPrice.value !== null && maxPrice.value > 0) {
        list = list.filter((item) => {
            const p = Number(item.price);
            return !isNaN(p) && p <= (maxPrice.value ?? 0);
        });
    }

    // Sort
    if (sortBy.value === 'price_asc') {
        list.sort((a, b) => (Number(a.price) || 0) - (Number(b.price) || 0));
    } else if (sortBy.value === 'price_desc') {
        list.sort((a, b) => (Number(b.price) || 0) - (Number(a.price) || 0));
    } else {
        // Newest
        list.sort((a, b) => b.listing_id - a.listing_id);
    }

    return list;
});

const activeLocationTitle = computed(() => {
    if (city.value) {
        return `${city.value} Real Estate & Properties`;
    }
    if (q.value) {
        return `Properties matching "${q.value}"`;
    }
    return 'Nepal Real Estate & Properties for Sale';
});

function formatPrice(value: Listing['price'], purposeStr?: string): string {
    if (value === null || value === undefined || value === '') return 'Contact for price';
    const n = Number(value);
    if (Number.isNaN(n)) return String(value);
    const formatted = 'Rs. ' + new Intl.NumberFormat('en-IN').format(n);
    if (purposeStr === 'rent') return formatted + ' / month';
    return formatted;
}

function formatSpecs(item: Listing): string {
    const parts: string[] = [];
    if (item.no_of_floors) parts.push(`${item.no_of_floors} Floors`);
    if (item.covered_area) parts.push(item.covered_area);
    else if (item.area) parts.push(item.area);

    const typeStr = item.property_type
        ? item.property_type
              .split('_')
              .map((w) => w.charAt(0).toUpperCase() + w.slice(1))
              .join(' ')
        : 'Property';
    parts.push(typeStr);

    return parts.join(' · ');
}

function formatAddress(item: Listing): string {
    const parts = [item.municipality, item.district, item.province].filter(Boolean);
    return parts.length ? parts.join(', ') : 'Nepal';
}

function formatTypeLabel(t: string | null | undefined): string {
    if (!t) return 'Residential Property';
    return t.split('_').map((w) => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
}

function executeSearch() {
    closeDropdowns();
    router.get(
        '/properties',
        {
            q: q.value,
            city: city.value,
            purpose: purpose.value,
        },
        { preserveState: true, preserveScroll: true },
    );
}

function clearSearchInput() {
    q.value = '';
    executeSearch();
}

function resetAllFilters() {
    q.value = '';
    city.value = '';
    purpose.value = '';
    selectedPropertyType.value = '';
    minPrice.value = null;
    maxPrice.value = null;
    isAllFiltersOpen.value = false;
    closeDropdowns();
    router.get('/properties');
}

// Global click & Escape
function handleClickOutside(e: MouseEvent) {
    const target = e.target as HTMLElement;
    if (!target.closest('.dropdown-container')) {
        closeDropdowns();
    }
}

function handleKeydown(e: KeyboardEvent) {
    if (e.key === 'Escape') {
        isAllFiltersOpen.value = false;
        closeDropdowns();
    }
}

onMounted(() => {
    window.addEventListener('click', handleClickOutside);
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('click', handleClickOutside);
    window.removeEventListener('keydown', handleKeydown);
    if (toastTimer) clearTimeout(toastTimer);
});
</script>

<template>
    <Head :title="activeLocationTitle" />

    <!-- Main Container -->
    <div class="min-h-screen flex flex-col bg-white text-slate-900 font-sans antialiased">
        <!-- Toast Notification -->
        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-y-[-100%] opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-[-100%] opacity-0"
        >
            <div
                v-if="toastMessage"
                class="fixed top-4 left-1/2 z-[100] flex -translate-x-1/2 items-center gap-3 rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white shadow-2xl ring-1 ring-white/20"
            >
                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ toastMessage }}</span>
            </div>
        </transition>

        <!-- TOP BAR / SOFT BLUE HEADER -->
        <header class="sticky top-0 z-40 shrink-0 bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-800 text-white shadow-md">
            <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                <!-- Brand & Nav Links -->
                <div class="flex items-center gap-8">
                    <a href="/" class="flex items-center gap-3 group">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white p-1.5 shadow-md shadow-blue-900/20 ring-1 ring-white/30 transition group-hover:scale-105">
                            <img src="/images/logo.png" alt="Api Ghar Jagga" class="h-full w-full object-contain" />
                        </div>
                        <div class="flex flex-col">
                            <span class="text-base font-extrabold tracking-tight text-white group-hover:text-blue-100 transition-colors">
                                API Ghar Jagga
                            </span>
                            <span class="text-[10px] tracking-wider uppercase text-blue-200 font-semibold">Real Estate Network</span>
                        </div>
                    </a>

                    <nav class="hidden md:flex items-center gap-6 text-sm font-semibold tracking-wide">
                        <a
                            href="/properties"
                            class="relative py-1 text-white font-bold transition-colors after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-full after:bg-white"
                        >
                            Buy
                        </a>
                        <a
                            href="/property-listing"
                            class="text-blue-100 hover:text-white transition-colors"
                        >
                            Sell
                        </a>
                        <a
                            href="/annex-c"
                            class="text-blue-100 hover:text-white transition-colors"
                        >
                            Valuation
                        </a>
                        <a
                            href="/agreement"
                            class="text-blue-100 hover:text-white transition-colors"
                        >
                            Agreements
                        </a>
                    
                    </nav>
                </div>

                <!-- Right Nav Actions -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <a
                        href="/signin"
                        class="text-xs sm:text-sm font-semibold text-white hover:text-blue-100 transition-colors px-2 py-1"
                    >
                        Sign In
                    </a>
                    <a
                        href="/signup"
                        class="hidden sm:inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-xs sm:text-sm font-bold text-blue-800 shadow-sm transition hover:bg-blue-50 active:scale-95"
                    >
                        Post Listing
                    </a>
                </div>
            </div>
        </header>

        <!-- ========================================================= -->
        <!-- SPLIT-VIEW MAP & LISTINGS BROWSER                         -->
        <!-- Clicking a listing navigates to the dynamic property      -->
        <!-- detail page (Marketplace/PropertyDetail.vue).             -->
        <!-- ========================================================= -->
        <div class="flex flex-1 flex-col overflow-hidden h-[calc(100vh-64px)]">
            <!-- FILTER & SEARCH TOOLBAR -->
            <div class="z-30 shrink-0 border-b border-blue-100 bg-white/95 py-2.5 px-4 sm:px-6 shadow-xs">
                <div class="flex flex-wrap items-center gap-2.5 lg:gap-3">
                    <!-- Location Search Bar -->
                    <div class="relative flex-1 min-w-[220px] max-w-sm">
                        <div class="relative flex items-center">
                            <input
                                v-model="q"
                                @keyup.enter="executeSearch"
                                type="text"
                                placeholder="Location, city, property code..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/70 py-2 pl-3.5 pr-16 text-sm font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/15 transition"
                            />
                            <div class="absolute right-1.5 flex items-center gap-1">
                                <button
                                    v-if="q"
                                    type="button"
                                    @click="clearSearchInput"
                                    class="p-1 text-slate-400 hover:text-slate-600 rounded-full"
                                    title="Clear"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    @click="executeSearch"
                                    class="p-1 text-blue-600 hover:text-blue-800 transition"
                                    title="Search"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- City Selector Dropdown -->
                    <div class="relative min-w-[130px]">
                        <select
                            v-model="city"
                            @change="executeSearch"
                            class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/70 py-2 pl-3 pr-8 text-xs sm:text-sm font-semibold text-slate-700 hover:border-slate-300 focus:bg-white focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/15 transition"
                        >
                            <option value="">All Cities</option>
                            <option v-for="opt in sortedCityOptions" :key="opt.value" :value="opt.value">
                                {{ opt.value }}
                            </option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-2.5 flex items-center text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <!-- Price Filter Button (Rs. PRICE) -->
                    <div class="dropdown-container relative">
                        <button
                            type="button"
                            @click="toggleDropdown('price')"
                            :class="[
                                'flex items-center gap-1.5 rounded-xl border px-3.5 py-2 text-xs sm:text-sm font-bold uppercase tracking-wider transition-colors',
                                minPrice || maxPrice
                                ? 'border-blue-500 bg-blue-50 text-blue-700'
                                : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300'
                            ]"
                        >
                            <span>Rs. PRICE</span>
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <!-- Price Dropdown Menu -->
                        <div
                            v-if="activeDropdown === 'price'"
                            class="absolute left-0 top-full mt-1.5 z-50 w-72 rounded-2xl border border-slate-100 bg-white p-4 shadow-xl"
                        >
                            <div class="font-bold text-xs uppercase tracking-wider text-slate-500 mb-3">Price Range (Rs.)</div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[11px] font-semibold text-slate-500">Min (Rs.)</label>
                                    <input
                                        v-model.number="minPrice"
                                        type="number"
                                        placeholder="Min Rs."
                                        class="w-full rounded-lg border border-slate-200 p-2 text-xs focus:border-blue-600 focus:outline-none"
                                    />
                                </div>
                                <div>
                                    <label class="text-[11px] font-semibold text-slate-500">Max (Rs.)</label>
                                    <input
                                        v-model.number="maxPrice"
                                        type="number"
                                        placeholder="Max Rs."
                                        class="w-full rounded-lg border border-slate-200 p-2 text-xs focus:border-blue-600 focus:outline-none"
                                    />
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between pt-3 border-t border-slate-100">
                                <button
                                    type="button"
                                    @click="minPrice = null; maxPrice = null"
                                    class="text-xs font-semibold text-slate-500 hover:text-slate-800"
                                >
                                    Reset
                                </button>
                                <button
                                    type="button"
                                    @click="closeDropdowns"
                                    class="rounded-lg bg-blue-600 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-blue-700 shadow-sm"
                                >
                                    Apply
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Home Type Filter -->
                    <div class="dropdown-container relative">
                        <button
                            type="button"
                            @click="toggleDropdown('type')"
                            :class="[
                                'flex items-center gap-1.5 rounded-xl border px-3.5 py-2 text-xs sm:text-sm font-bold uppercase tracking-wider transition-colors',
                                selectedPropertyType
                                    ? 'border-blue-500 bg-blue-50 text-blue-700'
                                    : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300'
                            ]"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <span>{{ selectedPropertyType ? selectedPropertyType : 'HOME TYPE' }}</span>
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div
                            v-if="activeDropdown === 'type'"
                            class="absolute left-0 top-full mt-1.5 z-50 w-64 rounded-2xl border border-slate-100 bg-white p-3 shadow-xl"
                        >
                            <div class="font-bold text-xs uppercase tracking-wider text-slate-500 mb-2">Property Type</div>
                            <div class="space-y-1">
                                <button
                                    v-for="pt in propertyTypes"
                                    :key="pt.value"
                                    type="button"
                                    @click="selectedPropertyType = pt.value; closeDropdowns()"
                                    :class="[
                                        'w-full text-left px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        selectedPropertyType === pt.value
                                            ? 'bg-blue-600 text-white'
                                            : 'hover:bg-slate-100 text-slate-700'
                                    ]"
                                >
                                    {{ pt.label }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- All Filters Modal Trigger -->
                    <button
                        type="button"
                        @click="isAllFiltersOpen = true"
                        :class="[
                            'flex items-center gap-2 rounded-xl border px-3.5 py-2 text-xs sm:text-sm font-bold uppercase tracking-wider transition-colors',
                            activeFilterCount > 0
                                ? 'border-blue-500 bg-blue-50 text-blue-700'
                                : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300'
                        ]"
                    >
                        <span
                            v-if="activeFilterCount > 0"
                            class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-[10px] font-extrabold text-white"
                        >
                            {{ activeFilterCount }}
                        </span>
                        <span>ALL FILTERS</span>
                    </button>
                </div>
            </div>

            <!-- MAIN SPLIT VIEW AREA -->
            <main class="relative flex-1 overflow-hidden">
                <div class="flex h-full w-full">
                    <!-- LEFT HALF: COMPLETELY EMPTY MAP SECTION -->
                    <section
                        aria-label="Map View"
                        :class="[
                            'relative flex-1 lg:w-[48%] xl:w-[50%] h-full bg-slate-100/60 border-r border-slate-200 transition-all',
                            activeMobileView === 'list' ? 'hidden lg:block' : 'block w-full'
                        ]"
                    >
                        <div id="marketplace-map-container" class="h-full w-full"></div>
                    </section>

                    <!-- RIGHT HALF: SCROLLABLE LISTINGS FEED -->
                    <section
                        aria-label="Property Listings"
                        :class="[
                            'flex-1 lg:w-[52%] xl:w-[50%] h-full overflow-y-auto bg-slate-50/50 p-4 sm:p-6 lg:p-7 transition-all',
                            activeMobileView === 'map' ? 'hidden lg:block' : 'block w-full'
                        ]"
                    >
                        <!-- Header with Breadcrumbs, Title & Sort -->
                        <div class="mb-5 pb-3 border-b border-slate-200">
                            <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium mb-1">
                                <a href="/" class="hover:text-slate-800">Home</a>
                                <span>/</span>
                                <a href="/properties" class="hover:text-slate-800">Properties</a>
                                <span v-if="city">/</span>
                                <span v-if="city" class="text-blue-600 font-semibold">{{ city }}</span>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-3 mt-1">
                                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                                    {{ activeLocationTitle }}
                                </h1>
                            </div>

                            <div class="mt-3 flex items-center justify-between gap-4">
                                <div class="text-xs sm:text-sm font-semibold text-slate-600">
                                    <span class="font-bold text-slate-900">{{ processedListings.length }}</span> Current {{ processedListings.length === 1 ? 'Home' : 'Homes' }}
                                </div>

                                <!-- Sort Dropdown -->
                                <div class="flex items-center gap-2">
                                    <label for="sort-select" class="text-xs font-bold uppercase tracking-wider text-slate-500 hidden sm:inline">
                                        Sort by:
                                    </label>
                                    <div class="relative inline-block">
                                        <select
                                            id="sort-select"
                                            v-model="sortBy"
                                            class="appearance-none rounded-xl border border-slate-200 bg-white py-1.5 pl-3 pr-8 text-xs font-bold text-slate-800 hover:border-slate-300 focus:border-blue-600 focus:outline-none"
                                        >
                                            <option value="newest">Newest</option>
                                            <option value="price_asc">Price: Low to High</option>
                                            <option value="price_desc">Price: High to Low</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-slate-500">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PROPERTY CARDS 2-COLUMN GRID (CLICK TO OPEN FULL PAGE) -->
                        <div
                            v-if="processedListings.length > 0"
                            class="grid grid-cols-1 sm:grid-cols-2 gap-5"
                        >
                            <article
                                v-for="item in processedListings"
                                :key="item.listing_id"
                                @click="openPropertyDetails(item)"
                                class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-blue-300 cursor-pointer"
                            >
                                <!-- Card Photo with Badges & Favorite Heart -->
                                <div class="relative aspect-[16/10] w-full overflow-hidden bg-slate-100">
                                    <img
                                        :src="item.photo_url || `https://picsum.photos/seed/property-${item.property_code || item.listing_id}/600/400`"
                                        :alt="item.property_code ?? 'Property'"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        loading="lazy"
                                    />

                                    <!-- Top Left Badge (New / Purpose) -->
                                    <div class="absolute top-3 left-3 flex items-center gap-1.5">
                                        <span
                                            class="rounded-full bg-blue-700/95 px-3 py-1 text-[11px] font-bold text-white shadow-sm tracking-wider uppercase"
                                        >
                                            {{ item.purpose ? (item.purpose === 'sale' ? 'For Sale' : item.purpose.toUpperCase()) : 'For Sale' }}
                                        </span>
                                        <span
                                            v-if="item.negotiable"
                                            class="rounded-full bg-emerald-700/90 px-2.5 py-1 text-[10px] font-bold text-white shadow-sm uppercase"
                                        >
                                            Negotiable
                                        </span>
                                    </div>

                                    <!-- Photo Count Badge (Bottom Left) -->
                                    <span
                                        v-if="item.photos && item.photos.length > 1"
                                        class="absolute bottom-3 left-3 flex items-center gap-1 rounded-full bg-black/60 px-2.5 py-0.5 text-[11px] font-bold text-white backdrop-blur shadow"
                                    >
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                            <circle cx="12" cy="13" r="4"></circle>
                                        </svg>
                                        <span>{{ item.photos.length }} Photos</span>
                                    </span>

                                    <!-- Top Right Favorite Heart -->
                                    <button
                                        type="button"
                                        @click.stop="toggleFavorite(item.listing_id)"
                                        :aria-label="favorites.has(item.listing_id) ? 'Remove favorite' : 'Save property'"
                                        class="absolute top-3 right-3 flex h-8 w-8 items-center justify-center rounded-full bg-black/30 text-white backdrop-blur-sm transition-transform active:scale-90 hover:bg-black/50"
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            :class="favorites.has(item.listing_id) ? 'fill-red-500 text-red-500' : 'fill-none text-white'"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                            />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Card Details Body -->
                                <div class="flex flex-1 flex-col p-4">
                                    <!-- Price & Status Line -->
                                    <div class="flex items-baseline justify-between gap-2">
                                        <div class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight group-hover:text-blue-700 transition-colors">
                                            {{ formatPrice(item.price, item.purpose) }}
                                        </div>
                                        <div class="flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span>Active</span>
                                        </div>
                                    </div>

                                    <!-- Key Specs Line -->
                                    <div class="mt-1.5 text-xs font-medium text-slate-700 line-clamp-1">
                                        {{ formatSpecs(item) }}
                                    </div>

                                    <!-- Formatted Address Line -->
                                    <div class="mt-1 text-xs text-slate-500 line-clamp-1">
                                        {{ formatAddress(item) }}
                                    </div>

                                    <!-- Footer MLS & Code -->
                                    <div class="mt-auto pt-3 flex items-center justify-between text-[11px] text-slate-400 border-t border-slate-100">
                                        <span>MLS# {{ item.application_no || item.property_code || item.listing_id }}</span>
                                        <span class="font-bold text-blue-700 text-[10px] tracking-wider uppercase group-hover:underline">VIEW FULL DETAILS →</span>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <!-- Empty State -->
                        <div
                            v-else
                            class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center my-6"
                        >
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600 mb-3">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">No properties found</h3>
                            <p class="mt-1 max-w-sm text-xs text-slate-500">
                                We couldn't find any approved listings matching your filter criteria. Try expanding your search or resetting filters.
                            </p>
                            <button
                                type="button"
                                @click="resetAllFilters"
                                class="mt-4 rounded-xl bg-blue-600 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-blue-700 transition"
                            >
                                Reset All Filters
                            </button>
                        </div>

                        <!-- Footer Info in Scroll -->
                        <div class="mt-8 pt-4 pb-12 border-t border-slate-200 text-center text-xs text-slate-400">
                            <p>© {{ new Date().getFullYear() }} Api Ghar Jagga. Real Estate Network.</p>
                        </div>
                    </section>
                </div>
            </main>

            <!-- MOBILE FLOATING VIEW SWITCHER (List <-> Map) -->
            <div class="lg:hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-40">
                <button
                    type="button"
                    @click="activeMobileView = activeMobileView === 'list' ? 'map' : 'list'"
                    class="flex items-center gap-2 rounded-full bg-slate-900 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-2xl ring-1 ring-white/20 active:scale-95 transition"
                >
                    <span v-if="activeMobileView === 'list'">🗺️ View Map</span>
                    <span v-else>📋 View Listings ({{ processedListings.length }})</span>
                </button>
            </div>
        </div>

        <!-- ========================================= -->
        <!-- ALL FILTERS MODAL DIALOG                  -->
        <!-- ========================================= -->
        <div
            v-if="isAllFiltersOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs"
        >
            <div class="w-full max-w-lg rounded-3xl bg-white shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">All Filters</h2>
                    <button
                        type="button"
                        @click="isAllFiltersOpen = false"
                        class="p-1 rounded-full text-slate-400 hover:text-slate-700"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-5">
                    <!-- City / Municipality -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            City / Metropolitan
                        </label>
                        <select
                            v-model="city"
                            class="w-full rounded-xl border border-slate-200 p-2.5 text-sm font-semibold text-slate-800 focus:border-blue-600 focus:outline-none"
                        >
                            <option value="">All Cities</option>
                            <option v-for="opt in sortedCityOptions" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Purpose -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Listing Purpose
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <button
                                v-for="opt in purposeOptions"
                                :key="opt.value"
                                type="button"
                                @click="purpose = opt.value"
                                :class="[
                                    'py-2 px-3 rounded-xl text-xs font-bold border transition text-center',
                                    purpose === opt.value
                                        ? 'bg-blue-600 text-white border-blue-600'
                                        : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300'
                                ]"
                            >
                                {{ opt.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Property Type -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Property Type
                        </label>
                        <select
                            v-model="selectedPropertyType"
                            class="w-full rounded-xl border border-slate-200 p-2.5 text-sm font-semibold text-slate-800 focus:border-blue-600 focus:outline-none"
                        >
                            <option v-for="pt in propertyTypes" :key="pt.value" :value="pt.value">
                                {{ pt.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Min and Max Price (Rs.) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Price Range (Rs.)
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <input
                                v-model.number="minPrice"
                                type="number"
                                placeholder="Min Price (Rs.)"
                                class="rounded-xl border border-slate-200 p-2.5 text-sm focus:border-blue-600 focus:outline-none"
                            />
                            <input
                                v-model.number="maxPrice"
                                type="number"
                                placeholder="Max Price (Rs.)"
                                class="rounded-xl border border-slate-200 p-2.5 text-sm focus:border-blue-600 focus:outline-none"
                            />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/70">
                    <button
                        type="button"
                        @click="resetAllFilters"
                        class="text-xs font-bold text-slate-600 hover:text-slate-900"
                    >
                        Reset All
                    </button>
                    <button
                        type="button"
                        @click="executeSearch(); isAllFiltersOpen = false"
                        class="rounded-xl bg-blue-600 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-blue-700 transition"
                    >
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
