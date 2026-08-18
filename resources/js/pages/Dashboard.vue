<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from '@/route';

type KycStatus = 'pending' | 'approved' | 'rejected' | null;
type Tab = 'overview' | 'kyc' | 'listings';

type KycRecord = {
    status: Exclude<KycStatus, null>;
    id_type: string;
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
        listingCounts: {
            pending: number;
            approved: number;
            rejected: number;
        };
        properties?: PropertyItem[];
    }>(),
    {
        tab: 'overview',
        kyc: null,
        properties: () => [],
    },
);

const activeTab = ref<Tab>(props.tab);

const tabs: { id: Tab; label: string }[] = [
    { id: 'overview', label: 'Overview' },
    { id: 'kyc', label: 'KYC Verification' },
    { id: 'listings', label: 'My Listings' },
];

const idTypes = [
    { value: 'citizenship', label: 'Citizenship' },
    { value: 'national_id', label: 'National ID' },
    { value: 'passport', label: 'Passport' },
    { value: 'driving_license', label: 'Driving license' },
];

const formLocked = computed(
    () => props.kycStatus === 'pending' || props.kycStatus === 'approved',
);

const form = useForm({
    id_type: props.kyc?.id_type ?? '',
    id_document: null as File | null,
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

const propertyTypes = [
    { value: 'land', label: 'Land' },
    { value: 'house', label: 'House' },
    { value: 'apartment', label: 'Apartment' },
    { value: 'commercial_building', label: 'Commercial building' },
    { value: 'office_space', label: 'Office space' },
    { value: 'industrial_property', label: 'Industrial property' },
    { value: 'agricultural_land', label: 'Agricultural land' },
    { value: 'other', label: 'Other' },
];

const ownershipRoles = [
    { value: 'self', label: 'Self' },
    { value: 'family_member', label: 'Family member' },
    { value: 'authorized_representative', label: 'Authorized representative' },
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

const kycLabel = computed(() => {
    if (props.kycStatus === 'approved') return 'Approved';
    if (props.kycStatus === 'rejected') return 'Rejected';
    if (props.kycStatus === 'pending') return 'Pending';
    return 'Not submitted';
});

const totalListings = computed(
    () => props.listingCounts.pending + props.listingCounts.approved + props.listingCounts.rejected,
);

function tabClass(id: Tab) {
    return activeTab.value === id
        ? 'bg-brand-600 text-white'
        : 'text-slate-700 hover:bg-slate-100';
}

function kycBadgeClass() {
    if (props.kycStatus === 'approved') return 'bg-brand-600 text-white';
    if (props.kycStatus === 'pending') return 'bg-brand-50 text-brand-800 ring-1 ring-brand-200';
    if (props.kycStatus === 'rejected') return 'bg-slate-800 text-white';
    return 'bg-slate-100 text-slate-600';
}

function onDocumentChange(event: Event) {
    const input = event.target as HTMLInputElement;
    form.id_document = input.files?.[0] ?? null;
}

function listingBadgeClass(status: PropertyItem['approval_status']) {
    if (status === 'approved') return 'bg-brand-600 text-white';
    if (status === 'pending') return 'bg-brand-50 text-brand-800 ring-1 ring-brand-200';
    return 'bg-slate-800 text-white';
}

function typeLabel(value: string) {
    return propertyTypes.find((t) => t.value === value)?.label ?? value;
}

function submitKyc() {
    if (formLocked.value) return;
    form.post(route('kyc.store'), {
        forceFormData: true,
        preserveScroll: true,
    });
}

function submitListing() {
    if (!canCreateListing.value) return;
    listingForm.post(route('properties.store'), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-800">Dashboard</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-6 lg:flex-row">
                    <aside class="w-full shrink-0 lg:w-60">
                        <nav class="flex gap-2 overflow-x-auto lg:flex-col lg:overflow-visible">
                            <button
                                v-for="tab in tabs"
                                :key="tab.id"
                                type="button"
                                :class="[
                                    'rounded-lg px-4 py-2.5 text-left text-sm font-semibold whitespace-nowrap transition',
                                    tabClass(tab.id),
                                ]"
                                @click="activeTab = tab.id"
                            >
                                {{ tab.label }}
                            </button>
                        </nav>
                    </aside>

                    <div class="min-w-0 flex-1">
                        <section v-if="activeTab === 'overview'" class="space-y-6">
                            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                <h3 class="text-sm font-bold tracking-wide text-slate-500 uppercase">
                                    KYC status
                                </h3>
                                <div class="mt-3 flex items-center gap-3">
                                    <span
                                        :class="[
                                            'inline-flex rounded-full px-3 py-1 text-xs font-bold',
                                            kycBadgeClass(),
                                        ]"
                                    >
                                        {{ kycLabel }}
                                    </span>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                <h3 class="text-sm font-bold tracking-wide text-slate-500 uppercase">
                                    My listings
                                </h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ totalListings }} total listing{{ totalListings === 1 ? '' : 's' }}
                                </p>
                                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                        <div class="text-xs font-bold tracking-wide text-slate-500 uppercase">
                                            Pending
                                        </div>
                                        <div class="mt-1 text-2xl font-extrabold text-slate-900">
                                            {{ listingCounts.pending }}
                                        </div>
                                    </div>
                                    <div class="rounded-xl border border-brand-100 bg-brand-50 p-4">
                                        <div class="text-xs font-bold tracking-wide text-brand-700 uppercase">
                                            Approved
                                        </div>
                                        <div class="mt-1 text-2xl font-extrabold text-brand-800">
                                            {{ listingCounts.approved }}
                                        </div>
                                    </div>
                                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                        <div class="text-xs font-bold tracking-wide text-slate-500 uppercase">
                                            Rejected
                                        </div>
                                        <div class="mt-1 text-2xl font-extrabold text-slate-900">
                                            {{ listingCounts.rejected }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section
                            v-else-if="activeTab === 'kyc'"
                            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
                        >
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <h3 class="text-lg font-bold text-slate-900">KYC Verification</h3>
                                <span
                                    :class="[
                                        'inline-flex rounded-full px-3 py-1 text-xs font-bold',
                                        kycBadgeClass(),
                                    ]"
                                >
                                    {{ kycLabel }}
                                </span>
                            </div>

                            <p v-if="kycStatus === 'pending'" class="mt-2 text-sm text-slate-500">
                                Your document is under review. You cannot submit again until it is reviewed.
                            </p>
                            <p v-else-if="kycStatus === 'approved'" class="mt-2 text-sm text-slate-500">
                                Your identity has been verified. No further action is needed.
                            </p>
                            <p v-else-if="kycStatus === 'rejected'" class="mt-2 text-sm text-slate-500">
                                Your previous submission was rejected. Review the note below and upload a new
                                document.
                            </p>
                            <p v-else class="mt-2 text-sm text-slate-500">
                                Upload a photo of your ID so we can verify your account.
                            </p>

                            <div
                                v-if="kycStatus === 'rejected' && kyc?.admin_note"
                                class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700"
                            >
                                <div class="text-xs font-bold tracking-wide text-slate-500 uppercase">
                                    Admin note
                                </div>
                                <p class="mt-1">{{ kyc.admin_note }}</p>
                            </div>

                            <form class="mt-6 space-y-4" @submit.prevent="submitKyc">
                                <fieldset :disabled="formLocked || form.processing" class="space-y-4">
                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">
                                            ID type
                                        </label>
                                        <select
                                            v-model="form.id_type"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10 disabled:bg-slate-50 disabled:text-slate-500"
                                        >
                                            <option value="" disabled>Select an ID type</option>
                                            <option
                                                v-for="type in idTypes"
                                                :key="type.value"
                                                :value="type.value"
                                            >
                                                {{ type.label }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.id_type" />
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">
                                            ID document
                                        </label>
                                        <input
                                            type="file"
                                            accept="image/jpeg,image/png,image/webp"
                                            class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700"
                                            @change="onDocumentChange"
                                        />
                                        <p class="mt-1 text-xs text-slate-400">JPEG, PNG, or WebP. Max 4 MB.</p>
                                        <InputError class="mt-2" :message="form.errors.id_document" />
                                    </div>
                                </fieldset>

                                <button
                                    type="submit"
                                    :disabled="formLocked || form.processing"
                                    class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    {{ kycStatus === 'rejected' ? 'Resubmit' : 'Submit for review' }}
                                </button>
                            </form>
                        </section>

                        <section v-else class="space-y-6">
                            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                <h3 class="text-lg font-bold text-slate-900">My Listings</h3>
                                <p v-if="properties.length === 0" class="mt-2 text-sm text-slate-500">
                                    You have not submitted any properties yet.
                                </p>
                                <ul v-else class="mt-4 divide-y divide-slate-100">
                                    <li
                                        v-for="item in properties"
                                        :key="item.property_id"
                                        class="flex flex-wrap items-center justify-between gap-3 py-3"
                                    >
                                        <div>
                                            <div class="text-sm font-bold text-slate-900">
                                                {{ item.property_code }}
                                            </div>
                                            <div class="text-sm text-slate-500">
                                                {{ typeLabel(item.property_type) }}
                                                <span v-if="item.municipality"> · {{ item.municipality }}</span>
                                                <span v-if="item.area"> · {{ item.area }}</span>
                                            </div>
                                        </div>
                                        <span
                                            :class="[
                                                'inline-flex rounded-full px-3 py-1 text-xs font-bold capitalize',
                                                listingBadgeClass(item.approval_status),
                                            ]"
                                        >
                                            {{ item.approval_status }}
                                        </span>
                                    </li>
                                </ul>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                <h3 class="text-lg font-bold text-slate-900">Add listing</h3>

                                <div
                                    v-if="!canCreateListing"
                                    class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700"
                                >
                                    Complete KYC verification before adding a listing.
                                    <button
                                        type="button"
                                        class="mt-3 block font-bold text-brand-700 hover:text-brand-800"
                                        @click="activeTab = 'kyc'"
                                    >
                                        Go to KYC Verification
                                    </button>
                                </div>

                                <form v-else class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2" @submit.prevent="submitListing">
                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">Property type</label>
                                        <select
                                            v-model="listingForm.property_type"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10"
                                        >
                                            <option value="" disabled>Select type</option>
                                            <option v-for="type in propertyTypes" :key="type.value" :value="type.value">
                                                {{ type.label }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="listingForm.errors.property_type" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">Purpose</label>
                                        <select
                                            v-model="listingForm.purpose_of_listing"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10"
                                        >
                                            <option v-for="item in purposes" :key="item.value" :value="item.value">
                                                {{ item.label }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="listingForm.errors.purpose_of_listing" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">Ownership role</label>
                                        <select
                                            v-model="listingForm.ownership_role"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10"
                                        >
                                            <option value="">Optional</option>
                                            <option v-for="item in ownershipRoles" :key="item.value" :value="item.value">
                                                {{ item.label }}
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">Kitta no.</label>
                                        <input
                                            v-model="listingForm.kitta_no"
                                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">Area</label>
                                        <input
                                            v-model="listingForm.area"
                                            placeholder="e.g. 4 aana"
                                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">Covered area</label>
                                        <input
                                            v-model="listingForm.covered_area"
                                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">Floors</label>
                                        <input
                                            v-model="listingForm.no_of_floors"
                                            type="number"
                                            min="0"
                                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">Year built</label>
                                        <input
                                            v-model="listingForm.year_of_construction"
                                            type="number"
                                            min="1800"
                                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">Province</label>
                                        <input
                                            v-model="listingForm.province"
                                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">District</label>
                                        <input
                                            v-model="listingForm.district"
                                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">Municipality</label>
                                        <input
                                            v-model="listingForm.municipality"
                                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">Ward</label>
                                        <input
                                            v-model="listingForm.ward_no"
                                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10"
                                        />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">Tole / locality</label>
                                        <input
                                            v-model="listingForm.tole_locality"
                                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">Expected price (Rs.)</label>
                                        <input
                                            v-model="listingForm.expected_selling_price"
                                            type="number"
                                            min="0"
                                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-semibold text-slate-700">Rent amount (Rs.)</label>
                                        <input
                                            v-model="listingForm.rental_amount"
                                            type="number"
                                            min="0"
                                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-500/10"
                                        />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <button
                                            type="submit"
                                            :disabled="listingForm.processing"
                                            class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-brand-700 disabled:opacity-50"
                                        >
                                            Submit listing
                                        </button>
                                        <InputError class="mt-2" :message="listingForm.errors.kyc" />
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
