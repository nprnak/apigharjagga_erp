<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { route } from '@/route';

type KycStatus = 'pending' | 'verified' | 'approved' | 'rejected' | null;
type Tab = 'overview' | 'kyc' | 'listings';

type PropertyPhotoItem = {
    photo_id: number;
    photo_url: string | null;
    caption?: string | null;
};

type PropertyItem = {
    property_id: number;
    property_code: string;
    property_type: string;
    area: string | null;
    municipality: string | null;
    approval_status: 'pending' | 'approved' | 'rejected';
    photos?: PropertyPhotoItem[];
    primary_photo_url?: string | null;
};

const props = withDefaults(
    defineProps<{
        tab?: Tab;
        kycStatus: KycStatus;
        listingCounts: { pending: number; approved: number; rejected: number };
        properties?: PropertyItem[];
    }>(),
    { tab: 'overview', properties: () => [] },
);

const page = usePage();
const authUser = computed(() => (page.props as any).auth?.user);

const activeTab = ref<Tab>(props.tab);

const tabs: { id: Tab; label: string; icon: string }[] = [
    {
        id: 'overview',
        label: 'Overview',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" /><path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.432z" /></svg>`,
    },
    {
        id: 'kyc',
        label: 'KYC Verification',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 00-1.032 0 11.209 11.209 0 01-7.877 3.08.75.75 0 00-.722.515A12.74 12.74 0 002.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 00.374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 00-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08zm3.094 8.016a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" /></svg>`,
    },
    {
        id: 'listings',
        label: 'My Properties',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M19.006 3.705a.75.75 0 00-.512-1.41L6 6.838V3a.75.75 0 00-1.5 0v4.93l-1.006.365a.75.75 0 00.512 1.41l15-5.47zM3.019 9.386a.75.75 0 00-.507 1.408l.75.27A2.25 2.25 0 005.25 13.5v6.75a.75.75 0 001.5 0V13.5a.75.75 0 00-.75-.75H5.25a.75.75 0 01-.712-.51l-.519-.854zM10.5 6.75a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3zm-3 3a.75.75 0 000 1.5h9a.75.75 0 000-1.5h-9zm1.5 3a.75.75 0 000 1.5h6a.75.75 0 000-1.5h-6z" /></svg>`,
    },
];

const listingForm = useForm({
    property_type: '',
    ownership_role: '',
    kitta_no: '',
    area: '',
    covered_area: '',
    no_of_floors: '',
    year_of_construction: '',
    facing_direction: '',
    structure_type: '',
    parking: '',
    province: '',
    district: '',
    municipality: '',
    ward_no: '',
    tole_locality: '',
    purpose_of_listing: 'sale',
    expected_selling_price: '',
    rental_amount: '',
    photos: [] as File[],
});

const photoPreviews = ref<string[]>([]);

const canCreateListing = computed(() => props.kycStatus === 'approved');
const totalListings = computed(
    () =>
        props.listingCounts.pending +
        props.listingCounts.approved +
        props.listingCounts.rejected,
);

const kycLabel = computed(() => {
    if (props.kycStatus === 'approved') {
        return 'Verified';
    }

    if (props.kycStatus === 'verified') {
        return 'Pending Approval';
    }

    if (props.kycStatus === 'pending') {
        return 'Under Review';
    }

    if (props.kycStatus === 'rejected') {
        return 'Rejected';
    }

    return 'Not Submitted';
});

const kycColor = computed(() => {
    if (props.kycStatus === 'approved') {
        return 'text-emerald-400 bg-emerald-400/10 ring-emerald-400/20';
    }

    if (props.kycStatus === 'verified') {
        return 'text-sky-400 bg-sky-400/10 ring-sky-400/20';
    }

    if (props.kycStatus === 'pending') {
        return 'text-amber-400 bg-amber-400/10 ring-amber-400/20';
    }

    if (props.kycStatus === 'rejected') {
        return 'text-red-400 bg-red-400/10 ring-red-400/20';
    }

    return 'text-slate-400 bg-slate-400/10 ring-slate-400/20';
});

const propertyTypes = [
    { value: 'land', label: 'Land' },
    { value: 'house', label: 'House' },
    { value: 'apartment', label: 'Apartment' },
    { value: 'commercial_building', label: 'Commercial Building' },
    { value: 'office_space', label: 'Office Space' },
    { value: 'industrial_property', label: 'Industrial Property' },
    { value: 'agricultural_land', label: 'Agricultural Land' },
    { value: 'other', label: 'Other' },
];

const ownershipRoles = [
    { value: 'self', label: 'Self' },
    { value: 'family_member', label: 'Family Member' },
    { value: 'authorized_representative', label: 'Authorized Representative' },
    { value: 'company', label: 'Company' },
];

const purposes = [
    { value: 'sale', label: 'Sale' },
    { value: 'rent', label: 'Rent' },
    { value: 'lease', label: 'Lease' },
    { value: 'exchange', label: 'Exchange' },
    { value: 'investment', label: 'Investment' },
    { value: 'other', label: 'Other' },
];

function onPropertyPhotosChange(event: Event) {
    const input = event.target as HTMLInputElement;

    if (!input.files || input.files.length === 0) {
        return;
    }

    const newFiles = Array.from(input.files);
    listingForm.photos = [...listingForm.photos, ...newFiles].slice(0, 12);

    photoPreviews.value = [];
    listingForm.photos.forEach((file) => {
        photoPreviews.value.push(URL.createObjectURL(file));
    });
}

function removePropertyPhoto(index: number) {
    listingForm.photos.splice(index, 1);
    photoPreviews.value.splice(index, 1);
}

function listingBadgeClass(status: PropertyItem['approval_status']) {
    if (status === 'approved') {
        return 'text-emerald-400 bg-emerald-400/10 ring-1 ring-emerald-400/20';
    }

    if (status === 'pending') {
        return 'text-amber-400 bg-amber-400/10 ring-1 ring-amber-400/20';
    }

    return 'text-red-400 bg-red-400/10 ring-1 ring-red-400/20';
}

function typeLabel(value: string) {
    return propertyTypes.find((t) => t.value === value)?.label ?? value;
}

function submitListing() {
    if (!canCreateListing.value) {
        return;
    }

    listingForm.post(route('properties.store'), {
        forceFormData: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl leading-tight font-semibold text-white">
                My Dashboard
            </h2>
        </template>

        <div
            class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 py-8"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Welcome banner -->
                <div
                    class="mb-8 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-700 p-6 shadow-xl shadow-blue-900/30"
                >
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="text-sm font-medium text-blue-200">
                                Welcome back,
                            </p>
                            <h1 class="mt-1 text-2xl font-bold text-white">
                                {{ authUser?.name ?? 'User' }}
                            </h1>
                            <p class="mt-1 text-sm text-blue-200">
                                {{ authUser?.email }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                :class="[
                                    'inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-bold ring-1',
                                    kycColor,
                                ]"
                            >
                                <span class="relative flex h-2 w-2">
                                    <span
                                        v-if="kycStatus === 'approved'"
                                        class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"
                                    ></span>
                                    <span
                                        class="relative inline-flex h-2 w-2 rounded-full bg-current"
                                    ></span>
                                </span>
                                KYC: {{ kycLabel }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-6 lg:flex-row">
                    <!-- Sidebar Navigation -->
                    <aside class="w-full shrink-0 lg:w-64">
                        <nav
                            class="flex gap-2 overflow-x-auto rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm lg:flex-col lg:overflow-visible"
                        >
                            <button
                                v-for="tab in tabs"
                                :key="tab.id"
                                type="button"
                                :class="[
                                    'flex items-center gap-3 rounded-xl px-4 py-3 text-left text-sm font-semibold whitespace-nowrap transition-all duration-200',
                                    activeTab === tab.id
                                        ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                                        : 'text-slate-300 hover:bg-white/10 hover:text-white',
                                ]"
                                @click="activeTab = tab.id"
                            >
                                <span v-html="tab.icon" class="shrink-0"></span>
                                {{ tab.label }}
                            </button>
                        </nav>

                        <!-- Quick stats on sidebar -->
                        <div
                            class="mt-4 hidden space-y-3 rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm lg:block"
                        >
                            <p
                                class="text-xs font-bold tracking-wider text-slate-400 uppercase"
                            >
                                Quick Stats
                            </p>
                            <div class="grid grid-cols-3 gap-2">
                                <div
                                    class="rounded-xl bg-amber-400/10 p-3 text-center"
                                >
                                    <div
                                        class="text-xl font-extrabold text-amber-400"
                                    >
                                        {{ listingCounts.pending }}
                                    </div>
                                    <div
                                        class="mt-0.5 text-xs text-amber-400/70"
                                    >
                                        Pending
                                    </div>
                                </div>
                                <div
                                    class="rounded-xl bg-emerald-400/10 p-3 text-center"
                                >
                                    <div
                                        class="text-xl font-extrabold text-emerald-400"
                                    >
                                        {{ listingCounts.approved }}
                                    </div>
                                    <div
                                        class="mt-0.5 text-xs text-emerald-400/70"
                                    >
                                        Approved
                                    </div>
                                </div>
                                <div
                                    class="rounded-xl bg-red-400/10 p-3 text-center"
                                >
                                    <div
                                        class="text-xl font-extrabold text-red-400"
                                    >
                                        {{ listingCounts.rejected }}
                                    </div>
                                    <div class="mt-0.5 text-xs text-red-400/70">
                                        Rejected
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <!-- Main content -->
                    <div class="min-w-0 flex-1 space-y-6">
                        <!-- ── OVERVIEW TAB ── -->
                        <section
                            v-if="activeTab === 'overview'"
                            class="space-y-6"
                        >
                            <!-- Stat cards -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div
                                    class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur-sm transition-all hover:bg-white/8"
                                >
                                    <div
                                        class="mb-4 flex items-center justify-between"
                                    >
                                        <div
                                            class="rounded-xl bg-blue-600/20 p-3"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-6 w-6 text-blue-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                                />
                                            </svg>
                                        </div>
                                        <span
                                            :class="[
                                                'rounded-full px-2.5 py-1 text-xs font-bold ring-1',
                                                kycColor,
                                            ]"
                                            >{{ kycLabel }}</span
                                        >
                                    </div>
                                    <p class="text-sm text-slate-400">
                                        KYC Status
                                    </p>
                                    <p
                                        class="mt-1 text-2xl font-extrabold text-white"
                                    >
                                        Identity
                                    </p>
                                </div>

                                <div
                                    class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur-sm transition-all hover:bg-white/8"
                                >
                                    <div
                                        class="mb-4 flex items-center justify-between"
                                    >
                                        <div
                                            class="rounded-xl bg-indigo-600/20 p-3"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-6 w-6 text-indigo-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-sm text-slate-400">
                                        Total Listings
                                    </p>
                                    <p
                                        class="mt-1 text-3xl font-extrabold text-white"
                                    >
                                        {{ totalListings }}
                                    </p>
                                </div>

                                <div
                                    class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur-sm transition-all hover:bg-white/8"
                                >
                                    <div
                                        class="mb-4 flex items-center justify-between"
                                    >
                                        <div
                                            class="rounded-xl bg-emerald-600/20 p-3"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-6 w-6 text-emerald-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-sm text-slate-400">
                                        Approved Listings
                                    </p>
                                    <p
                                        class="mt-1 text-3xl font-extrabold text-white"
                                    >
                                        {{ listingCounts.approved }}
                                    </p>
                                </div>
                            </div>

                            <!-- Action cards -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div
                                    class="rounded-2xl border border-blue-500/20 bg-gradient-to-br from-blue-600/20 to-indigo-600/20 p-6"
                                >
                                    <h3
                                        class="mb-2 text-lg font-bold text-white"
                                    >
                                        KYC Verification
                                    </h3>
                                    <p class="mb-4 text-sm text-slate-400">
                                        <span v-if="kycStatus === 'approved'"
                                            >Your identity is verified. You can
                                            now list properties.</span
                                        >
                                        <span
                                            v-else-if="kycStatus === 'pending'"
                                            >Your documents are under review.
                                            We'll notify you soon.</span
                                        >
                                        <span
                                            v-else-if="kycStatus === 'rejected'"
                                            >Your submission was rejected.
                                            Please resubmit with correct
                                            documents.</span
                                        >
                                        <span v-else
                                            >Complete identity verification to
                                            unlock property listings.</span
                                        >
                                    </p>
                                    <button
                                        @click="activeTab = 'kyc'"
                                        class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-600/30 transition-all hover:bg-blue-500"
                                    >
                                        {{
                                            kycStatus === 'approved'
                                                ? 'View KYC'
                                                : kycStatus === 'pending'
                                                  ? 'Check Status'
                                                  : 'Start KYC'
                                        }}
                                    </button>
                                </div>

                                <div
                                    class="rounded-2xl border border-emerald-500/20 bg-gradient-to-br from-emerald-600/20 to-teal-600/20 p-6"
                                >
                                    <h3
                                        class="mb-2 text-lg font-bold text-white"
                                    >
                                        List a Property
                                    </h3>
                                    <p class="mb-4 text-sm text-slate-400">
                                        <span v-if="canCreateListing"
                                            >Add your property to our
                                            marketplace and reach thousands of
                                            buyers.</span
                                        >
                                        <span v-else
                                            >Complete KYC verification first to
                                            start listing your properties.</span
                                        >
                                    </p>
                                    <button
                                        @click="activeTab = 'listings'"
                                        :class="[
                                            'rounded-xl px-4 py-2.5 text-sm font-bold transition-all',
                                            canCreateListing
                                                ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30 hover:bg-emerald-500'
                                                : 'cursor-not-allowed bg-slate-700 text-slate-400',
                                        ]"
                                    >
                                        Add Listing
                                    </button>
                                </div>
                            </div>

                            <!-- Recent listings -->
                            <div
                                v-if="properties.length > 0"
                                class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur-sm"
                            >
                                <h3 class="mb-4 text-lg font-bold text-white">
                                    Recent Listings
                                </h3>
                                <div class="space-y-3">
                                    <div
                                        v-for="item in properties.slice(0, 3)"
                                        :key="item.property_id"
                                        class="flex items-center justify-between rounded-xl bg-white/5 px-4 py-3"
                                    >
                                        <div>
                                            <p
                                                class="text-sm font-semibold text-white"
                                            >
                                                {{ item.property_code }}
                                            </p>
                                            <p
                                                class="mt-0.5 text-xs text-slate-400"
                                            >
                                                {{
                                                    typeLabel(
                                                        item.property_type,
                                                    )
                                                }}<span
                                                    v-if="item.municipality"
                                                >
                                                    ·
                                                    {{
                                                        item.municipality
                                                    }}</span
                                                >
                                            </p>
                                        </div>
                                        <span
                                            :class="[
                                                'rounded-full px-2.5 py-1 text-xs font-bold capitalize ring-1',
                                                listingBadgeClass(
                                                    item.approval_status,
                                                ),
                                            ]"
                                            >{{ item.approval_status }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- ── KYC TAB ── -->
                        <section
                            v-else-if="activeTab === 'kyc'"
                            class="overflow-hidden rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm"
                        >
                            <!-- Header -->
                            <div
                                class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 px-6 py-5"
                            >
                                <div>
                                    <h3 class="text-lg font-bold text-white">
                                        KYC Verification
                                    </h3>
                                    <p class="mt-0.5 text-sm text-slate-400">
                                        Annex F — Client Identity Registration
                                    </p>
                                </div>
                                <span
                                    :class="[
                                        'inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold ring-1',
                                        kycColor,
                                    ]"
                                >
                                    {{ kycLabel }}
                                </span>
                            </div>

                            <!-- Status messages -->
                            <div class="px-6 pt-5">
                                <div
                                    v-if="kycStatus === 'pending'"
                                    class="flex items-start gap-3 rounded-xl border border-amber-400/20 bg-amber-400/10 px-4 py-3 text-sm text-amber-300"
                                >
                                    <svg
                                        class="mt-0.5 h-5 w-5 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    Your documents are with our verification
                                    officer for the first review stage.
                                </div>
                                <div
                                    v-else-if="kycStatus === 'verified'"
                                    class="flex items-start gap-3 rounded-xl border border-sky-400/20 bg-sky-400/10 px-4 py-3 text-sm text-sky-300"
                                >
                                    <svg
                                        class="mt-0.5 h-5 w-5 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    Your documents have been verified and are
                                    now with the KYC Approver for final
                                    sign-off.
                                </div>
                                <div
                                    v-else-if="kycStatus === 'approved'"
                                    class="flex items-start gap-3 rounded-xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-300"
                                >
                                    <svg
                                        class="mt-0.5 h-5 w-5 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    Your identity has been verified and
                                    approved. You can now use the full portal.
                                </div>
                                <div
                                    v-else-if="kycStatus === 'rejected'"
                                    class="rounded-xl border border-red-400/20 bg-red-400/10 px-4 py-3 text-sm text-red-300"
                                >
                                    <div class="flex items-start gap-3">
                                        <svg
                                            class="mt-0.5 h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                            />
                                        </svg>
                                        <div>
                                            <p class="font-semibold">
                                                Your submission needs
                                                corrections.
                                            </p>
                                            <p class="mt-1 text-red-300/80">
                                                Open the full Annex F form below
                                                to see the reviewer's note and
                                                resubmit.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="flex items-start gap-3 rounded-xl border border-blue-400/20 bg-blue-400/10 px-4 py-3 text-sm text-blue-300"
                                >
                                    <svg
                                        class="mt-0.5 h-5 w-5 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    You haven't submitted your Annex F KYC form
                                    yet. It's required before you can use the
                                    rest of the portal.
                                </div>
                            </div>

                            <!-- The full bilingual Annex-F wizard (personal details, address, document
                                 checklist with uploads, and declaration/signature) lives on one dedicated
                                 Filament page rather than being duplicated here. -->
                            <div class="px-6 py-8 text-center">
                                <a
                                    href="/dashboard/kyc-verification-page"
                                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/30 transition-all hover:bg-blue-500"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5l7 7-7 7"
                                        />
                                    </svg>
                                    <span v-if="kycStatus === 'rejected'"
                                        >Open Form to Resubmit</span
                                    >
                                    <span v-else-if="!kycStatus"
                                        >Start Annex F KYC Form</span
                                    >
                                    <span v-else>Open KYC Form</span>
                                </a>
                            </div>
                        </section>

                        <!-- ── LISTINGS TAB ── -->
                        <section v-else class="space-y-6">
                            <!-- Properties list -->
                            <div
                                class="overflow-hidden rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm"
                            >
                                <div class="border-b border-white/10 px-6 py-5">
                                    <h3 class="text-lg font-bold text-white">
                                        My Listings
                                    </h3>
                                    <p class="mt-0.5 text-sm text-slate-400">
                                        {{ totalListings }} total listing{{
                                            totalListings === 1 ? '' : 's'
                                        }}
                                    </p>
                                </div>
                                <div
                                    v-if="properties.length === 0"
                                    class="px-6 py-12 text-center"
                                >
                                    <svg
                                        class="mx-auto mb-3 h-12 w-12 text-slate-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                        />
                                    </svg>
                                    <p class="text-sm text-slate-400">
                                        No properties listed yet.
                                    </p>
                                </div>
                                <ul v-else class="divide-y divide-white/5">
                                    <li
                                        v-for="item in properties"
                                        :key="item.property_id"
                                        class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 transition-all hover:bg-white/5"
                                    >
                                        <div class="flex items-center gap-4">
                                            <div
                                                v-if="item.primary_photo_url"
                                                class="h-12 w-12 shrink-0 overflow-hidden rounded-xl border border-white/10 shadow-sm"
                                            >
                                                <img
                                                    :src="
                                                        item.primary_photo_url
                                                    "
                                                    :alt="item.property_code"
                                                    class="h-full w-full object-cover"
                                                />
                                            </div>
                                            <div
                                                v-else
                                                class="shrink-0 rounded-xl bg-indigo-600/20 p-2.5"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5 text-indigo-400"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                                    />
                                                </svg>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm font-semibold text-white"
                                                >
                                                    {{ item.property_code }}
                                                </p>
                                                <p
                                                    class="mt-0.5 text-xs text-slate-400"
                                                >
                                                    {{
                                                        typeLabel(
                                                            item.property_type,
                                                        )
                                                    }}
                                                    <span
                                                        v-if="item.municipality"
                                                    >
                                                        ·
                                                        {{
                                                            item.municipality
                                                        }}</span
                                                    >
                                                    <span v-if="item.area">
                                                        · {{ item.area }}</span
                                                    >
                                                    <span
                                                        v-if="
                                                            item.photos &&
                                                            item.photos.length >
                                                                0
                                                        "
                                                        class="font-medium text-blue-400"
                                                    >
                                                        ·
                                                        {{
                                                            item.photos.length
                                                        }}
                                                        photo{{
                                                            item.photos.length >
                                                            1
                                                                ? 's'
                                                                : ''
                                                        }}</span
                                                    >
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            :class="[
                                                'rounded-full px-3 py-1.5 text-xs font-bold capitalize ring-1',
                                                listingBadgeClass(
                                                    item.approval_status,
                                                ),
                                            ]"
                                        >
                                            {{ item.approval_status }}
                                        </span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Add listing form -->
                            <div
                                class="overflow-hidden rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm"
                            >
                                <div class="border-b border-white/10 px-6 py-5">
                                    <h3 class="text-lg font-bold text-white">
                                        Add New Listing
                                    </h3>
                                    <p class="mt-0.5 text-sm text-slate-400">
                                        Fill in your property details below
                                    </p>
                                </div>

                                <div
                                    v-if="!canCreateListing"
                                    class="px-6 py-8 text-center"
                                >
                                    <div
                                        class="inline-block rounded-2xl border border-amber-400/20 bg-amber-400/10 p-6"
                                    >
                                        <svg
                                            class="mx-auto mb-3 h-10 w-10 text-amber-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                            />
                                        </svg>
                                        <p
                                            class="mb-3 font-semibold text-amber-300"
                                        >
                                            KYC verification required
                                        </p>
                                        <p class="mb-4 text-sm text-slate-400">
                                            Complete identity verification
                                            before listing a property.
                                        </p>
                                        <button
                                            @click="activeTab = 'kyc'"
                                            class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-600/30 transition-all hover:bg-blue-500"
                                        >
                                            Start KYC Verification
                                        </button>
                                    </div>
                                </div>

                                <form
                                    v-else
                                    @submit.prevent="submitListing"
                                    class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2"
                                >
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >Property Type
                                            <span class="text-red-400"
                                                >*</span
                                            ></label
                                        >
                                        <select
                                            v-model="listingForm.property_type"
                                            required
                                            class="w-full rounded-xl border border-white/10 bg-slate-800 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        >
                                            <option value="" disabled>
                                                Select property category
                                            </option>
                                            <option
                                                v-for="t in propertyTypes"
                                                :key="t.value"
                                                :value="t.value"
                                            >
                                                {{ t.label }}
                                            </option>
                                        </select>
                                        <InputError
                                            class="mt-1.5"
                                            :message="
                                                listingForm.errors.property_type
                                            "
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >Listing Purpose</label
                                        >
                                        <select
                                            v-model="
                                                listingForm.purpose_of_listing
                                            "
                                            class="w-full rounded-xl border border-white/10 bg-slate-800 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        >
                                            <option
                                                v-for="p in purposes"
                                                :key="p.value"
                                                :value="p.value"
                                            >
                                                {{ p.label }}
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >Ownership Role</label
                                        >
                                        <select
                                            v-model="listingForm.ownership_role"
                                            class="w-full rounded-xl border border-white/10 bg-slate-800 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        >
                                            <option value="">
                                                Select ownership
                                            </option>
                                            <option
                                                v-for="o in ownershipRoles"
                                                :key="o.value"
                                                :value="o.value"
                                            >
                                                {{ o.label }}
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >Kitta No.</label
                                        >
                                        <input
                                            v-model="listingForm.kitta_no"
                                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >Area</label
                                        >
                                        <input
                                            v-model="listingForm.area"
                                            placeholder="e.g. 4 aana"
                                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-500 transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >Covered Area</label
                                        >
                                        <input
                                            v-model="listingForm.covered_area"
                                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >No. of Floors</label
                                        >
                                        <input
                                            v-model="listingForm.no_of_floors"
                                            type="number"
                                            min="0"
                                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >Year of Construction</label
                                        >
                                        <input
                                            v-model="
                                                listingForm.year_of_construction
                                            "
                                            type="number"
                                            min="1800"
                                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >Facing Direction</label
                                        >
                                        <input
                                            v-model="
                                                listingForm.facing_direction
                                            "
                                            placeholder="e.g. East"
                                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >Structure Type</label
                                        >
                                        <input
                                            v-model="listingForm.structure_type"
                                            placeholder="e.g. RCC Frame"
                                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <h4
                                            class="my-2 flex items-center gap-2 text-xs font-bold tracking-widest text-blue-400 uppercase"
                                        >
                                            <span
                                                class="h-px flex-1 bg-blue-400/20"
                                            ></span>
                                            Property Location
                                            <span
                                                class="h-px flex-1 bg-blue-400/20"
                                            ></span>
                                        </h4>
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >Province</label
                                        >
                                        <input
                                            v-model="listingForm.province"
                                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >District</label
                                        >
                                        <input
                                            v-model="listingForm.district"
                                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >Municipality</label
                                        >
                                        <input
                                            v-model="listingForm.municipality"
                                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >Ward No.</label
                                        >
                                        <input
                                            v-model="listingForm.ward_no"
                                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >Tole / Locality</label
                                        >
                                        <input
                                            v-model="listingForm.tole_locality"
                                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >Expected Price (Rs.)</label
                                        >
                                        <input
                                            v-model="
                                                listingForm.expected_selling_price
                                            "
                                            type="number"
                                            min="0"
                                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-semibold text-slate-300"
                                            >Rent Amount (Rs.)</label
                                        >
                                        <input
                                            v-model="listingForm.rental_amount"
                                            type="number"
                                            min="0"
                                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        />
                                    </div>

                                    <!-- Property Photographs Section -->
                                    <div class="sm:col-span-2">
                                        <h4
                                            class="my-2 flex items-center gap-2 text-xs font-bold tracking-widest text-blue-400 uppercase"
                                        >
                                            <span
                                                class="h-px flex-1 bg-blue-400/20"
                                            ></span>
                                            Property Photographs (तस्विरहरू)
                                            <span
                                                class="h-px flex-1 bg-blue-400/20"
                                            ></span>
                                        </h4>

                                        <label
                                            class="flex min-h-[120px] w-full cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-white/20 bg-white/5 p-4 transition-all hover:bg-white/10"
                                        >
                                            <div
                                                class="flex flex-col items-center justify-center text-center"
                                            >
                                                <svg
                                                    class="mb-2 h-8 w-8 text-slate-400"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                    />
                                                </svg>
                                                <p
                                                    class="text-xs font-medium text-slate-300"
                                                >
                                                    Click to upload multiple
                                                    property photos
                                                </p>
                                                <p
                                                    class="mt-1 text-[11px] text-slate-500"
                                                >
                                                    JPEG, PNG, WebP (Max 20MB
                                                    per image, up to 12
                                                    pictures)
                                                </p>
                                            </div>
                                            <input
                                                type="file"
                                                multiple
                                                accept="image/jpeg,image/jpg,image/png,image/webp"
                                                class="hidden"
                                                @change="onPropertyPhotosChange"
                                            />
                                        </label>

                                        <!-- Thumbnail Grid Preview -->
                                        <div
                                            v-if="photoPreviews.length > 0"
                                            class="mt-4"
                                        >
                                            <p
                                                class="mb-2 text-xs font-medium text-slate-400"
                                            >
                                                {{
                                                    photoPreviews.length
                                                }}
                                                photo{{
                                                    photoPreviews.length > 1
                                                        ? 's'
                                                        : ''
                                                }}
                                                selected (First photo will be
                                                cover):
                                            </p>
                                            <div
                                                class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6"
                                            >
                                                <div
                                                    v-for="(
                                                        preview, index
                                                    ) in photoPreviews"
                                                    :key="index"
                                                    class="group relative aspect-square overflow-hidden rounded-xl border border-white/10 bg-black/40"
                                                >
                                                    <img
                                                        :src="preview"
                                                        alt="Property thumbnail"
                                                        class="h-full w-full object-cover"
                                                    />
                                                    <button
                                                        type="button"
                                                        @click="
                                                            removePropertyPhoto(
                                                                index,
                                                            )
                                                        "
                                                        class="absolute top-1 right-1 flex h-6 w-6 items-center justify-center rounded-full bg-rose-600/90 text-white opacity-90 shadow transition-opacity group-hover:opacity-100"
                                                        title="Remove photo"
                                                    >
                                                        &times;
                                                    </button>
                                                    <span
                                                        v-if="index === 0"
                                                        class="absolute bottom-1 left-1 rounded bg-black/70 px-1.5 py-0.5 text-[9px] font-bold text-amber-300"
                                                    >
                                                        Cover
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <InputError
                                            class="mt-1.5"
                                            :message="listingForm.errors.photos"
                                        />
                                    </div>

                                    <div
                                        class="flex items-center gap-4 pt-2 sm:col-span-2"
                                    >
                                        <button
                                            type="submit"
                                            :disabled="listingForm.processing"
                                            class="rounded-xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-600/30 transition-all hover:bg-emerald-500 disabled:opacity-50"
                                        >
                                            <span v-if="listingForm.processing"
                                                >Submitting…</span
                                            >
                                            <span v-else>Submit Listing</span>
                                        </button>
                                        <InputError
                                            :message="listingForm.errors.kyc"
                                        />
                                    </div>
                                </form>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
