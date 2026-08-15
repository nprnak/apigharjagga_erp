<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface Client {
    client_id: number;
    client_code: string;
    full_name: string;
    client_type: string;
    mobile_no: string;
    email: string | null;
    is_active: boolean;
    created_at: string;
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    meta: { current_page: number; last_page: number; total: number };
}

const props = defineProps<{
    clients: Paginated<Client>;
    filters: { search?: string; type?: string };
}>();

const search = ref(props.filters.search ?? '');
const type = ref(props.filters.type ?? '');

function applyFilter() {
    router.get('/admin/clients', { search: search.value, type: type.value }, { preserveState: true, replace: true });
}
</script>

<template>
    <Head title="Clients" />

    <AdminLayout>
        <template #title>Clients</template>

        <!-- Toolbar -->
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex gap-2">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search name, code, mobile…"
                    class="rounded-md border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-400"
                    @keyup.enter="applyFilter"
                />
                <select
                    v-model="type"
                    class="rounded-md border px-3 py-2 text-sm shadow-sm"
                    @change="applyFilter"
                >
                    <option value="">All types</option>
                    <option value="owner">Owner</option>
                    <option value="buyer">Buyer</option>
                    <option value="investor">Investor</option>
                    <option value="tenant">Tenant</option>
                    <option value="agent">Agent</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <Link
                href="/admin/clients/create"
                class="rounded-md bg-slate-700 px-4 py-2 text-sm text-white hover:bg-slate-800"
            >
                + New Client
            </Link>
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-xl bg-white shadow">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Mobile</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="c in clients.data"
                        :key="c.client_id"
                        class="border-b hover:bg-gray-50"
                    >
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ c.client_code }}</td>
                        <td class="px-4 py-3 font-medium">{{ c.full_name }}</td>
                        <td class="px-4 py-3 capitalize">{{ c.client_type }}</td>
                        <td class="px-4 py-3">{{ c.mobile_no }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ c.email ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span
                                :class="c.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                            >
                                {{ c.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <Link
                                :href="`/admin/clients/${c.client_id}`"
                                class="mr-2 text-blue-600 hover:underline"
                            >
                                View
                            </Link>
                            <Link
                                :href="`/admin/clients/${c.client_id}/edit`"
                                class="text-gray-500 hover:underline"
                            >
                                Edit
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="clients.data.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">No clients found.</td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="flex items-center justify-between border-t px-4 py-3 text-sm text-gray-600">
                <span>Total: {{ clients.meta?.total ?? clients.data.length }}</span>
                <div class="flex gap-1">
                    <template v-for="link in clients.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            :class="[link.active ? 'bg-slate-700 text-white' : 'hover:bg-gray-100', 'rounded px-2 py-1']"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
