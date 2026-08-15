<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Company {
    company_id?: number;
    company_name: string;
    registration_no?: string;
    broker_licence_no?: string;
    land_survey_licence_no?: string;
    pan_vat_no?: string;
    contact_no?: string;
    email?: string;
    website?: string;
    tagline?: string;
    licence_expiry_date?: string;
    logo_url?: string;
}

const props = defineProps<{ company: Company }>();

const form = useForm({
    company_name: props.company.company_name ?? '',
    registration_no: props.company.registration_no ?? '',
    broker_licence_no: props.company.broker_licence_no ?? '',
    land_survey_licence_no: props.company.land_survey_licence_no ?? '',
    pan_vat_no: props.company.pan_vat_no ?? '',
    contact_no: props.company.contact_no ?? '',
    email: props.company.email ?? '',
    website: props.company.website ?? '',
    tagline: props.company.tagline ?? '',
    licence_expiry_date: props.company.licence_expiry_date ?? '',
    logo: null as File | null,
});

const logoPreview = ref<string | null>(props.company.logo_url ?? null);

function onLogoChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
}

function submit() {
    form.post('/admin/settings', {
        forceFormData: true,
    });
}
</script>

<template>
    <AdminLayout>
        <template #title>Settings</template>

        <div class="mx-auto max-w-3xl space-y-6">
            <div class="rounded-lg bg-white p-6 shadow">
                <h2 class="mb-6 text-xl font-semibold text-gray-800">Company Settings</h2>

                <form class="space-y-6" @submit.prevent="submit">
                    <!-- Logo Upload -->
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Company Logo</label>
                        <div class="flex items-center gap-4">
                            <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                                <img v-if="logoPreview" :src="logoPreview" alt="Logo preview" class="h-full w-full object-contain" />
                                <span v-else class="text-3xl text-gray-300">🏢</span>
                            </div>
                            <div>
                                <input
                                    id="logo"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="onLogoChange"
                                />
                                <label
                                    for="logo"
                                    class="cursor-pointer rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                >
                                    Choose Logo
                                </label>
                                <p class="mt-1 text-xs text-gray-400">PNG, JPG, SVG — max 2 MB</p>
                                <p v-if="form.errors.logo" class="mt-1 text-xs text-red-500">{{ form.errors.logo }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Company Name & Tagline -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Company Name <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.company_name"
                                type="text"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                            />
                            <p v-if="form.errors.company_name" class="mt-1 text-xs text-red-500">{{ form.errors.company_name }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Tagline</label>
                            <input
                                v-model="form.tagline"
                                type="text"
                                placeholder="Reliable Realty Solution"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                            />
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Contact No.</label>
                            <input
                                v-model="form.contact_no"
                                type="text"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Website</label>
                        <input
                            v-model="form.website"
                            type="url"
                            placeholder="https://example.com"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                        />
                        <p v-if="form.errors.website" class="mt-1 text-xs text-red-500">{{ form.errors.website }}</p>
                    </div>

                    <!-- Registration Details -->
                    <div class="border-t border-gray-100 pt-4">
                        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Regulatory Details</h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Registration No.</label>
                                <input
                                    v-model="form.registration_no"
                                    type="text"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">PAN / VAT No.</label>
                                <input
                                    v-model="form.pan_vat_no"
                                    type="text"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Broker Licence No.</label>
                                <input
                                    v-model="form.broker_licence_no"
                                    type="text"
                                    placeholder="e.g. BRK1835451"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Land Survey Licence No.</label>
                                <input
                                    v-model="form.land_survey_licence_no"
                                    type="text"
                                    placeholder="e.g. BRS1873551"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Licence Expiry Date</label>
                                <input
                                    v-model="form.licence_expiry_date"
                                    type="date"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-gray-100 pt-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-md bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-60"
                        >
                            {{ form.processing ? 'Saving…' : 'Save Settings' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
