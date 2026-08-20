<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref } from 'vue';
import type { ListingDetail } from '../../types/marketplace';

const props = defineProps<{
    listing: ListingDetail;
}>();

const activePhotoIndex = ref<number>(0);
const isFavorited = ref(false);

// Inquiry form
const inquiryName = ref('');
const inquiryPhone = ref('');
const inquiryEmail = ref('');
const inquiryMessage = ref('');
const isInquirySent = ref(false);
const isSubmittingInquiry = ref(false);
const inquiryError = ref<string | null>(null);

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

function toggleFavorite() {
    isFavorited.value = !isFavorited.value;
    showToast(isFavorited.value ? 'Saved to your favorite properties!' : 'Removed from saved properties');
}

function shareProperty() {
    const url = window.location.origin + '/properties/' + props.listing.listing_id;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            showToast('Property link copied to clipboard!');
        });
    } else {
        showToast('Link: ' + url);
    }
}

const photos = computed<string[]>(() => {
    if (props.listing.photos && props.listing.photos.length > 0) {
        return props.listing.photos;
    }
    if (props.listing.photo_url) {
        return [props.listing.photo_url];
    }
    return [`https://picsum.photos/seed/property-${props.listing.property_code || props.listing.listing_id}/1200/800`];
});

function prevPhoto() {
    if (photos.value.length <= 1) return;
    activePhotoIndex.value = (activePhotoIndex.value - 1 + photos.value.length) % photos.value.length;
}

function nextPhoto() {
    if (photos.value.length <= 1) return;
    activePhotoIndex.value = (activePhotoIndex.value + 1) % photos.value.length;
}

async function submitInquiry() {
    inquiryError.value = null;

    if (!inquiryName.value || !inquiryPhone.value) {
        inquiryError.value = 'Please provide your name and phone number';
        return;
    }

    isSubmittingInquiry.value = true;

    try {
        const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null)?.content;
        await axios.post(
            '/inquiries',
            {
                property_id: props.listing.property_id,
                listing_id: props.listing.listing_id,
                name: inquiryName.value,
                phone: inquiryPhone.value,
                email: inquiryEmail.value || null,
                message: inquiryMessage.value || null,
            },
            {
                headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
            },
        );

        isInquirySent.value = true;
        showToast('Thank you! Our agent will contact you shortly.');
        inquiryName.value = '';
        inquiryPhone.value = '';
        inquiryEmail.value = '';
        inquiryMessage.value = '';
    } catch (err: any) {
        if (err.response?.status === 422) {
            const firstError = Object.values(err.response.data.errors ?? {})[0];
            inquiryError.value = Array.isArray(firstError) ? String(firstError[0]) : 'Please check the form and try again.';
        } else {
            inquiryError.value = 'Something went wrong. Please try again in a moment.';
        }
    } finally {
        isSubmittingInquiry.value = false;
    }
}

function formatPrice(value: ListingDetail['price'], purposeStr?: string | null): string {
    if (value === null || value === undefined || value === '') return 'Contact for price';
    const n = Number(value);
    if (Number.isNaN(n)) return String(value);
    const formatted = 'Rs. ' + new Intl.NumberFormat('en-IN').format(n);
    if (purposeStr === 'rent') return formatted + ' / month';
    return formatted;
}

function formatTypeLabel(t: string | null | undefined): string {
    if (!t) return 'Residential Property';
    return t.split('_').map((w) => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
}

function formatAddress(item: ListingDetail): string {
    const parts = [item.municipality, item.district, item.province].filter(Boolean);
    return parts.length ? parts.join(', ') : 'Nepal';
}

function formatLegalStatus(status?: string | null): string {
    if (status === 'completed') return 'Legally Verified';
    if (status === 'pending') return 'Verification In Progress';
    return 'Not Yet Verified';
}

function isAmenityAvailable(value?: string | null): boolean {
    if (!value) return false;
    const v = value.trim().toLowerCase();
    return v !== '' && !['no', 'none', 'n/a', 'na', 'not available', 'unavailable', 'nil'].includes(v);
}

function amenityDescription(value?: string | null, fallback = 'Not specified'): string {
    if (!isAmenityAvailable(value)) return fallback;
    return (value as string).charAt(0).toUpperCase() + (value as string).slice(1);
}

const amenities = computed(() => [
    { label: 'Road Access', value: props.listing.road_access },
    { label: 'Water Supply', value: props.listing.water_supply },
    { label: 'Electricity', value: props.listing.electricity },
    { label: 'Drainage System', value: props.listing.drainage },
    { label: 'Parking', value: props.listing.parking },
    { label: 'Internet', value: props.listing.internet },
]);

const pageTitle = computed(
    () => `${props.listing.property_code ?? 'Property'} · ${formatTypeLabel(props.listing.property_type)} in ${props.listing.municipality ?? 'Nepal'} | Api Ghar Jagga`,
);

function handleKeydown(e: KeyboardEvent) {
    if (e.key === 'ArrowLeft') prevPhoto();
    if (e.key === 'ArrowRight') nextPhoto();
}
</script>

<template>
    <Head :title="pageTitle" />

    <div class="min-h-screen flex flex-col bg-white text-slate-900 font-sans antialiased" @keydown="handleKeydown">
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
                        <a href="/properties" class="text-blue-100 hover:text-white transition-colors">Buy</a>
                        <a href="/property-listing" class="text-blue-100 hover:text-white transition-colors">Sell</a>
                        <a href="/annex-c" class="text-blue-100 hover:text-white transition-colors">Valuation</a>
                        <a href="/agreement" class="text-blue-100 hover:text-white transition-colors">Agreements</a>
                    </nav>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">
                    <a href="/signin" class="text-xs sm:text-sm font-semibold text-white hover:text-blue-100 transition-colors px-2 py-1">
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
        <!-- FULL-PAGE DEDICATED PROPERTY DETAILS VIEW                 -->
        <!-- ========================================================= -->
        <div class="flex-1 bg-slate-50/60 pb-16">
            <!-- Sticky Sub-Nav / Breadcrumb & Back Bar -->
            <div class="sticky top-16 z-30 border-b border-slate-200 bg-white/95 backdrop-blur px-4 sm:px-8 py-3 shadow-xs">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                    <Link
                        href="/properties"
                        class="inline-flex items-center gap-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold px-4 py-2 text-xs sm:text-sm transition active:scale-95"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Back to all properties</span>
                    </Link>

                    <div class="hidden md:flex items-center gap-2 text-xs text-slate-500">
                        <Link href="/properties" class="hover:text-slate-800">Properties</Link>
                        <span>/</span>
                        <span class="text-slate-700 font-semibold">{{ listing.municipality || 'Nepal' }}</span>
                        <span>/</span>
                        <span class="text-blue-600 font-bold">{{ listing.property_code }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="shareProperty"
                            class="flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
                        >
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                                <polyline points="16 6 12 2 8 6"></polyline>
                                <line x1="12" y1="2" x2="12" y2="15"></line>
                            </svg>
                            <span>Share</span>
                        </button>
                        <button
                            type="button"
                            @click="toggleFavorite"
                            class="flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
                        >
                            <svg
                                class="h-4 w-4"
                                :class="isFavorited ? 'fill-red-500 text-red-500' : 'fill-none text-slate-500'"
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
                            <span>{{ isFavorited ? 'Saved' : 'Save' }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Page Main Content Area -->
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-6">
                <!-- 1. MULTI-PHOTO INTERACTIVE SHOWCASE GALLERY -->
                <section class="mb-8">
                    <div class="overflow-hidden rounded-3xl bg-slate-900 shadow-xl border border-slate-200/50">
                        <div class="relative aspect-[16/9] sm:aspect-[21/9] w-full select-none overflow-hidden bg-slate-950">
                            <img
                                :src="photos[activePhotoIndex]"
                                :alt="`${listing.property_code} Photo ${activePhotoIndex + 1}`"
                                class="h-full w-full object-cover transition-all duration-300"
                            />

                            <div class="absolute top-4 left-4 flex items-center gap-2">
                                <span class="rounded-full bg-blue-600 px-3.5 py-1 text-xs font-bold text-white uppercase tracking-wider shadow-lg">
                                    {{ listing.purpose ? (listing.purpose === 'sale' ? 'For Sale' : listing.purpose.toUpperCase()) : 'For Sale' }}
                                </span>
                                <span v-if="listing.negotiable" class="rounded-full bg-emerald-600 px-3 py-1 text-xs font-bold text-white uppercase tracking-wider shadow-lg">
                                    Negotiable
                                </span>
                                <span class="rounded-full bg-black/60 px-3 py-1 text-xs font-bold text-white shadow-lg backdrop-blur">
                                    Photo {{ activePhotoIndex + 1 }} of {{ photos.length }}
                                </span>
                            </div>

                            <button
                                v-if="photos.length > 1"
                                type="button"
                                @click="prevPhoto"
                                class="absolute left-4 top-1/2 -translate-y-1/2 flex h-11 w-11 items-center justify-center rounded-full bg-black/50 hover:bg-black/80 text-white backdrop-blur shadow-lg transition active:scale-95"
                                title="Previous Photo"
                            >
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button
                                v-if="photos.length > 1"
                                type="button"
                                @click="nextPhoto"
                                class="absolute right-4 top-1/2 -translate-y-1/2 flex h-11 w-11 items-center justify-center rounded-full bg-black/50 hover:bg-black/80 text-white backdrop-blur shadow-lg transition active:scale-95"
                                title="Next Photo"
                            >
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>

                        <div
                            v-if="photos.length > 1"
                            class="flex gap-3 overflow-x-auto bg-slate-900/95 p-4 border-t border-white/10"
                        >
                            <button
                                v-for="(img, idx) in photos"
                                :key="idx"
                                type="button"
                                @click="activePhotoIndex = idx"
                                :class="[
                                    'relative h-18 w-28 shrink-0 overflow-hidden rounded-xl border-2 transition-all',
                                    activePhotoIndex === idx
                                        ? 'border-blue-500 ring-2 ring-blue-500/40 opacity-100 scale-102'
                                        : 'border-transparent opacity-60 hover:opacity-90'
                                ]"
                            >
                                <img :src="img" :alt="`Thumbnail ${idx + 1}`" class="h-full w-full object-cover" />
                                <span class="absolute bottom-1 right-1 rounded bg-black/70 px-1.5 py-0.5 text-[9px] font-bold text-white">
                                    #{{ idx + 1 }}
                                </span>
                            </button>
                        </div>
                    </div>
                </section>

                <!-- 2. MAIN PROPERTY DETAILS & INQUIRY SIDEBAR -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left 2 Cols: Main Specs & Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Heading & Price Card -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                            <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-4 border-b border-slate-100 pb-6">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Active Listing
                                        </span>
                                        <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                                            MLS# {{ listing.application_no }}
                                        </span>
                                    </div>
                                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-3">
                                        {{ formatTypeLabel(listing.property_type) }} in {{ listing.municipality }}
                                    </h1>
                                    <p class="text-slate-500 text-sm mt-1 flex items-center gap-1.5">
                                        <svg class="h-4 w-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>{{ formatAddress(listing) }}</span>
                                    </p>
                                </div>

                                <div class="sm:text-right shrink-0">
                                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Listed Price</div>
                                    <div class="text-2xl sm:text-3xl font-black text-blue-800 tracking-tight mt-0.5">
                                        {{ formatPrice(listing.price, listing.purpose) }}
                                    </div>
                                    <span v-if="listing.negotiable" class="text-xs text-emerald-600 font-bold">
                                        * Price is negotiable
                                    </span>
                                </div>
                            </div>

                            <!-- 4 Key Specs Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6">
                                <div class="rounded-2xl bg-slate-50/80 border border-slate-100 p-4 text-center">
                                    <div class="text-xs font-semibold text-slate-500">Property Type</div>
                                    <div class="text-base font-bold text-slate-900 mt-1">{{ formatTypeLabel(listing.property_type) }}</div>
                                </div>
                                <div class="rounded-2xl bg-slate-50/80 border border-slate-100 p-4 text-center">
                                    <div class="text-xs font-semibold text-slate-500">Total Area</div>
                                    <div class="text-base font-bold text-slate-900 mt-1">{{ listing.area || 'N/A' }}</div>
                                </div>
                                <div class="rounded-2xl bg-slate-50/80 border border-slate-100 p-4 text-center">
                                    <div class="text-xs font-semibold text-slate-500">Covered Area</div>
                                    <div class="text-base font-bold text-slate-900 mt-1">{{ listing.covered_area || listing.area || 'N/A' }}</div>
                                </div>
                                <div class="rounded-2xl bg-slate-50/80 border border-slate-100 p-4 text-center">
                                    <div class="text-xs font-semibold text-slate-500">Floors</div>
                                    <div class="text-base font-bold text-slate-900 mt-1">{{ listing.no_of_floors ? `${listing.no_of_floors} Floors` : 'Land Plot' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed Technical & Location Specifications -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                            <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Property Details & Verification</span>
                            </h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500">Property Code:</span>
                                        <span class="font-bold text-slate-800">{{ listing.property_code || 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500">Application Number:</span>
                                        <span class="font-bold text-slate-800">{{ listing.application_no }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500">Municipality:</span>
                                        <span class="font-bold text-slate-800">{{ listing.municipality || 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500">Ward / Locality:</span>
                                        <span class="font-bold text-slate-800">{{ [listing.ward_no ? `Ward ${listing.ward_no}` : null, listing.tole_locality].filter(Boolean).join(', ') || 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2">
                                        <span class="text-slate-500">District:</span>
                                        <span class="font-bold text-slate-800">{{ listing.district || 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500">Province:</span>
                                        <span class="font-bold text-slate-800">{{ listing.province || 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500">Country:</span>
                                        <span class="font-bold text-slate-800">Nepal</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500">Pricing Status:</span>
                                        <span class="font-bold text-emerald-600">{{ listing.negotiable ? 'Negotiable' : 'Fixed' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-slate-100">
                                        <span class="text-slate-500">Ownership Type:</span>
                                        <span class="font-bold text-slate-800">{{ listing.ownership_type ? formatTypeLabel(listing.ownership_type) : 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2">
                                        <span class="text-slate-500">Legal Verification:</span>
                                        <span
                                            class="font-bold"
                                            :class="listing.legal_verification_status === 'completed' ? 'text-emerald-700' : 'text-amber-600'"
                                        >
                                            {{ formatLegalStatus(listing.legal_verification_status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional structural specs, only when property has them -->
                            <div
                                v-if="listing.structure_type || listing.roof_type || listing.facing_direction || listing.year_of_construction || listing.road_width || listing.kitta_no"
                                class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-4 border-t border-slate-100 pt-6"
                            >
                                <div v-if="listing.kitta_no" class="rounded-xl bg-slate-50/80 p-3 text-center">
                                    <div class="text-[11px] font-semibold text-slate-500">Kitta No.</div>
                                    <div class="text-sm font-bold text-slate-900 mt-0.5">{{ listing.kitta_no }}</div>
                                </div>
                                <div v-if="listing.structure_type" class="rounded-xl bg-slate-50/80 p-3 text-center">
                                    <div class="text-[11px] font-semibold text-slate-500">Structure</div>
                                    <div class="text-sm font-bold text-slate-900 mt-0.5">{{ formatTypeLabel(listing.structure_type) }}</div>
                                </div>
                                <div v-if="listing.roof_type" class="rounded-xl bg-slate-50/80 p-3 text-center">
                                    <div class="text-[11px] font-semibold text-slate-500">Roof Type</div>
                                    <div class="text-sm font-bold text-slate-900 mt-0.5">{{ formatTypeLabel(listing.roof_type) }}</div>
                                </div>
                                <div v-if="listing.facing_direction" class="rounded-xl bg-slate-50/80 p-3 text-center">
                                    <div class="text-[11px] font-semibold text-slate-500">Facing</div>
                                    <div class="text-sm font-bold text-slate-900 mt-0.5">{{ formatTypeLabel(listing.facing_direction) }}</div>
                                </div>
                                <div v-if="listing.year_of_construction" class="rounded-xl bg-slate-50/80 p-3 text-center">
                                    <div class="text-[11px] font-semibold text-slate-500">Year Built</div>
                                    <div class="text-sm font-bold text-slate-900 mt-0.5">{{ listing.year_of_construction }}</div>
                                </div>
                                <div v-if="listing.road_width" class="rounded-xl bg-slate-50/80 p-3 text-center">
                                    <div class="text-[11px] font-semibold text-slate-500">Road Width</div>
                                    <div class="text-sm font-bold text-slate-900 mt-0.5">{{ listing.road_width }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Features & Highlights (dynamic, from real property data) -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                            <h2 class="text-lg font-bold text-slate-900 mb-4">Features & Amenities</h2>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <div
                                    v-for="amenity in amenities"
                                    :key="amenity.label"
                                    class="flex items-start gap-2 rounded-xl bg-slate-50 p-3 text-xs font-semibold text-slate-700 border border-slate-100"
                                >
                                    <span :class="isAmenityAvailable(amenity.value) ? 'text-emerald-600' : 'text-slate-300'">
                                        {{ isAmenityAvailable(amenity.value) ? '✓' : '×' }}
                                    </span>
                                    <span>
                                        {{ amenity.label }}
                                        <span class="block text-[10px] font-normal text-slate-400">{{ amenityDescription(amenity.value) }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Listing Remarks (only when the office added notes) -->
                        <div v-if="listing.remarks" class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                            <h2 class="text-lg font-bold text-slate-900 mb-3">Additional Notes</h2>
                            <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ listing.remarks }}</p>
                        </div>
                    </div>

                    <!-- Right Col: Sticky Inquire & Quick Actions Box -->
                    <div class="space-y-6">
                        <div class="sticky top-32 rounded-3xl border border-blue-100 bg-white p-6 shadow-lg shadow-blue-900/5">
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-600 text-white font-bold text-lg shadow-md shadow-blue-600/20">
                                    AGJ
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-base">API Ghar Jagga</div>
                                    <div class="text-xs text-blue-600 font-semibold">Licensed Real Estate Network</div>
                                </div>
                            </div>

                            <div class="pt-4 space-y-3">
                                <a
                                    href="tel:9800000000"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 font-bold text-sm shadow-md shadow-blue-600/20 transition active:scale-95"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span>Call Us</span>
                                </a>

                                <a
                                    href="/agreement"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 py-3 px-4 font-bold text-sm transition"
                                >
                                    <span>Draft Purchase Agreement</span>
                                </a>

                                <a
                                    href="/annex-c"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 py-3 px-4 font-bold text-sm transition"
                                >
                                    <span>Request Official Valuation</span>
                                </a>
                            </div>

                            <!-- Send Inquiry Form (submits to the backend) -->
                            <div class="mt-6 pt-5 border-t border-slate-100">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Send Direct Inquiry</h3>

                                <div v-if="isInquirySent" class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs text-emerald-800 font-semibold mb-3">
                                    Inquiry sent! Our representative will contact you shortly.
                                </div>

                                <div v-if="inquiryError" class="rounded-xl bg-rose-50 border border-rose-200 p-3 text-xs text-rose-700 font-semibold mb-3">
                                    {{ inquiryError }}
                                </div>

                                <form @submit.prevent="submitInquiry" class="space-y-3">
                                    <div>
                                        <input
                                            v-model="inquiryName"
                                            type="text"
                                            required
                                            placeholder="Your Full Name"
                                            class="w-full rounded-xl border border-slate-200 p-2.5 text-xs text-slate-900 placeholder-slate-400 focus:border-blue-600 focus:outline-none"
                                        />
                                    </div>
                                    <div>
                                        <input
                                            v-model="inquiryPhone"
                                            type="tel"
                                            required
                                            placeholder="Phone Number (e.g. 98XXXXXXXX)"
                                            class="w-full rounded-xl border border-slate-200 p-2.5 text-xs text-slate-900 placeholder-slate-400 focus:border-blue-600 focus:outline-none"
                                        />
                                    </div>
                                    <div>
                                        <input
                                            v-model="inquiryEmail"
                                            type="email"
                                            placeholder="Email (optional)"
                                            class="w-full rounded-xl border border-slate-200 p-2.5 text-xs text-slate-900 placeholder-slate-400 focus:border-blue-600 focus:outline-none"
                                        />
                                    </div>
                                    <div>
                                        <textarea
                                            v-model="inquiryMessage"
                                            rows="3"
                                            placeholder="I am interested in this property..."
                                            class="w-full rounded-xl border border-slate-200 p-2.5 text-xs text-slate-900 placeholder-slate-400 focus:border-blue-600 focus:outline-none resize-none"
                                        ></textarea>
                                    </div>
                                    <button
                                        type="submit"
                                        :disabled="isSubmittingInquiry"
                                        class="w-full rounded-xl bg-slate-900 hover:bg-slate-800 disabled:opacity-60 text-white py-2.5 text-xs font-bold shadow transition"
                                    >
                                        {{ isSubmittingInquiry ? 'Sending…' : 'Send Message' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
