<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface ValuationReport {
    report_id: number;
    report_no: string;
    valuation_type: string;
    valuated_amount: number;
    approval_status: string;
    issued_date: string | null;
    valuator?: { full_name: string };
    approved_by?: { full_name: string };
}

interface ValuationRequest {
    request_id: number;
    request_code: string;
    purpose_of_valuation: string | null;
    requested_valuation_type: string | null;
    preferred_visit_date: string | null;
    field_visit_date: string | null;
    site_contact_person_name: string | null;
    site_contact_mobile: string | null;
    status: string;
    remarks: string | null;
    created_at: string;
    client?: { client_id: number; full_name: string; mobile_no: string };
    property?: { property_id: number; property_code: string; property_type: string; kitta_no: string | null; area: string | null; address?: { municipality: string | null; district: string | null } };
    assigned_valuator?: { full_name: string };
    reports: ValuationReport[];
}

const props = defineProps<{
    valuation: ValuationRequest;
    valuators: { staff_id: number; full_name: string }[];
}>();

const statusForm = useForm({
    status: props.valuation.status,
    assigned_valuator_staff_id: props.valuation.assigned_valuator?.full_name ? '' : '',
    field_visit_date: props.valuation.field_visit_date ?? '',
    remarks: props.valuation.remarks ?? '',
});

const reportForm = useForm({
    valuation_type: 'market_value',
    valuated_amount: '',
    rate_basis: '',
    valuator_staff_id: '',
    issued_date: '',
});

function updateStatus() {
    statusForm.patch(`/admin/valuations/${props.valuation.request_id}/status`);
}

function submitReport() {
    reportForm.post(`/admin/valuations/${props.valuation.request_id}/reports`);
}

const statusClass: Record<string, string> = {
    received: 'bg-blue-100 text-blue-700',
    in_progress: 'bg-orange-100 text-orange-700',
    report_issued: 'bg-green-100 text-green-700',
    cancelled: 'bg-red-100 text-red-600',
};

const approvalClass: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-600',
    pending_approval: 'bg-yellow-100 text-yellow-700',
    approved: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-600',
};
</script>

<template>
    <Head :title="valuation.request_code" />

    <AdminLayout>
        <template #title>Valuation: {{ valuation.request_code }}</template>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Summary -->
            <div class="space-y-4 lg:col-span-1">
                <div class="rounded-xl bg-white p-5 shadow">
                    <div class="mb-3 flex items-center justify-between">
                        <span class="font-mono text-xs text-gray-400">{{ valuation.request_code }}</span>
                        <span :class="statusClass[valuation.status] ?? 'bg-gray-100 text-gray-600'" class="rounded-full px-2 py-0.5 text-xs font-medium">
                            {{ valuation.status.replace(/_/g, ' ') }}
                        </span>
                    </div>

                    <h3 class="mb-3 font-semibold text-gray-700">Client</h3>
                    <p class="font-medium">{{ valuation.client?.full_name }}</p>
                    <p class="text-sm text-gray-500">{{ valuation.client?.mobile_no }}</p>

                    <hr class="my-3" />

                    <h3 class="mb-3 font-semibold text-gray-700">Property</h3>
                    <p class="font-mono text-sm text-gray-500">{{ valuation.property?.property_code }}</p>
                    <p class="capitalize text-sm">{{ valuation.property?.property_type.replace(/_/g, ' ') }}</p>
                    <p class="text-xs text-gray-400">{{ [valuation.property?.address?.municipality, valuation.property?.address?.district].filter(Boolean).join(', ') }}</p>
                    <dl class="mt-2 space-y-1 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">Kitta</dt><dd>{{ valuation.property?.kitta_no ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Area</dt><dd>{{ valuation.property?.area ?? '—' }}</dd></div>
                    </dl>

                    <hr class="my-3" />
                    <dl class="space-y-1 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">Purpose</dt><dd class="capitalize text-xs">{{ valuation.purpose_of_valuation?.replace(/_/g, ' ') ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Type Requested</dt><dd class="capitalize text-xs">{{ valuation.requested_valuation_type?.replace(/_/g, ' ') ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Preferred Visit</dt><dd>{{ valuation.preferred_visit_date ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Field Visit</dt><dd>{{ valuation.field_visit_date ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Assigned To</dt><dd>{{ valuation.assigned_valuator?.full_name ?? 'Unassigned' }}</dd></div>
                    </dl>
                </div>

                <!-- Update status -->
                <div class="rounded-xl bg-white p-5 shadow">
                    <h3 class="mb-3 font-semibold text-gray-700">Update Status</h3>
                    <form @submit.prevent="updateStatus" class="space-y-3">
                        <select v-model="statusForm.status" class="w-full rounded border px-3 py-2 text-sm">
                            <option value="received">Received</option>
                            <option value="site_visit_scheduled">Site Visit Scheduled</option>
                            <option value="in_progress">In Progress</option>
                            <option value="report_issued">Report Issued</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <select v-model="statusForm.assigned_valuator_staff_id" class="w-full rounded border px-3 py-2 text-sm">
                            <option value="">Assign valuator…</option>
                            <option v-for="s in valuators" :key="s.staff_id" :value="s.staff_id">{{ s.full_name }}</option>
                        </select>
                        <input v-model="statusForm.field_visit_date" type="date" class="w-full rounded border px-3 py-2 text-sm" placeholder="Field visit date" />
                        <button type="submit" :disabled="statusForm.processing" class="w-full rounded-md bg-slate-700 py-2 text-sm text-white hover:bg-slate-800 disabled:opacity-60">
                            Update
                        </button>
                    </form>
                </div>
            </div>

            <!-- Reports -->
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-xl bg-white p-5 shadow">
                    <h3 class="mb-4 font-semibold text-gray-700">Valuation Reports ({{ valuation.reports.length }})</h3>

                    <div v-if="valuation.reports.length > 0" class="mb-6 space-y-3">
                        <div v-for="r in valuation.reports" :key="r.report_id" class="rounded-md border p-4">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-sm font-medium">{{ r.report_no }}</span>
                                <span :class="approvalClass[r.approval_status] ?? 'bg-gray-100 text-gray-600'" class="rounded-full px-2 py-0.5 text-xs font-medium capitalize">
                                    {{ r.approval_status.replace(/_/g, ' ') }}
                                </span>
                            </div>
                            <dl class="mt-3 grid grid-cols-2 gap-2 text-sm">
                                <div><dt class="text-gray-500 text-xs">Type</dt><dd class="capitalize font-medium">{{ r.valuation_type.replace(/_/g, ' ') }}</dd></div>
                                <div><dt class="text-gray-500 text-xs">Amount (NPR)</dt><dd class="font-bold text-green-700">{{ Number(r.valuated_amount).toLocaleString() }}</dd></div>
                                <div><dt class="text-gray-500 text-xs">Valuator</dt><dd>{{ r.valuator?.full_name ?? '—' }}</dd></div>
                                <div><dt class="text-gray-500 text-xs">Issued Date</dt><dd>{{ r.issued_date ?? '—' }}</dd></div>
                            </dl>
                        </div>
                    </div>

                    <!-- Add report form -->
                    <h4 class="mb-3 font-medium text-gray-600">Add Valuation Report</h4>
                    <form @submit.prevent="submitReport" class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">Valuation Type *</label>
                                <select v-model="reportForm.valuation_type" class="w-full rounded border px-3 py-2 text-sm">
                                    <option value="market_value">Market Value</option>
                                    <option value="forced_sale_value">Forced Sale Value</option>
                                    <option value="mortgage_valuation">Mortgage Valuation</option>
                                    <option value="fair_value">Fair Value</option>
                                    <option value="insurance_value">Insurance Value</option>
                                    <option value="investment_value">Investment Value</option>
                                    <option value="rental_value">Rental Value</option>
                                    <option value="government_valuation">Government Valuation</option>
                                    <option value="asset_valuation">Asset Valuation</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">Valuated Amount (NPR) *</label>
                                <input v-model="reportForm.valuated_amount" type="number" step="0.01" class="w-full rounded border px-3 py-2 text-sm" placeholder="e.g. 5000000" />
                                <p v-if="reportForm.errors.valuated_amount" class="mt-1 text-xs text-red-600">{{ reportForm.errors.valuated_amount }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">Valuator</label>
                                <select v-model="reportForm.valuator_staff_id" class="w-full rounded border px-3 py-2 text-sm">
                                    <option value="">Select…</option>
                                    <option v-for="s in valuators" :key="s.staff_id" :value="s.staff_id">{{ s.full_name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">Issued Date</label>
                                <input v-model="reportForm.issued_date" type="date" class="w-full rounded border px-3 py-2 text-sm" />
                            </div>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Rate Basis / Notes</label>
                            <textarea v-model="reportForm.rate_basis" rows="2" class="w-full rounded border px-3 py-2 text-sm" placeholder="Land rate, building rate, formula used…" />
                        </div>
                        <button type="submit" :disabled="reportForm.processing" class="rounded-md bg-green-700 px-5 py-2 text-sm text-white hover:bg-green-800 disabled:opacity-60">
                            Issue Report
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
