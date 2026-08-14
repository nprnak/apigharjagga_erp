<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface Address {
    address_id: number;
    province: string | null;
    district: string | null;
    municipality: string | null;
    ward_no: number | null;
    tole: string | null;
}

interface DocType { doc_name: string }
interface Document { doc_id: number; file_path: string; description: string | null; docType?: DocType }
interface ServiceReq { request_id: number; status: string; serviceType?: { service_name: string } }
interface Property { property_id: number; property_code: string; property_type: string; status: string }

interface Client {
    client_id: number;
    client_code: string;
    client_type: string;
    full_name: string;
    father_mother_name: string | null;
    citizenship_no: string | null;
    mobile_no: string;
    email: string | null;
    gender: string | null;
    date_of_birth: string | null;
    occupation: string | null;
    is_active: boolean;
    registration_date: string;
    permanent_address?: Address;
    documents: Document[];
    service_requests: ServiceReq[];
    properties: Property[];
}

defineProps<{ client: Client }>();
</script>

<template>
    <Head :title="client.full_name" />

    <AdminLayout>
        <template #title>Client: {{ client.full_name }}</template>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Profile card -->
            <div class="rounded-xl bg-white p-6 shadow lg:col-span-1">
                <div class="mb-4 flex items-center justify-between">
                    <span class="font-mono text-xs text-gray-400">{{ client.client_code }}</span>
                    <span
                        :class="client.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                        class="rounded-full px-2 py-0.5 text-xs font-medium"
                    >
                        {{ client.is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <h2 class="text-xl font-bold text-gray-800">{{ client.full_name }}</h2>
                <p class="capitalize text-sm text-gray-500">{{ client.client_type }}</p>

                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Father / Mother</dt>
                        <dd>{{ client.father_mother_name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Citizenship No.</dt>
                        <dd>{{ client.citizenship_no ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Mobile</dt>
                        <dd>{{ client.mobile_no }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Email</dt>
                        <dd>{{ client.email ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Gender</dt>
                        <dd>{{ client.gender ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Registered</dt>
                        <dd>{{ client.registration_date }}</dd>
                    </div>
                </dl>

                <div v-if="client.permanent_address" class="mt-4 rounded-md bg-gray-50 p-3 text-xs text-gray-600">
                    <p class="mb-1 font-semibold text-gray-700">Permanent Address</p>
                    {{ [client.permanent_address.tole, client.permanent_address.municipality, `Ward ${client.permanent_address.ward_no}`, client.permanent_address.district, client.permanent_address.province].filter(Boolean).join(', ') }}
                </div>

                <Link
                    :href="`/admin/clients/${client.client_id}/edit`"
                    class="mt-4 block rounded-md border px-4 py-2 text-center text-sm text-gray-600 hover:bg-gray-50"
                >
                    Edit Client
                </Link>
            </div>

            <!-- Right panel -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Properties -->
                <div class="rounded-xl bg-white p-5 shadow">
                    <h3 class="mb-3 font-semibold text-gray-700">Properties ({{ client.properties.length }})</h3>
                    <div v-if="client.properties.length > 0" class="space-y-2">
                        <div
                            v-for="p in client.properties"
                            :key="p.property_id"
                            class="flex items-center justify-between rounded-md border px-3 py-2 text-sm"
                        >
                            <span class="font-mono text-xs text-gray-500">{{ p.property_code }}</span>
                            <span class="capitalize">{{ p.property_type.replace(/_/g, ' ') }}</span>
                            <span class="capitalize text-gray-500">{{ p.status }}</span>
                            <Link :href="`/admin/properties/${p.property_id}`" class="text-blue-600 hover:underline">View</Link>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-400">No properties registered.</p>
                </div>

                <!-- Documents -->
                <div class="rounded-xl bg-white p-5 shadow">
                    <h3 class="mb-3 font-semibold text-gray-700">Documents ({{ client.documents.length }})</h3>
                    <div v-if="client.documents.length > 0" class="space-y-2">
                        <div
                            v-for="d in client.documents"
                            :key="d.doc_id"
                            class="flex items-center justify-between text-sm"
                        >
                            <span>{{ d.docType?.doc_name }}</span>
                            <a :href="`/storage/${d.file_path}`" target="_blank" class="text-blue-600 hover:underline">Download</a>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-400">No documents uploaded.</p>
                </div>

                <!-- Service requests -->
                <div class="rounded-xl bg-white p-5 shadow">
                    <h3 class="mb-3 font-semibold text-gray-700">Service Requests ({{ client.service_requests.length }})</h3>
                    <div v-if="client.service_requests.length > 0" class="space-y-2">
                        <div
                            v-for="r in client.service_requests"
                            :key="r.request_id"
                            class="flex items-center justify-between rounded-md border px-3 py-2 text-sm"
                        >
                            <span>{{ r.serviceType?.service_name }}</span>
                            <span class="capitalize text-gray-500">{{ r.status }}</span>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-400">No service requests.</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
