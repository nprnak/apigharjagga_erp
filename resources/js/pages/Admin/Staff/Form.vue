<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface StaffMember {
    staff_id?: number;
    role_id?: number;
    full_name?: string;
    designation?: string;
    mobile_no?: string;
    email?: string;
    is_active?: boolean;
}

const props = defineProps<{
    staff?: StaffMember;
    roles: { role_id: number; role_name: string }[];
}>();

const isEdit = !!props.staff?.staff_id;

const form = useForm({
    role_id: props.staff?.role_id ?? '',
    full_name: props.staff?.full_name ?? '',
    designation: props.staff?.designation ?? '',
    mobile_no: props.staff?.mobile_no ?? '',
    email: props.staff?.email ?? '',
    is_active: props.staff?.is_active ?? true,
});

function submit() {
    if (isEdit) {
        form.put(`/admin/staff/${props.staff!.staff_id}`);
    } else {
        form.post('/admin/staff');
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Edit Staff' : 'Add Staff'" />

    <AdminLayout>
        <template #title>{{ isEdit ? 'Edit Staff Member' : 'Add Staff Member' }}</template>

        <div class="max-w-lg rounded-xl bg-white p-6 shadow">
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Role *</label>
                    <select v-model="form.role_id" class="w-full rounded border px-3 py-2 text-sm">
                        <option value="">Select role…</option>
                        <option v-for="r in roles" :key="r.role_id" :value="r.role_id">{{ r.role_name }}</option>
                    </select>
                    <p v-if="form.errors.role_id" class="mt-1 text-xs text-red-600">{{ form.errors.role_id }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Full Name *</label>
                    <input v-model="form.full_name" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                    <p v-if="form.errors.full_name" class="mt-1 text-xs text-red-600">{{ form.errors.full_name }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Designation</label>
                    <input v-model="form.designation" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Mobile</label>
                        <input v-model="form.mobile_no" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <input v-model="form.email" type="email" class="w-full rounded border px-3 py-2 text-sm" />
                    </div>
                </div>

                <div v-if="isEdit" class="flex items-center gap-2">
                    <input v-model="form.is_active" type="checkbox" id="is_active" class="h-4 w-4 rounded border-gray-300" />
                    <label for="is_active" class="text-sm text-gray-700">Active</label>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="form.processing" class="rounded-md bg-slate-700 px-5 py-2 text-sm text-white hover:bg-slate-800 disabled:opacity-60">
                        {{ isEdit ? 'Update' : 'Add Staff' }}
                    </button>
                    <a href="/admin/staff" class="rounded-md border px-5 py-2 text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
