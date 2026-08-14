<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface Staff {
    staff_id: number;
    full_name: string;
    designation: string | null;
    mobile_no: string | null;
    email: string | null;
    is_active: boolean;
    role?: { role_name: string };
}

defineProps<{ staff: Staff[] }>();
</script>

<template>
    <Head title="Staff" />

    <AdminLayout>
        <template #title>Staff Management</template>

        <div class="mb-4 flex justify-end">
            <Link href="/admin/staff/create" class="rounded-md bg-slate-700 px-4 py-2 text-sm text-white hover:bg-slate-800">
                + Add Staff
            </Link>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Designation</th>
                        <th class="px-4 py-3">Mobile</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="s in staff" :key="s.staff_id" class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ s.full_name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ s.role?.role_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ s.designation ?? '—' }}</td>
                        <td class="px-4 py-3">{{ s.mobile_no ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ s.email ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span :class="s.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'" class="rounded-full px-2 py-0.5 text-xs font-medium">
                                {{ s.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <Link :href="`/admin/staff/${s.staff_id}/edit`" class="text-blue-600 hover:underline">Edit</Link>
                        </td>
                    </tr>
                    <tr v-if="staff.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">No staff members found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
