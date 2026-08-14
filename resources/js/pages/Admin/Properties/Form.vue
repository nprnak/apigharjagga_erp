<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface Property {
    property_id?: number;
    owner_client_id?: number;
    property_type?: string;
    status?: string;
    kitta_no?: string;
    area?: string;
    ownership_type?: string;
    ownership_certificate_no?: string;
    year_of_construction?: number;
    no_of_floors?: number;
    structure_type?: string;
}

const props = defineProps<{
    property?: Property;
    clients: { client_id: number; full_name: string; client_code: string }[];
}>();

const isEdit = !!props.property?.property_id;

const form = useForm({
    owner_client_id: props.property?.owner_client_id ?? '',
    property_type: props.property?.property_type ?? 'land',
    status: props.property?.status ?? 'draft',
    kitta_no: props.property?.kitta_no ?? '',
    area: props.property?.area ?? '',
    ownership_type: props.property?.ownership_type ?? '',
    ownership_certificate_no: props.property?.ownership_certificate_no ?? '',
    year_of_construction: props.property?.year_of_construction ?? '',
    no_of_floors: props.property?.no_of_floors ?? '',
    structure_type: props.property?.structure_type ?? '',
    province: '',
    district: '',
    municipality: '',
    ward_no: '',
    tole: '',
});

function submit() {
    if (isEdit) {
        form.put(`/admin/properties/${props.property!.property_id}`);
    } else {
        form.post('/admin/properties');
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Edit Property' : 'New Property'" />

    <AdminLayout>
        <template #title>{{ isEdit ? 'Edit Property' : 'Register Property' }}</template>

        <div class="max-w-2xl rounded-xl bg-white p-6 shadow">
            <form @submit.prevent="submit" class="space-y-4">
                <!-- Owner -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Owner (Client) *</label>
                    <select v-model="form.owner_client_id" class="w-full rounded border px-3 py-2 text-sm">
                        <option value="">Select client…</option>
                        <option v-for="c in clients" :key="c.client_id" :value="c.client_id">
                            {{ c.full_name }} ({{ c.client_code }})
                        </option>
                    </select>
                    <p v-if="form.errors.owner_client_id" class="mt-1 text-xs text-red-600">{{ form.errors.owner_client_id }}</p>
                </div>

                <!-- Property Type -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Property Type *</label>
                    <select v-model="form.property_type" class="w-full rounded border px-3 py-2 text-sm">
                        <option value="land">Land</option>
                        <option value="house">House</option>
                        <option value="apartment">Apartment</option>
                        <option value="commercial_building">Commercial Building</option>
                        <option value="office_space">Office Space</option>
                        <option value="industrial_property">Industrial Property</option>
                        <option value="agricultural_land">Agricultural Land</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Kitta + Area -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Kitta No.</label>
                        <input v-model="form.kitta_no" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Area (Ropani/Aana/Sqft)</label>
                        <input v-model="form.area" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                    </div>
                </div>

                <!-- Lalpurja + Ownership Type -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Lalpurja No.</label>
                        <input v-model="form.ownership_certificate_no" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Ownership Type</label>
                        <select v-model="form.ownership_type" class="w-full rounded border px-3 py-2 text-sm">
                            <option value="">Select…</option>
                            <option value="private">Private</option>
                            <option value="joint">Joint</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <!-- Building details -->
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Year Built</label>
                        <input v-model="form.year_of_construction" type="number" class="w-full rounded border px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Floors</label>
                        <input v-model="form.no_of_floors" type="number" class="w-full rounded border px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Structure Type</label>
                        <select v-model="form.structure_type" class="w-full rounded border px-3 py-2 text-sm">
                            <option value="">Select…</option>
                            <option value="RCC">RCC</option>
                            <option value="Load Bearing">Load Bearing</option>
                            <option value="Steel">Steel</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <!-- Status (edit only) -->
                <div v-if="isEdit">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Status *</label>
                    <select v-model="form.status" class="w-full rounded border px-3 py-2 text-sm">
                        <option value="draft">Draft</option>
                        <option value="listed">Listed</option>
                        <option value="under_verification">Under Verification</option>
                        <option value="under_valuation">Under Valuation</option>
                        <option value="under_negotiation">Under Negotiation</option>
                        <option value="sold">Sold</option>
                        <option value="rented">Rented</option>
                        <option value="leased">Leased</option>
                        <option value="withdrawn">Withdrawn</option>
                    </select>
                </div>

                <!-- Address (create only) -->
                <template v-if="!isEdit">
                    <hr />
                    <h3 class="text-sm font-semibold text-gray-600">Property Location</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Province</label>
                            <input v-model="form.province" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">District</label>
                            <input v-model="form.district" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Municipality / VDC</label>
                            <input v-model="form.municipality" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Ward No.</label>
                            <input v-model="form.ward_no" type="number" class="w-full rounded border px-3 py-2 text-sm" />
                        </div>
                        <div class="col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700">Tole / Street</label>
                            <input v-model="form.tole" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                        </div>
                    </div>
                </template>

                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="form.processing" class="rounded-md bg-slate-700 px-5 py-2 text-sm text-white hover:bg-slate-800 disabled:opacity-60">
                        {{ isEdit ? 'Update Property' : 'Register Property' }}
                    </button>
                    <a href="/admin/properties" class="rounded-md border px-5 py-2 text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
