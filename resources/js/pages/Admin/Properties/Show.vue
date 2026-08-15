<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface Address { municipality: string | null; district: string | null; ward_no: number | null; tole: string | null }
interface Photo { photo_id: number; file_path: string; caption: string | null; is_primary: boolean }
interface Document { doc_id: number; file_path: string; docType?: { doc_name: string } }
interface ValuationReport { report_id: number; report_no: string; valuation_type: string; valuated_amount: number; approval_status: string }
interface ValuationRequest { request_id: number; request_code: string; status: string; reports: ValuationReport[] }
interface Owner { full_name: string; client_code: string; client_id: number }

interface Property {
    property_id: number;
    property_code: string;
    property_type: string;
    status: string;
    kitta_no: string | null;
    area: string | null;
    ownership_type: string | null;
    ownership_certificate_no: string | null;
    year_of_construction: number | null;
    no_of_floors: number | null;
    structure_type: string | null;
    road_access: string | null;
    facing_direction: string | null;
    owner: Owner;
    address: Address | null;
    photos: Photo[];
    documents: Document[];
    valuation_requests: ValuationRequest[];
}

defineProps<{ property: Property }>();

const statusClass: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-600',
    listed: 'bg-blue-100 text-blue-700',
    under_valuation: 'bg-orange-100 text-orange-700',
    sold: 'bg-green-100 text-green-700',
};
</script>

<template>
    <Head :title="property.property_code" />

    <AdminLayout>
        <template #title>Property: {{ property.property_code }}</template>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Details -->
            <div class="rounded-xl bg-white p-6 shadow lg:col-span-1">
                <div class="mb-3 flex items-center justify-between">
                    <span class="font-mono text-xs text-gray-400">{{ property.property_code }}</span>
                    <span :class="statusClass[property.status] ?? 'bg-gray-100 text-gray-600'" class="rounded-full px-2 py-0.5 text-xs font-medium capitalize">
                        {{ property.status.replace(/_/g, ' ') }}
                    </span>
                </div>
                <h2 class="text-lg font-bold text-gray-800 capitalize">{{ property.property_type.replace(/_/g, ' ') }}</h2>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Owner</dt><dd><Link :href="`/admin/clients/${property.owner.client_id}`" class="text-blue-600 hover:underline">{{ property.owner.full_name }}</Link></dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Kitta No.</dt><dd>{{ property.kitta_no ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Area</dt><dd>{{ property.area ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Lalpurja No.</dt><dd>{{ property.ownership_certificate_no ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Ownership</dt><dd class="capitalize">{{ property.ownership_type ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Structure</dt><dd>{{ property.structure_type ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Floors</dt><dd>{{ property.no_of_floors ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Year Built</dt><dd>{{ property.year_of_construction ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Road Access</dt><dd>{{ property.road_access ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Facing</dt><dd>{{ property.facing_direction ?? '—' }}</dd></div>
                </dl>
                <div v-if="property.address" class="mt-4 rounded-md bg-gray-50 p-3 text-xs text-gray-600">
                    {{ [property.address.tole, property.address.municipality, property.address.ward_no ? `Ward ${property.address.ward_no}` : null, property.address.district].filter(Boolean).join(', ') }}
                </div>
                <div class="mt-4 flex gap-2">
                    <Link :href="`/admin/properties/${property.property_id}/edit`" class="flex-1 rounded-md border px-4 py-2 text-center text-sm text-gray-600 hover:bg-gray-50">Edit</Link>
                    <Link :href="`/admin/valuations/create?property_id=${property.property_id}`" class="flex-1 rounded-md bg-slate-700 px-4 py-2 text-center text-sm text-white hover:bg-slate-800">+ Valuation</Link>
                </div>
            </div>

            <div class="space-y-6 lg:col-span-2">
                <!-- Photos -->
                <div class="rounded-xl bg-white p-5 shadow">
                    <h3 class="mb-3 font-semibold text-gray-700">Photos ({{ property.photos.length }})</h3>
                    <div v-if="property.photos.length > 0" class="grid grid-cols-3 gap-2">
                        <img v-for="ph in property.photos" :key="ph.photo_id" :src="`/storage/${ph.file_path}`" class="aspect-video w-full rounded object-cover" />
                    </div>
                    <p v-else class="text-sm text-gray-400">No photos uploaded.</p>
                </div>

                <!-- Valuations -->
                <div class="rounded-xl bg-white p-5 shadow">
                    <h3 class="mb-3 font-semibold text-gray-700">Valuation Requests ({{ property.valuation_requests.length }})</h3>
                    <div v-if="property.valuation_requests.length > 0" class="space-y-2">
                        <div v-for="vr in property.valuation_requests" :key="vr.request_id" class="flex items-center justify-between rounded-md border px-3 py-2 text-sm">
                            <span class="font-mono text-xs text-gray-500">{{ vr.request_code }}</span>
                            <span class="capitalize text-gray-500">{{ vr.status.replace(/_/g, ' ') }}</span>
                            <span class="text-xs text-gray-400">{{ vr.reports.length }} report(s)</span>
                            <Link :href="`/admin/valuations/${vr.request_id}`" class="text-blue-600 hover:underline">View</Link>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-400">No valuations yet.</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
