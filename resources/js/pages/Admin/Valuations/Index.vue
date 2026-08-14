<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface ValuationRequest {
    request_id: number;
    request_code: string;
    status: string;
    purpose_of_valuation: string | null;
    created_at: string;
    client?: { full_name: string };
    property?: { property_code: string; property_type: string };
    assigned_valuator?: { full_name: string };
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    meta: { total: number };
}

const props = defineProps<{
    requests: Paginated<ValuationRequest>;
    filters: { search?: string; status?: string };
}>();

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

function applyFilter() {
    router.get('/admin/valuations', { search: search.value, status: status.value }, { preserveState: true, replace: true });
}

const statusClass: Record<string, string> = {
    received: 'bg-blue-100 text-blue-700',
    site_visit_scheduled: 'bg-yellow-100 text-yellow-700',
    in_progress: 'bg-orange-100 text-orange-700',
    report_issued: 'bg-green-100 text-green-700',
    cancelled: 'bg-red-100 text-red-600',
};
</script>

<template>
    <Head title="Valuations" />

    <AdminLayout>
        <template #title>Valuation Requests</template>

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex gap-2">
                <input v-model="search" type="text" placeholder="Search code…" class="rounded-md border px-3 py-2 text-sm" @keyup.enter="applyFilter" />
                <select v-model="status" class="rounded-md border px-3 py-2 text-sm" @change="applyFilter">
                    <option value="">All statuses</option>
                    <option value="received">Received</option>
                    <option value="site_visit_scheduled">Site Visit Scheduled</option>
                    <option value="in_progress">In Progress</option>
                    <option value="report_issued">Report Issued</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <Link href="/admin/valuations/create" class="rounded-md bg-slate-700 px-4 py-2 text-sm text-white hover:bg-slate-800">
                + New Valuation
            </Link>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Property</th>
                        <th class="px-4 py-3">Purpose</th>
                        <th class="px-4 py-3">Assigned To</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="r in requests.data" :key="r.request_id" class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ r.request_code }}</td>
                        <td class="px-4 py-3 font-medium">{{ r.client?.full_name ?? '—' }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ r.property?.property_code ?? '—' }}</td>
                        <td class="px-4 py-3 capitalize text-gray-500 text-xs">{{ r.purpose_of_valuation?.replace(/_/g, ' ') ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ r.assigned_valuator?.full_name ?? 'Unassigned' }}</td>
                        <td class="px-4 py-3">
                            <span :class="statusClass[r.status] ?? 'bg-gray-100 text-gray-600'" class="rounded-full px-2 py-0.5 text-xs font-medium">
                                {{ r.status.replace(/_/g, ' ') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ new Date(r.created_at).toLocaleDateString() }}</td>
                        <td class="px-4 py-3">
                            <Link :href="`/admin/valuations/${r.request_id}`" class="text-blue-600 hover:underline">View</Link>
                        </td>
                    </tr>
                    <tr v-if="requests.data.length === 0">
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">No valuation requests found.</td>
                    </tr>
                </tbody>
            </table>
            <div class="flex items-center justify-between border-t px-4 py-3 text-sm text-gray-600">
                <span>Total: {{ requests.meta?.total ?? requests.data.length }}</span>
                <div class="flex gap-1">
                    <template v-for="link in requests.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url" :class="[link.active ? 'bg-slate-700 text-white' : 'hover:bg-gray-100', 'rounded px-2 py-1']" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
