<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface Property {
    property_id: number;
    property_code: string;
    property_type: string;
    status: string;
    kitta_no: string | null;
    area: string | null;
    owner?: { full_name: string; client_code: string };
    address?: { municipality: string | null; district: string | null };
    created_at: string;
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    meta: { total: number };
}

const props = defineProps<{
    properties: Paginated<Property>;
    filters: { search?: string; type?: string; status?: string };
}>();

const search = ref(props.filters.search ?? '');
const type = ref(props.filters.type ?? '');
const status = ref(props.filters.status ?? '');

function applyFilter() {
    router.get('/admin/properties', { search: search.value, type: type.value, status: status.value }, { preserveState: true, replace: true });
}

const statusClass: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-600',
    listed: 'bg-blue-100 text-blue-700',
    under_verification: 'bg-yellow-100 text-yellow-700',
    under_valuation: 'bg-orange-100 text-orange-700',
    sold: 'bg-green-100 text-green-700',
    rented: 'bg-teal-100 text-teal-700',
    withdrawn: 'bg-red-100 text-red-600',
};
</script>

<template>
    <Head title="Properties" />

    <AdminLayout>
        <template #title>Properties</template>

        <!-- Toolbar -->
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap gap-2">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search code, kitta…"
                    class="rounded-md border px-3 py-2 text-sm shadow-sm"
                    @keyup.enter="applyFilter"
                />
                <select v-model="type" class="rounded-md border px-3 py-2 text-sm" @change="applyFilter">
                    <option value="">All types</option>
                    <option value="land">Land</option>
                    <option value="house">House</option>
                    <option value="apartment">Apartment</option>
                    <option value="commercial_building">Commercial</option>
                    <option value="agricultural_land">Agricultural</option>
                </select>
                <select v-model="status" class="rounded-md border px-3 py-2 text-sm" @change="applyFilter">
                    <option value="">All statuses</option>
                    <option value="draft">Draft</option>
                    <option value="listed">Listed</option>
                    <option value="under_valuation">Under Valuation</option>
                    <option value="sold">Sold</option>
                </select>
            </div>
            <Link href="/admin/properties/create" class="rounded-md bg-slate-700 px-4 py-2 text-sm text-white hover:bg-slate-800">
                + New Property
            </Link>
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-xl bg-white shadow">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Owner</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Kitta No.</th>
                        <th class="px-4 py-3">Area</th>
                        <th class="px-4 py-3">Location</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in properties.data" :key="p.property_id" class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ p.property_code }}</td>
                        <td class="px-4 py-3 font-medium">{{ p.owner?.full_name ?? '—' }}</td>
                        <td class="px-4 py-3 capitalize">{{ p.property_type.replace(/_/g, ' ') }}</td>
                        <td class="px-4 py-3">{{ p.kitta_no ?? '—' }}</td>
                        <td class="px-4 py-3">{{ p.area ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ [p.address?.municipality, p.address?.district].filter(Boolean).join(', ') || '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span :class="statusClass[p.status] ?? 'bg-gray-100 text-gray-600'" class="rounded-full px-2 py-0.5 text-xs font-medium">
                                {{ p.status.replace(/_/g, ' ') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <Link :href="`/admin/properties/${p.property_id}`" class="mr-2 text-blue-600 hover:underline">View</Link>
                            <Link :href="`/admin/properties/${p.property_id}/edit`" class="text-gray-500 hover:underline">Edit</Link>
                        </td>
                    </tr>
                    <tr v-if="properties.data.length === 0">
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">No properties found.</td>
                    </tr>
                </tbody>
            </table>
            <div class="flex items-center justify-between border-t px-4 py-3 text-sm text-gray-600">
                <span>Total: {{ properties.meta?.total ?? properties.data.length }}</span>
                <div class="flex gap-1">
                    <template v-for="link in properties.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url" :class="[link.active ? 'bg-slate-700 text-white' : 'hover:bg-gray-100', 'rounded px-2 py-1']" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
