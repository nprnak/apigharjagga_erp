<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface Stats {
    total_clients: number;
    total_properties: number;
    pending_valuations: number;
    in_progress_valuations: number;
    properties_by_type: Record<string, number>;
    recent_clients: Array<{
        client_id: number;
        client_code: string;
        full_name: string;
        client_type: string;
        mobile_no: string;
        created_at: string;
    }>;
    recent_valuations: Array<{
        request_id: number;
        request_code: string;
        status: string;
        created_at: string;
        client?: { full_name: string };
        property?: { property_code: string };
    }>;
}

defineProps<{ stats: Stats }>();

const statusClass: Record<string, string> = {
    received: 'bg-blue-100 text-blue-700',
    site_visit_scheduled: 'bg-yellow-100 text-yellow-700',
    in_progress: 'bg-orange-100 text-orange-700',
    report_issued: 'bg-green-100 text-green-700',
    cancelled: 'bg-red-100 text-red-700',
};
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <template #title>Dashboard</template>

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-xl bg-white p-5 shadow">
                <p class="text-sm text-gray-500">Total Clients</p>
                <p class="mt-1 text-3xl font-bold text-slate-700">{{ stats.total_clients }}</p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow">
                <p class="text-sm text-gray-500">Total Properties</p>
                <p class="mt-1 text-3xl font-bold text-slate-700">{{ stats.total_properties }}</p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow">
                <p class="text-sm text-gray-500">Pending Valuations</p>
                <p class="mt-1 text-3xl font-bold text-blue-600">{{ stats.pending_valuations }}</p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow">
                <p class="text-sm text-gray-500">In-Progress Valuations</p>
                <p class="mt-1 text-3xl font-bold text-orange-500">{{ stats.in_progress_valuations }}</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Recent Clients -->
            <div class="rounded-xl bg-white p-5 shadow">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-700">Recent Clients</h2>
                    <a href="/admin/clients" class="text-sm text-blue-600 hover:underline">View all</a>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="pb-2">Code</th>
                            <th class="pb-2">Name</th>
                            <th class="pb-2">Type</th>
                            <th class="pb-2">Mobile</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="c in stats.recent_clients"
                            :key="c.client_id"
                            class="border-b hover:bg-gray-50"
                        >
                            <td class="py-2 font-mono text-xs text-gray-500">{{ c.client_code }}</td>
                            <td class="py-2 font-medium">{{ c.full_name }}</td>
                            <td class="py-2 capitalize">{{ c.client_type }}</td>
                            <td class="py-2">{{ c.mobile_no }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Recent Valuations -->
            <div class="rounded-xl bg-white p-5 shadow">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-700">Recent Valuations</h2>
                    <a href="/admin/valuations" class="text-sm text-blue-600 hover:underline">View all</a>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="pb-2">Code</th>
                            <th class="pb-2">Client</th>
                            <th class="pb-2">Property</th>
                            <th class="pb-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="v in stats.recent_valuations"
                            :key="v.request_id"
                            class="border-b hover:bg-gray-50"
                        >
                            <td class="py-2 font-mono text-xs text-gray-500">{{ v.request_code }}</td>
                            <td class="py-2">{{ v.client?.full_name ?? '—' }}</td>
                            <td class="py-2 font-mono text-xs">{{ v.property?.property_code ?? '—' }}</td>
                            <td class="py-2">
                                <span
                                    :class="statusClass[v.status] ?? 'bg-gray-100 text-gray-600'"
                                    class="rounded-full px-2 py-0.5 text-xs font-medium"
                                >
                                    {{ v.status.replace(/_/g, ' ') }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
