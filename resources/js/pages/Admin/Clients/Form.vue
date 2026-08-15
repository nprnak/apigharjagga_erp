<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface Client {
    client_id?: number;
    client_code?: string;
    client_type?: string;
    full_name?: string;
    father_mother_name?: string;
    citizenship_no?: string;
    mobile_no?: string;
    email?: string;
    gender?: string;
    date_of_birth?: string;
    occupation?: string;
}

const props = defineProps<{
    client?: Client;
    staffList: { staff_id: number; full_name: string }[];
}>();

const isEdit = !!props.client?.client_id;

const form = useForm({
    client_type: props.client?.client_type ?? 'owner',
    full_name: props.client?.full_name ?? '',
    father_mother_name: props.client?.father_mother_name ?? '',
    citizenship_no: props.client?.citizenship_no ?? '',
    mobile_no: props.client?.mobile_no ?? '',
    email: props.client?.email ?? '',
    gender: props.client?.gender ?? '',
    date_of_birth: props.client?.date_of_birth ?? '',
    occupation: props.client?.occupation ?? '',
    province: '',
    district: '',
    municipality: '',
    ward_no: '',
    tole: '',
});

function submit() {
    if (isEdit) {
        form.put(`/admin/clients/${props.client!.client_id}`);
    } else {
        form.post('/admin/clients');
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Edit Client' : 'New Client'" />

    <AdminLayout>
        <template #title>{{ isEdit ? 'Edit Client' : 'Register Client' }}</template>

        <div class="max-w-2xl rounded-xl bg-white p-6 shadow">
            <form @submit.prevent="submit" class="space-y-4">
                <!-- Client Type -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Client Type *</label>
                    <select v-model="form.client_type" class="w-full rounded border px-3 py-2 text-sm">
                        <option value="owner">Owner</option>
                        <option value="buyer">Buyer</option>
                        <option value="investor">Investor</option>
                        <option value="tenant">Tenant</option>
                        <option value="agent">Agent</option>
                        <option value="other">Other</option>
                    </select>
                    <p v-if="form.errors.client_type" class="mt-1 text-xs text-red-600">{{ form.errors.client_type }}</p>
                </div>

                <!-- Full Name -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Full Name *</label>
                    <input v-model="form.full_name" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                    <p v-if="form.errors.full_name" class="mt-1 text-xs text-red-600">{{ form.errors.full_name }}</p>
                </div>

                <!-- Father/Mother Name -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Father / Mother Name</label>
                    <input v-model="form.father_mother_name" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                </div>

                <!-- Citizenship No -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Citizenship No.</label>
                    <input v-model="form.citizenship_no" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                    <p v-if="form.errors.citizenship_no" class="mt-1 text-xs text-red-600">{{ form.errors.citizenship_no }}</p>
                </div>

                <!-- Mobile + Email -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Mobile *</label>
                        <input v-model="form.mobile_no" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                        <p v-if="form.errors.mobile_no" class="mt-1 text-xs text-red-600">{{ form.errors.mobile_no }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <input v-model="form.email" type="email" class="w-full rounded border px-3 py-2 text-sm" />
                    </div>
                </div>

                <!-- Gender + DOB -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Gender</label>
                        <select v-model="form.gender" class="w-full rounded border px-3 py-2 text-sm">
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Date of Birth</label>
                        <input v-model="form.date_of_birth" type="date" class="w-full rounded border px-3 py-2 text-sm" />
                    </div>
                </div>

                <!-- Occupation -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Occupation</label>
                    <input v-model="form.occupation" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                </div>

                <!-- Address (only on create) -->
                <template v-if="!isEdit">
                    <hr />
                    <h3 class="text-sm font-semibold text-gray-600">Permanent Address</h3>
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

                <!-- Actions -->
                <div class="flex gap-3 pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-md bg-slate-700 px-5 py-2 text-sm text-white hover:bg-slate-800 disabled:opacity-60"
                    >
                        {{ isEdit ? 'Update Client' : 'Register Client' }}
                    </button>
                    <a href="/admin/clients" class="rounded-md border px-5 py-2 text-sm text-gray-600 hover:bg-gray-50">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
