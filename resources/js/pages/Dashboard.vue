<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from '@/route';

type KycStatus = 'pending' | 'approved' | 'rejected' | null;
type Tab = 'overview' | 'kyc' | 'listings';

type KycRecord = {
    status: Exclude<KycStatus, null>;
    id_type: string;
    full_name: string | null;
    citizenship_no: string | null;
    mobile_no: string | null;
    admin_note: string | null;
    submitted_at: string | null;
};

type PropertyItem = {
    property_id: number;
    property_code: string;
    property_type: string;
    area: string | null;
    municipality: string | null;
    approval_status: 'pending' | 'approved' | 'rejected';
};

const props = withDefaults(
    defineProps<{
        tab?: Tab;
        kycStatus: KycStatus;
        kyc?: KycRecord | null;
        listingCounts: { pending: number; approved: number; rejected: number };
        properties?: PropertyItem[];
    }>(),
    { tab: 'overview', kyc: null, properties: () => [] },
);

const page = usePage();
const authUser = computed(() => (page.props as any).auth?.user);

const activeTab = ref<Tab>(props.tab);

const tabs: { id: Tab; label: string; icon: string }[] = [
    {
        id: 'overview',
        label: 'Overview',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" /><path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" /></svg>`,
    },
    {
        id: 'kyc',
        label: 'KYC Verification',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" /></svg>`,
    },
    {
        id: 'listings',
        label: 'My Listings',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M19.006 3.705a.75.75 0 00-.512-1.41L6 6.838V3a.75.75 0 00-1.5 0v4.93l-1.006.365a.75.75 0 00.512 1.41l15-5.47zM3.019 9.386a.75.75 0 00-.507 1.408l.75.27A2.25 2.25 0 005.25 13.5v6.75a.75.75 0 001.5 0V13.5a.75.75 0 00-.75-.75H5.25a.75.75 0 01-.712-.51l-.519-.854zM10.5 6.75a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3zm-3 3a.75.75 0 000 1.5h9a.75.75 0 000-1.5h-9zm1.5 3a.75.75 0 000 1.5h6a.75.75 0 000-1.5h-6z" /></svg>`,
    },
];

const idTypes = [
    { value: 'citizenship', label: 'Citizenship Card' },
    { value: 'national_id', label: 'National ID' },
    { value: 'passport', label: 'Passport' },
    { value: 'driving_license', label: 'Driving License' },
];

const formLocked = computed(
    () => props.kycStatus === 'pending' || props.kycStatus === 'approved',
);

const form = useForm({
    id_type: props.kyc?.id_type ?? '',
    id_document: null as File | null,
    selfie_photo: null as File | null,
    full_name: props.kyc?.full_name ?? authUser.value?.name ?? '',
    father_mother_name: props.kyc?.father_mother_name ?? '',
    spouse_name: props.kyc?.spouse_name ?? '',
    citizenship_no: props.kyc?.citizenship_no ?? '',
    date_of_birth: props.kyc?.date_of_birth ?? '',
    gender: props.kyc?.gender ?? '',
    nationality: props.kyc?.nationality ?? 'Nepali',
    occupation: props.kyc?.occupation ?? '',
    mobile_no: props.kyc?.mobile_no ?? '',
    email: props.kyc?.email ?? authUser.value?.email ?? '',
    permanent_province: props.kyc?.permanent_province ?? '',
    permanent_district: props.kyc?.permanent_district ?? '',
    permanent_municipality: props.kyc?.permanent_municipality ?? '',
    permanent_ward_no: props.kyc?.permanent_ward_no ?? '',
    permanent_tole: props.kyc?.permanent_tole ?? '',
    current_province: props.kyc?.current_province ?? '',
    current_district: props.kyc?.current_district ?? '',
    current_municipality: props.kyc?.current_municipality ?? '',
    current_ward_no: props.kyc?.current_ward_no ?? '',
    current_tole: props.kyc?.current_tole ?? '',
});

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
});

const canCreateListing = computed(() => props.kycStatus === 'approved');
const totalListings = computed(
    () => props.listingCounts.pending + props.listingCounts.approved + props.listingCounts.rejected,
);

const kycLabel = computed(() => {
    if (props.kycStatus === 'approved') return 'Verified';
    if (props.kycStatus === 'pending') return 'Under Review';
    if (props.kycStatus === 'rejected') return 'Rejected';
    return 'Not Submitted';
});

const kycColor = computed(() => {
    if (props.kycStatus === 'approved') return 'text-emerald-400 bg-emerald-400/10 ring-emerald-400/20';
    if (props.kycStatus === 'pending') return 'text-amber-400 bg-amber-400/10 ring-amber-400/20';
    if (props.kycStatus === 'rejected') return 'text-red-400 bg-red-400/10 ring-red-400/20';
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

function onDocumentChange(event: Event) {
    const input = event.target as HTMLInputElement;
    form.id_document = input.files?.[0] ?? null;
}

function onSelfieChange(event: Event) {
    const input = event.target as HTMLInputElement;
    form.selfie_photo = input.files?.[0] ?? null;
}

function listingBadgeClass(status: PropertyItem['approval_status']) {
    if (status === 'approved') return 'text-emerald-400 bg-emerald-400/10 ring-1 ring-emerald-400/20';
    if (status === 'pending') return 'text-amber-400 bg-amber-400/10 ring-1 ring-amber-400/20';
    return 'text-red-400 bg-red-400/10 ring-1 ring-red-400/20';
}

function typeLabel(value: string) {
    return propertyTypes.find((t) => t.value === value)?.label ?? value;
}

function submitKyc() {
    if (formLocked.value) return;
    form.post(route('kyc.store'), { forceFormData: true, preserveScroll: true });
}

function submitListing() {
    if (!canCreateListing.value) return;
    listingForm.post(route('properties.store'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">My Dashboard</h2>
        </template>

        <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Welcome banner -->
                <div class="mb-8 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-700 p-6 shadow-xl shadow-blue-900/30">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-blue-200 text-sm font-medium">Welcome back,</p>
                            <h1 class="text-2xl font-bold text-white mt-1">{{ authUser?.name ?? 'User' }}</h1>
                            <p class="text-blue-200 text-sm mt-1">{{ authUser?.email }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div :class="['inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-bold ring-1', kycColor]">
                                <span class="relative flex h-2 w-2">
                                    <span v-if="kycStatus === 'approved'" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-current"></span>
                                </span>
                                KYC: {{ kycLabel }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-6 lg:flex-row">
                    <!-- Sidebar Navigation -->
                    <aside class="w-full shrink-0 lg:w-64">
                        <nav class="flex gap-2 overflow-x-auto lg:flex-col lg:overflow-visible rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 p-3">
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
                        <div class="mt-4 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 p-4 space-y-3 hidden lg:block">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Quick Stats</p>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="rounded-xl bg-amber-400/10 p-3 text-center">
                                    <div class="text-xl font-extrabold text-amber-400">{{ listingCounts.pending }}</div>
                                    <div class="text-xs text-amber-400/70 mt-0.5">Pending</div>
                                </div>
                                <div class="rounded-xl bg-emerald-400/10 p-3 text-center">
                                    <div class="text-xl font-extrabold text-emerald-400">{{ listingCounts.approved }}</div>
                                    <div class="text-xs text-emerald-400/70 mt-0.5">Approved</div>
                                </div>
                                <div class="rounded-xl bg-red-400/10 p-3 text-center">
                                    <div class="text-xl font-extrabold text-red-400">{{ listingCounts.rejected }}</div>
                                    <div class="text-xs text-red-400/70 mt-0.5">Rejected</div>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <!-- Main content -->
                    <div class="min-w-0 flex-1 space-y-6">

                        <!-- ── OVERVIEW TAB ── -->
                        <section v-if="activeTab === 'overview'" class="space-y-6">
                            <!-- Stat cards -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 p-6 hover:bg-white/8 transition-all">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="rounded-xl bg-blue-600/20 p-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                        </div>
                                        <span :class="['text-xs font-bold px-2.5 py-1 rounded-full ring-1', kycColor]">{{ kycLabel }}</span>
                                    </div>
                                    <p class="text-slate-400 text-sm">KYC Status</p>
                                    <p class="text-white text-2xl font-extrabold mt-1">Identity</p>
                                </div>

                                <div class="rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 p-6 hover:bg-white/8 transition-all">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="rounded-xl bg-indigo-600/20 p-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                        </div>
                                    </div>
                                    <p class="text-slate-400 text-sm">Total Listings</p>
                                    <p class="text-white text-3xl font-extrabold mt-1">{{ totalListings }}</p>
                                </div>

                                <div class="rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 p-6 hover:bg-white/8 transition-all">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="rounded-xl bg-emerald-600/20 p-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                    </div>
                                    <p class="text-slate-400 text-sm">Approved Listings</p>
                                    <p class="text-white text-3xl font-extrabold mt-1">{{ listingCounts.approved }}</p>
                                </div>
                            </div>

                            <!-- Action cards -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="rounded-2xl bg-gradient-to-br from-blue-600/20 to-indigo-600/20 border border-blue-500/20 p-6">
                                    <h3 class="text-white font-bold text-lg mb-2">KYC Verification</h3>
                                    <p class="text-slate-400 text-sm mb-4">
                                        <span v-if="kycStatus === 'approved'">Your identity is verified. You can now list properties.</span>
                                        <span v-else-if="kycStatus === 'pending'">Your documents are under review. We'll notify you soon.</span>
                                        <span v-else-if="kycStatus === 'rejected'">Your submission was rejected. Please resubmit with correct documents.</span>
                                        <span v-else>Complete identity verification to unlock property listings.</span>
                                    </p>
                                    <button
                                        @click="activeTab = 'kyc'"
                                        class="rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold px-4 py-2.5 transition-all shadow-lg shadow-blue-600/30"
                                    >
                                        {{ kycStatus === 'approved' ? 'View KYC' : kycStatus === 'pending' ? 'Check Status' : 'Start KYC' }}
                                    </button>
                                </div>

                                <div class="rounded-2xl bg-gradient-to-br from-emerald-600/20 to-teal-600/20 border border-emerald-500/20 p-6">
                                    <h3 class="text-white font-bold text-lg mb-2">List a Property</h3>
                                    <p class="text-slate-400 text-sm mb-4">
                                        <span v-if="canCreateListing">Add your property to our marketplace and reach thousands of buyers.</span>
                                        <span v-else>Complete KYC verification first to start listing your properties.</span>
                                    </p>
                                    <button
                                        @click="activeTab = 'listings'"
                                        :class="[
                                            'rounded-xl text-sm font-bold px-4 py-2.5 transition-all',
                                            canCreateListing
                                                ? 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/30'
                                                : 'bg-slate-700 text-slate-400 cursor-not-allowed',
                                        ]"
                                    >
                                        Add Listing
                                    </button>
                                </div>
                            </div>

                            <!-- Recent listings -->
                            <div v-if="properties.length > 0" class="rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 p-6">
                                <h3 class="text-white font-bold text-lg mb-4">Recent Listings</h3>
                                <div class="space-y-3">
                                    <div
                                        v-for="item in properties.slice(0, 3)"
                                        :key="item.property_id"
                                        class="flex items-center justify-between rounded-xl bg-white/5 px-4 py-3"
                                    >
                                        <div>
                                            <p class="text-white font-semibold text-sm">{{ item.property_code }}</p>
                                            <p class="text-slate-400 text-xs mt-0.5">{{ typeLabel(item.property_type) }}<span v-if="item.municipality"> · {{ item.municipality }}</span></p>
                                        </div>
                                        <span :class="['text-xs font-bold px-2.5 py-1 rounded-full capitalize ring-1', listingBadgeClass(item.approval_status)]">{{ item.approval_status }}</span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- ── KYC TAB ── -->
                        <section v-else-if="activeTab === 'kyc'" class="rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 overflow-hidden">
                            <!-- Header -->
                            <div class="px-6 py-5 border-b border-white/10 flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-white font-bold text-lg">KYC Verification</h3>
                                    <p class="text-slate-400 text-sm mt-0.5">Annex F — Client Identity Registration</p>
                                </div>
                                <span :class="['inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold ring-1', kycColor]">
                                    {{ kycLabel }}
                                </span>
                            </div>

                            <!-- Status messages -->
                            <div class="px-6 pt-5">
                                <div v-if="kycStatus === 'pending'" class="rounded-xl bg-amber-400/10 border border-amber-400/20 px-4 py-3 text-sm text-amber-300 mb-5 flex items-start gap-3">
                                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Your documents are under review. You cannot resubmit until reviewed.
                                </div>
                                <div v-else-if="kycStatus === 'approved'" class="rounded-xl bg-emerald-400/10 border border-emerald-400/20 px-4 py-3 text-sm text-emerald-300 mb-5 flex items-start gap-3">
                                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Your identity has been verified. You can now list properties.
                                </div>
                                <div v-else-if="kycStatus === 'rejected'" class="rounded-xl bg-red-400/10 border border-red-400/20 px-4 py-3 text-sm text-red-300 mb-5">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                        <div>
                                            <p class="font-semibold">Submission rejected. Please review and resubmit.</p>
                                            <p v-if="kyc?.admin_note" class="mt-1 text-red-300/80">Admin note: {{ kyc.admin_note }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- KYC Form -->
                            <form class="px-6 pb-6 space-y-8" @submit.prevent="submitKyc">
                                <fieldset :disabled="formLocked || form.processing" class="space-y-8">

                                    <!-- Personal Information -->
                                    <div>
                                        <h4 class="text-blue-400 text-xs font-bold uppercase tracking-widest mb-4 flex items-center gap-2">
                                            <span class="h-px flex-1 bg-blue-400/20"></span>
                                            Personal Information
                                            <span class="h-px flex-1 bg-blue-400/20"></span>
                                        </h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div class="sm:col-span-2">
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Full Name <span class="text-red-400">*</span></label>
                                                <input v-model="form.full_name" required class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder-slate-500 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" placeholder="As per citizenship" />
                                                <InputError class="mt-1.5" :message="form.errors.full_name" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Father / Mother Name</label>
                                                <input v-model="form.father_mother_name" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder-slate-500 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                                <InputError class="mt-1.5" :message="form.errors.father_mother_name" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Spouse Name</label>
                                                <input v-model="form.spouse_name" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder-slate-500 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Date of Birth</label>
                                                <input v-model="form.date_of_birth" type="date" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Gender</label>
                                                <select v-model="form.gender" class="w-full rounded-xl bg-slate-800 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition">
                                                    <option value="">Select gender</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Nationality</label>
                                                <input v-model="form.nationality" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Occupation</label>
                                                <input v-model="form.occupation" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Mobile Number</label>
                                                <input v-model="form.mobile_no" type="tel" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Email Address</label>
                                                <input v-model="form.email" type="email" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Permanent Address -->
                                    <div>
                                        <h4 class="text-blue-400 text-xs font-bold uppercase tracking-widest mb-4 flex items-center gap-2">
                                            <span class="h-px flex-1 bg-blue-400/20"></span>
                                            Permanent Address
                                            <span class="h-px flex-1 bg-blue-400/20"></span>
                                        </h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Province</label>
                                                <input v-model="form.permanent_province" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">District</label>
                                                <input v-model="form.permanent_district" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Municipality / VDC</label>
                                                <input v-model="form.permanent_municipality" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Ward No.</label>
                                                <input v-model="form.permanent_ward_no" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Tole / Locality</label>
                                                <input v-model="form.permanent_tole" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Current Address -->
                                    <div>
                                        <h4 class="text-blue-400 text-xs font-bold uppercase tracking-widest mb-4 flex items-center gap-2">
                                            <span class="h-px flex-1 bg-blue-400/20"></span>
                                            Current / Temporary Address
                                            <span class="h-px flex-1 bg-blue-400/20"></span>
                                        </h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Province</label>
                                                <input v-model="form.current_province" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">District</label>
                                                <input v-model="form.current_district" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Municipality / VDC</label>
                                                <input v-model="form.current_municipality" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Ward No.</label>
                                                <input v-model="form.current_ward_no" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Tole / Locality</label>
                                                <input v-model="form.current_tole" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Identity Document -->
                                    <div>
                                        <h4 class="text-blue-400 text-xs font-bold uppercase tracking-widest mb-4 flex items-center gap-2">
                                            <span class="h-px flex-1 bg-blue-400/20"></span>
                                            Identity Document
                                            <span class="h-px flex-1 bg-blue-400/20"></span>
                                        </h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Citizenship No.</label>
                                                <input v-model="form.citizenship_no" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">ID Type <span class="text-red-400">*</span></label>
                                                <select v-model="form.id_type" required class="w-full rounded-xl bg-slate-800 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 disabled:opacity-50 transition">
                                                    <option value="" disabled>Select ID type</option>
                                                    <option v-for="t in idTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
                                                </select>
                                                <InputError class="mt-1.5" :message="form.errors.id_type" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">ID Document Scan <span class="text-red-400">*</span></label>
                                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-white/20 rounded-xl cursor-pointer bg-white/5 hover:bg-white/10 transition-all">
                                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                        <svg class="w-8 h-8 mb-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                                        <p class="text-xs text-slate-400">{{ form.id_document?.name ?? 'Click to upload document' }}</p>
                                                        <p class="text-xs text-slate-500 mt-1">JPEG, PNG, WebP — max 4 MB</p>
                                                    </div>
                                                    <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="onDocumentChange" />
                                                </label>
                                                <InputError class="mt-1.5" :message="form.errors.id_document" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Selfie Photo</label>
                                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-white/20 rounded-xl cursor-pointer bg-white/5 hover:bg-white/10 transition-all">
                                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                        <svg class="w-8 h-8 mb-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                        <p class="text-xs text-slate-400">{{ form.selfie_photo?.name ?? 'Click to upload selfie' }}</p>
                                                        <p class="text-xs text-slate-500 mt-1">Optional — face visible, clear background</p>
                                                    </div>
                                                    <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="onSelfieChange" />
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <div class="flex items-center gap-4 pt-2">
                                    <button
                                        type="submit"
                                        :disabled="formLocked || form.processing"
                                        class="rounded-xl bg-blue-600 hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed px-6 py-3 text-sm font-bold text-white transition-all shadow-lg shadow-blue-600/30"
                                    >
                                        <span v-if="form.processing">Submitting…</span>
                                        <span v-else-if="kycStatus === 'rejected'">Resubmit for Review</span>
                                        <span v-else>Submit for Verification</span>
                                    </button>
                                    <p v-if="formLocked && kycStatus !== 'rejected'" class="text-slate-500 text-sm">
                                        Form locked while {{ kycStatus === 'pending' ? 'under review' : 'verified' }}.
                                    </p>
                                </div>
                            </form>
                        </section>

                        <!-- ── LISTINGS TAB ── -->
                        <section v-else class="space-y-6">
                            <!-- Properties list -->
                            <div class="rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 overflow-hidden">
                                <div class="px-6 py-5 border-b border-white/10">
                                    <h3 class="text-white font-bold text-lg">My Listings</h3>
                                    <p class="text-slate-400 text-sm mt-0.5">{{ totalListings }} total listing{{ totalListings === 1 ? '' : 's' }}</p>
                                </div>
                                <div v-if="properties.length === 0" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    <p class="text-slate-400 text-sm">No properties listed yet.</p>
                                </div>
                                <ul v-else class="divide-y divide-white/5">
                                    <li
                                        v-for="item in properties"
                                        :key="item.property_id"
                                        class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 hover:bg-white/5 transition-all"
                                    >
                                        <div class="flex items-center gap-4">
                                            <div class="rounded-xl bg-indigo-600/20 p-2.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" /></svg>
                                            </div>
                                            <div>
                                                <p class="text-white font-semibold text-sm">{{ item.property_code }}</p>
                                                <p class="text-slate-400 text-xs mt-0.5">
                                                    {{ typeLabel(item.property_type) }}
                                                    <span v-if="item.municipality"> · {{ item.municipality }}</span>
                                                    <span v-if="item.area"> · {{ item.area }}</span>
                                                </p>
                                            </div>
                                        </div>
                                        <span :class="['text-xs font-bold px-3 py-1.5 rounded-full capitalize ring-1', listingBadgeClass(item.approval_status)]">
                                            {{ item.approval_status }}
                                        </span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Add listing form -->
                            <div class="rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 overflow-hidden">
                                <div class="px-6 py-5 border-b border-white/10">
                                    <h3 class="text-white font-bold text-lg">Add New Listing</h3>
                                    <p class="text-slate-400 text-sm mt-0.5">Fill in your property details below</p>
                                </div>

                                <div v-if="!canCreateListing" class="px-6 py-8 text-center">
                                    <div class="rounded-2xl bg-amber-400/10 border border-amber-400/20 p-6 inline-block">
                                        <svg class="w-10 h-10 mx-auto text-amber-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                        <p class="text-amber-300 font-semibold mb-3">KYC verification required</p>
                                        <p class="text-slate-400 text-sm mb-4">Complete identity verification before listing a property.</p>
                                        <button @click="activeTab = 'kyc'" class="rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold px-4 py-2.5 transition-all shadow-lg shadow-blue-600/30">
                                            Start KYC Verification
                                        </button>
                                    </div>
                                </div>

                                <form v-else class="px-6 py-6 grid grid-cols-1 gap-4 sm:grid-cols-2" @submit.prevent="submitListing">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Property Type <span class="text-red-400">*</span></label>
                                        <select v-model="listingForm.property_type" required class="w-full rounded-xl bg-slate-800 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition">
                                            <option value="" disabled>Select type</option>
                                            <option v-for="t in propertyTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
                                        </select>
                                        <InputError class="mt-1.5" :message="listingForm.errors.property_type" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Purpose <span class="text-red-400">*</span></label>
                                        <select v-model="listingForm.purpose_of_listing" class="w-full rounded-xl bg-slate-800 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition">
                                            <option v-for="p in purposes" :key="p.value" :value="p.value">{{ p.label }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Ownership Role</label>
                                        <select v-model="listingForm.ownership_role" class="w-full rounded-xl bg-slate-800 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition">
                                            <option value="">Select role</option>
                                            <option v-for="r in ownershipRoles" :key="r.value" :value="r.value">{{ r.label }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Kitta No.</label>
                                        <input v-model="listingForm.kitta_no" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Land Area</label>
                                        <input v-model="listingForm.area" placeholder="e.g. 4 aana" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder-slate-500 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Covered Area (sq.ft)</label>
                                        <input v-model="listingForm.covered_area" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">No. of Floors</label>
                                        <input v-model="listingForm.no_of_floors" type="number" min="0" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Year Built</label>
                                        <input v-model="listingForm.year_of_construction" type="number" min="1800" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                                    </div>

                                    <!-- Location -->
                                    <div class="sm:col-span-2">
                                        <h4 class="text-blue-400 text-xs font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
                                            <span class="h-px flex-1 bg-blue-400/20"></span>
                                            Property Location
                                            <span class="h-px flex-1 bg-blue-400/20"></span>
                                        </h4>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Province</label>
                                        <input v-model="listingForm.province" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">District</label>
                                        <input v-model="listingForm.district" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Municipality</label>
                                        <input v-model="listingForm.municipality" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Ward No.</label>
                                        <input v-model="listingForm.ward_no" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Tole / Locality</label>
                                        <input v-model="listingForm.tole_locality" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Expected Price (Rs.)</label>
                                        <input v-model="listingForm.expected_selling_price" type="number" min="0" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Rent Amount (Rs.)</label>
                                        <input v-model="listingForm.rental_amount" type="number" min="0" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition" />
                                    </div>
                                    <div class="sm:col-span-2 flex items-center gap-4">
                                        <button
                                            type="submit"
                                            :disabled="listingForm.processing"
                                            class="rounded-xl bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 px-6 py-3 text-sm font-bold text-white transition-all shadow-lg shadow-emerald-600/30"
                                        >
                                            <span v-if="listingForm.processing">Submitting…</span>
                                            <span v-else>Submit Listing</span>
                                        </button>
                                        <InputError :message="listingForm.errors.kyc" />
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
