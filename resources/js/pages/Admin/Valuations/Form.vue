<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

const props = defineProps<{
    clients: { client_id: number; full_name: string; client_code: string }[];
    properties: { property_id: number; property_code: string; property_type: string; owner_client_id: number; owner?: { full_name: string } }[];
    valuators: { staff_id: number; full_name: string }[];
}>();

const form = useForm({
    client_id: '',
    property_id: '',
    purpose_of_valuation: '',
    requested_valuation_type: '',
    preferred_visit_date: '',
    site_contact_person_name: '',
    site_contact_mobile: '',
    assigned_valuator_staff_id: '',
    remarks: '',
});

function submit() {
    form.post('/admin/valuations');
}
</script>

<template>
    <Head title="New Valuation Request" />

    <AdminLayout>
        <template #title>New Valuation Request</template>

        <div class="max-w-2xl rounded-xl bg-white p-6 shadow">
            <form @submit.prevent="submit" class="space-y-4">
                <!-- Client -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Client *</label>
                    <select v-model="form.client_id" class="w-full rounded border px-3 py-2 text-sm">
                        <option value="">Select client…</option>
                        <option v-for="c in clients" :key="c.client_id" :value="c.client_id">{{ c.full_name }} ({{ c.client_code }})</option>
                    </select>
                    <p v-if="form.errors.client_id" class="mt-1 text-xs text-red-600">{{ form.errors.client_id }}</p>
                </div>

                <!-- Property -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Property *</label>
                    <select v-model="form.property_id" class="w-full rounded border px-3 py-2 text-sm">
                        <option value="">Select property…</option>
                        <option v-for="p in properties" :key="p.property_id" :value="p.property_id">
                            {{ p.property_code }} — {{ p.property_type.replace(/_/g, ' ') }} ({{ p.owner?.full_name }})
                        </option>
                    </select>
                    <p v-if="form.errors.property_id" class="mt-1 text-xs text-red-600">{{ form.errors.property_id }}</p>
                </div>

                <!-- Purpose + Type -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Purpose of Valuation</label>
                        <select v-model="form.purpose_of_valuation" class="w-full rounded border px-3 py-2 text-sm">
                            <option value="">Select…</option>
                            <option value="bank_loan_mortgage">Bank Loan / Mortgage</option>
                            <option value="buying_selling">Buying / Selling</option>
                            <option value="insurance">Insurance</option>
                            <option value="legal">Legal</option>
                            <option value="investment_decision">Investment Decision</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Valuation Type Requested</label>
                        <select v-model="form.requested_valuation_type" class="w-full rounded border px-3 py-2 text-sm">
                            <option value="">Select…</option>
                            <option value="market_value">Market Value</option>
                            <option value="forced_sale_value">Forced Sale Value</option>
                            <option value="government_value_reference">Government Value</option>
                            <option value="rental_value">Rental Value</option>
                        </select>
                    </div>
                </div>

                <!-- Site visit -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Preferred Visit Date</label>
                        <input v-model="form.preferred_visit_date" type="date" class="w-full rounded border px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Assigned Valuator</label>
                        <select v-model="form.assigned_valuator_staff_id" class="w-full rounded border px-3 py-2 text-sm">
                            <option value="">Unassigned</option>
                            <option v-for="s in valuators" :key="s.staff_id" :value="s.staff_id">{{ s.full_name }}</option>
                        </select>
                    </div>
                </div>

                <!-- Site contact -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Site Contact Person</label>
                        <input v-model="form.site_contact_person_name" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Site Contact Mobile</label>
                        <input v-model="form.site_contact_mobile" type="text" class="w-full rounded border px-3 py-2 text-sm" />
                    </div>
                </div>

                <!-- Remarks -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Remarks</label>
                    <textarea v-model="form.remarks" rows="3" class="w-full rounded border px-3 py-2 text-sm" />
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="form.processing" class="rounded-md bg-slate-700 px-5 py-2 text-sm text-white hover:bg-slate-800 disabled:opacity-60">
                        Submit Request
                    </button>
                    <a href="/admin/valuations" class="rounded-md border px-5 py-2 text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
