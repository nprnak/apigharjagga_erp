<template>
    <div
        class="min-h-screen bg-slate-50 font-sans text-slate-800 selection:bg-emerald-500 selection:text-white"
    >
        <div
            v-if="submitted"
            class="flex min-h-screen items-center justify-center p-4"
        >
            <div
                class="animate-in fade-in zoom-in w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-[0_20px_50px_-12px_rgba(0,0,0,0.1)] duration-500"
            >
                <div
                    class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-emerald-700 px-8 py-12 text-center"
                >
                    <div
                        class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"
                    ></div>
                    <div
                        class="relative z-10 mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-white/20 shadow-inner backdrop-blur-md"
                    >
                        <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="relative z-10 mb-2 text-3xl font-extrabold tracking-tight text-white">
                        Client Registered
                    </h2>
                    <p class="relative z-10 text-lg font-medium text-emerald-100">
                        ग्राहक दर्ता सफलतापूर्वक भयो
                    </p>
                </div>
                <div class="px-8 py-10 text-center">
                    <p class="mb-3 text-xs font-bold tracking-[0.2em] text-slate-400 uppercase">
                        Client ID
                    </p>
                    <div class="mb-8 inline-block rounded-2xl border border-slate-100 bg-slate-50 px-6 py-4 shadow-sm">
                        <p class="font-mono text-3xl font-black tracking-widest text-slate-800">
                            {{ clientCode }}
                        </p>
                    </div>
                    <p class="mx-auto mb-10 max-w-sm text-sm leading-relaxed text-slate-500">
                        The client has been added to the database. Download the
                        official registration copy below.
                    </p>
                    <div class="flex flex-col gap-4">
                        <a
                            :href="`/client-registration/${clientId}/pdf`"
                            target="_blank"
                            class="group relative inline-flex items-center justify-center gap-3 overflow-hidden rounded-2xl bg-slate-900 px-8 py-4 font-semibold text-white shadow-xl shadow-slate-900/20 transition-all duration-300 hover:-translate-y-0.5 hover:bg-slate-800"
                        >
                            <span class="relative z-10">Download PDF Document</span>
                        </a>
                        <button
                            @click="resetForm"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-8 py-4 font-semibold text-slate-600 hover:bg-slate-50"
                        >
                            Register Another Client
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="mx-auto max-w-4xl px-4 py-12 sm:px-6">
            <div class="mb-12 text-center">
                <div
                    class="mb-6 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-1.5 text-[10px] font-semibold tracking-widest text-slate-500 uppercase shadow-sm"
                >
                    AGJ-FRM-07
                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                    Version 1.0
                </div>
                <h1 class="mb-4 text-4xl font-extrabold tracking-tight text-slate-900 md:text-5xl">
                    Client Registration Form
                </h1>
                <p class="text-lg font-medium text-slate-500">ग्राहक दर्ता फारम</p>
                <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-400">
                    Project / Service: Api Ghar Jagga Property Listing, Verification &amp; Valuation Service
                </p>
            </div>

            <div class="relative mx-auto mb-12 max-w-3xl">
                <div class="absolute top-1/2 left-0 z-0 h-1 w-full -translate-y-1/2 rounded-full bg-slate-200"></div>
                <div
                    class="absolute top-1/2 left-0 z-0 h-1 -translate-y-1/2 rounded-full bg-emerald-500 transition-all duration-500"
                    :style="{ width: progressPercentage + '%' }"
                ></div>
                <div class="relative z-10 flex justify-between">
                    <div v-for="(step, index) in steps" :key="index" class="flex flex-col items-center gap-2">
                        <button
                            @click="goToStep(index)"
                            :disabled="index > highestStepReached"
                            class="flex h-10 w-10 items-center justify-center rounded-full border-[3px] text-sm font-bold transition-all"
                            :class="[
                                currentStep === index
                                    ? 'border-emerald-200 bg-emerald-500 text-white'
                                    : index < currentStep
                                      ? 'cursor-pointer border-emerald-500 bg-emerald-500 text-white'
                                      : index <= highestStepReached
                                        ? 'cursor-pointer border-slate-300 bg-white text-slate-500'
                                        : 'cursor-not-allowed border-slate-200 bg-slate-50 text-slate-400',
                            ]"
                        >
                            <span v-if="index >= currentStep">{{ index + 1 }}</span>
                            <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                        <span
                            class="hidden text-[10px] font-bold tracking-wider uppercase sm:block"
                            :class="currentStep >= index ? 'text-slate-800' : 'text-slate-400'"
                        >
                            {{ step.title }}
                        </span>
                    </div>
                </div>
            </div>

            <div
                v-if="globalError"
                class="mb-8 rounded-2xl border border-red-200 bg-red-50 p-5"
            >
                <p class="mb-2 text-sm font-bold text-red-800">Please resolve the following errors:</p>
                <ul class="space-y-1">
                    <li v-for="(msg, field) in stepErrors" :key="field" class="text-sm font-medium text-red-600">
                        {{ msg }}
                    </li>
                </ul>
            </div>

            <form @submit.prevent="handleNextOrSubmit" novalidate>
                <transition name="fade-slide" mode="out-in">
                    <!-- STEP 1: TYPE + PERSONAL -->
                    <div
                        v-if="currentStep === 0"
                        key="s1"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">Client Type &amp; Personal Info</h2>
                            <p class="mb-8 text-sm text-slate-500">ग्राहकको प्रकार तथा व्यक्तिगत विवरण</p>

                            <FormField label="Client Type" required :error="errors.client_type" class="mb-8">
                                <div class="mt-2 grid grid-cols-2 gap-3 md:grid-cols-3">
                                    <label v-for="opt in clientTypes" :key="opt.value" class="cursor-pointer">
                                        <input type="radio" :value="opt.value" v-model="form.client_type" class="peer sr-only" />
                                        <span class="block rounded-2xl border-2 border-slate-200 px-3 py-4 text-center text-sm font-bold text-slate-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50">
                                            {{ opt.label }}
                                        </span>
                                    </label>
                                </div>
                                <input
                                    v-if="form.client_type === 'other'"
                                    v-model="form.client_type_other"
                                    type="text"
                                    placeholder="Please specify"
                                    :class="inputClass(errors.client_type_other) + ' mt-3'"
                                />
                            </FormField>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Full Name" required :error="errors.full_name">
                                    <input v-model="form.full_name" type="text" :class="inputClass(errors.full_name)" @blur="validateField('full_name')" />
                                </FormField>
                                <FormField label="Father / Mother Name" :error="errors.father_mother_name">
                                    <input v-model="form.father_mother_name" type="text" :class="inputClass(errors.father_mother_name)" />
                                </FormField>
                                <FormField label="Spouse Name" :error="errors.spouse_name">
                                    <input v-model="form.spouse_name" type="text" :class="inputClass(errors.spouse_name)" />
                                </FormField>
                                <FormField label="Citizenship No." :error="errors.citizenship_no">
                                    <input v-model="form.citizenship_no" type="text" :class="inputClass(errors.citizenship_no)" />
                                </FormField>
                                <FormField label="Nationality" :error="errors.nationality">
                                    <input v-model="form.nationality" type="text" :class="inputClass(errors.nationality)" />
                                </FormField>
                                <FormField label="Date of Birth" :error="errors.date_of_birth">
                                    <input v-model="form.date_of_birth" type="date" :max="today" :class="inputClass(errors.date_of_birth)" />
                                </FormField>
                                <FormField label="Gender" :error="errors.gender">
                                    <select v-model="form.gender" :class="inputClass(errors.gender)">
                                        <option value="">Select</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </FormField>
                                <FormField label="Occupation" :error="errors.occupation">
                                    <input v-model="form.occupation" type="text" :class="inputClass(errors.occupation)" />
                                </FormField>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: CONTACT -->
                    <div
                        v-else-if="currentStep === 1"
                        key="s2"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">Contact Details</h2>
                            <p class="mb-8 text-sm text-slate-500">सम्पर्क विवरण</p>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Mobile No." required hint="10 digits starting with 9" :error="errors.mobile_no">
                                    <input
                                        v-model="form.mobile_no"
                                        type="tel"
                                        placeholder="98XXXXXXXX"
                                        :class="inputClass(errors.mobile_no)"
                                        @blur="validateField('mobile_no')"
                                        @input="form.mobile_no = form.mobile_no.replace(/\D/g, '').slice(0, 10)"
                                    />
                                </FormField>
                                <FormField label="Alternate Contact No." hint="10 digits starting with 9" :error="errors.alt_contact_no">
                                    <input
                                        v-model="form.alt_contact_no"
                                        type="tel"
                                        :class="inputClass(errors.alt_contact_no)"
                                        @input="form.alt_contact_no = form.alt_contact_no.replace(/\D/g, '').slice(0, 10)"
                                    />
                                </FormField>
                                <FormField label="Email Address" :error="errors.email" class="md:col-span-2">
                                    <input v-model="form.email" type="email" :class="inputClass(errors.email)" />
                                </FormField>
                                <FormField label="Permanent Address" required :error="errors.permanent_address">
                                    <textarea v-model="form.permanent_address" rows="3" :class="inputClass(errors.permanent_address) + ' resize-none'" @blur="validateField('permanent_address')" />
                                </FormField>
                                <FormField label="Current Address" :error="errors.current_address">
                                    <textarea v-model="form.current_address" rows="3" :class="inputClass(errors.current_address) + ' resize-none'" />
                                </FormField>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: ORG + SERVICES -->
                    <div
                        v-else-if="currentStep === 2"
                        key="s3"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">Organization &amp; Services</h2>
                            <p class="mb-8 text-sm text-slate-500">संस्था विवरण (लागू भएमा) तथा आवश्यक सेवा छनोट</p>

                            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Organization Name" :error="errors.organization_name">
                                    <input v-model="form.organization_name" type="text" :class="inputClass(errors.organization_name)" />
                                </FormField>
                                <FormField label="Registration No." :error="errors.registration_no">
                                    <input v-model="form.registration_no" type="text" :class="inputClass(errors.registration_no)" />
                                </FormField>
                                <FormField label="PAN / VAT No." :error="errors.pan_vat_no">
                                    <input v-model="form.pan_vat_no" type="text" :class="inputClass(errors.pan_vat_no)" />
                                </FormField>
                                <FormField label="Authorized Person" :error="errors.authorized_person">
                                    <input v-model="form.authorized_person" type="text" :class="inputClass(errors.authorized_person)" />
                                </FormField>
                                <FormField label="Designation" :error="errors.designation">
                                    <input v-model="form.designation" type="text" :class="inputClass(errors.designation)" />
                                </FormField>
                                <FormField label="Office Address" :error="errors.office_address">
                                    <input v-model="form.office_address" type="text" :class="inputClass(errors.office_address)" />
                                </FormField>
                            </div>

                            <FormField label="Required Service Selection" required :error="errors.requested_services">
                                <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <label
                                        v-for="svc in serviceOptions"
                                        :key="svc.value"
                                        class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3.5 hover:bg-slate-50 [&:has(input:checked)]:border-emerald-500 [&:has(input:checked)]:bg-emerald-50/30"
                                    >
                                        <input type="checkbox" :value="svc.value" v-model="form.requested_services" class="h-5 w-5 rounded border-slate-300 text-emerald-600" />
                                        <span class="text-sm font-medium text-slate-700">{{ svc.label }}</span>
                                    </label>
                                </div>
                            </FormField>
                        </div>
                    </div>

                    <!-- STEP 4: PROPERTY REQUIREMENT + OWNER DETAILS -->
                    <div
                        v-else-if="currentStep === 3"
                        key="s4"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">Property Details</h2>
                            <p class="mb-8 text-sm text-slate-500">आवश्यकता (खरिदकर्ता/लगानीकर्ता/भाडावाला) र धनी विवरण</p>

                            <h3 class="mb-4 text-base font-bold text-slate-800">Property Requirement / सम्पत्ति आवश्यकता</h3>
                            <p class="mb-4 text-xs text-slate-400">For Buyer / Investor / Tenant</p>
                            <FormField label="Purpose" :error="errors.req_purpose" class="mb-6">
                                <div class="mt-2 flex flex-wrap gap-3">
                                    <label v-for="opt in purposes" :key="opt.value" class="cursor-pointer">
                                        <input type="radio" :value="opt.value" v-model="form.req_purpose" class="peer sr-only" />
                                        <span class="inline-block rounded-full border border-slate-200 px-5 py-2.5 text-sm font-semibold peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white">
                                            {{ opt.label }}
                                        </span>
                                    </label>
                                </div>
                            </FormField>
                            <FormField label="Property Type" :error="errors.req_property_type" class="mb-6">
                                <div class="mt-2 flex flex-wrap gap-3">
                                    <label v-for="opt in reqPropertyTypes" :key="opt.value" class="cursor-pointer">
                                        <input type="radio" :value="opt.value" v-model="form.req_property_type" class="peer sr-only" />
                                        <span class="inline-block rounded-full border border-slate-200 px-5 py-2.5 text-sm font-semibold peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white">
                                            {{ opt.label }}
                                        </span>
                                    </label>
                                </div>
                            </FormField>
                            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Preferred Location" :error="errors.req_preferred_location">
                                    <input v-model="form.req_preferred_location" type="text" :class="inputClass(errors.req_preferred_location)" />
                                </FormField>
                                <FormField label="Required Area" :error="errors.req_required_area">
                                    <input v-model="form.req_required_area" type="text" :class="inputClass(errors.req_required_area)" />
                                </FormField>
                                <FormField label="Estimated Budget" :error="errors.req_estimated_budget">
                                    <input v-model.number="form.req_estimated_budget" type="number" min="0" :class="inputClass(errors.req_estimated_budget)" />
                                </FormField>
                                <FormField label="Purchase Timeline" :error="errors.req_purchase_timeline">
                                    <input v-model="form.req_purchase_timeline" type="text" placeholder="e.g. Within 3 months" :class="inputClass(errors.req_purchase_timeline)" />
                                </FormField>
                            </div>

                            <h3 class="mb-2 border-t border-slate-100 pt-8 text-base font-bold text-slate-800">Property Owner Details / सम्पत्ति धनी विवरण</h3>
                            <p class="mb-4 text-xs text-slate-400">For Property Listing Clients</p>
                            <FormField label="Property Available For" :error="errors.available_for" class="mb-6">
                                <div class="mt-2 flex flex-wrap gap-3">
                                    <label v-for="opt in availableOptions" :key="opt.value" class="flex cursor-pointer items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold [&:has(input:checked)]:border-slate-900 [&:has(input:checked)]:bg-slate-900 [&:has(input:checked)]:text-white">
                                        <input type="checkbox" :value="opt.value" v-model="form.available_for" class="sr-only" />
                                        {{ opt.label }}
                                    </label>
                                </div>
                            </FormField>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Property Location" :error="errors.property_location">
                                    <input v-model="form.property_location" type="text" :class="inputClass(errors.property_location)" />
                                </FormField>
                                <FormField label="Kitta No." :error="errors.kitta_no">
                                    <input v-model="form.kitta_no" type="text" :class="inputClass(errors.kitta_no)" />
                                </FormField>
                                <FormField label="Land Area" :error="errors.land_area">
                                    <input v-model="form.land_area" type="text" :class="inputClass(errors.land_area)" />
                                </FormField>
                                <FormField label="Expected Price" :error="errors.expected_price">
                                    <input v-model.number="form.expected_price" type="number" min="0" :class="inputClass(errors.expected_price)" />
                                </FormField>
                                <div class="md:col-span-2">
                                    <FormField label="Building Details" :error="errors.building_details">
                                        <textarea v-model="form.building_details" rows="3" :class="inputClass(errors.building_details) + ' resize-none'" />
                                    </FormField>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 5: DOCUMENTS -->
                    <div
                        v-else-if="currentStep === 4"
                        key="s5"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">Document Submission Checklist</h2>
                            <p class="mb-8 text-sm text-slate-500">कागजात बुझाउने चेकलिस्ट — Submitted or Pending</p>
                            <div class="space-y-4">
                                <div
                                    v-for="doc in documentOptions"
                                    :key="doc.value"
                                    class="flex flex-col gap-3 rounded-2xl border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between"
                                >
                                    <span class="text-sm font-bold text-slate-700">{{ doc.label }}</span>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 text-sm">
                                            <input type="radio" :name="'doc-' + doc.value" value="submitted" v-model="form.document_status[doc.value]" class="text-emerald-600" />
                                            Submitted
                                        </label>
                                        <label class="flex items-center gap-2 text-sm">
                                            <input type="radio" :name="'doc-' + doc.value" value="pending" v-model="form.document_status[doc.value]" class="text-emerald-600" />
                                            Pending
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6">
                                <FormField label="Other Documents (specify)" :error="errors.other_documents_note">
                                    <input v-model="form.other_documents_note" type="text" :class="inputClass(errors.other_documents_note)" />
                                </FormField>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 6: DIGITAL + DECLARATION -->
                    <div
                        v-else-if="currentStep === 5"
                        key="s6"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">Digital Registration &amp; Declaration</h2>
                            <p class="mb-8 text-sm text-slate-500">डिजिटल दर्ता विवरण तथा ग्राहक घोषणा</p>

                            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Client ID" hint="Assigned automatically after save">
                                    <input type="text" value="Auto-generated (CLT-YYYYMMDD-####)" disabled class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-medium text-slate-500" />
                                </FormField>
                                <FormField label="Registration Date" required :error="errors.registration_date">
                                    <input v-model="form.registration_date" type="date" :max="today" :class="inputClass(errors.registration_date)" />
                                </FormField>
                                <FormField label="Mobile App User ID" :error="errors.mobile_app_user_id">
                                    <input v-model="form.mobile_app_user_id" type="text" :class="inputClass(errors.mobile_app_user_id)" />
                                </FormField>
                                <FormField label="MIS Entry Status" :error="errors.mis_entry_status">
                                    <select v-model="form.mis_entry_status" :class="inputClass(errors.mis_entry_status)">
                                        <option value="pending">Pending</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </FormField>
                            </div>

                            <h3 class="mb-4 text-base font-bold text-slate-800">Registered By / दर्ता गर्ने कर्मचारी</h3>
                            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Name" :error="errors.registered_by_name">
                                    <input v-model="form.registered_by_name" type="text" :class="inputClass(errors.registered_by_name)" />
                                </FormField>
                                <FormField label="Designation" :error="errors.registered_by_designation">
                                    <input v-model="form.registered_by_designation" type="text" :class="inputClass(errors.registered_by_designation)" />
                                </FormField>
                                <FormField label="Date" :error="errors.registered_by_date">
                                    <input v-model="form.registered_by_date" type="date" :max="today" :class="inputClass(errors.registered_by_date)" />
                                </FormField>
                                <SignatureUpload v-model="form.registered_by_signature" label="Staff Signature" :error="errors.registered_by_signature" />
                            </div>

                            <h3 class="mb-4 text-base font-bold text-slate-800">Approved By / स्वीकृत गर्ने</h3>
                            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Name" :error="errors.approved_by_name">
                                    <input v-model="form.approved_by_name" type="text" :class="inputClass(errors.approved_by_name)" />
                                </FormField>
                                <FormField label="Designation" :error="errors.approved_by_designation">
                                    <input v-model="form.approved_by_designation" type="text" :class="inputClass(errors.approved_by_designation)" />
                                </FormField>
                                <FormField label="Date" :error="errors.approved_by_date">
                                    <input v-model="form.approved_by_date" type="date" :max="today" :class="inputClass(errors.approved_by_date)" />
                                </FormField>
                                <SignatureUpload v-model="form.approved_by_signature" label="Approver Signature" :error="errors.approved_by_signature" />
                            </div>

                            <div class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 p-6 text-sm leading-relaxed text-slate-700">
                                I hereby confirm that the information provided in this registration form is true and correct. I authorize Api Ghar Jagga Pvt. Ltd. to verify, process, store and use the information for property-related services.
                                <p class="mt-3 text-xs text-slate-500 italic">म यसद्वारा घोषणा गर्दछु कि यस फारममा उपलब्ध गराइएको जानकारी सत्य र सही छ। म Api Ghar Jagga Pvt. Ltd. लाई सम्पत्ति सम्बन्धी सेवा प्रदान गर्ने प्रयोजनका लागि उक्त विवरण प्रमाणीकरण, प्रशोधन, अभिलेख तथा प्रयोग गर्न अनुमति दिन्छु।</p>
                            </div>
                            <label
                                class="mb-6 flex cursor-pointer items-start gap-4 rounded-2xl border p-4"
                                :class="errors.declaration_agreed ? 'border-red-300 bg-red-50' : 'border-slate-200'"
                            >
                                <input type="checkbox" v-model="form.declaration_agreed" class="mt-0.5 h-6 w-6 rounded border-slate-300 text-emerald-600" />
                                <span class="font-bold text-slate-800">I agree to the declaration / घोषणामा सहमत छु</span>
                            </label>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Client Name (Signature)" required :error="errors.client_signature_name">
                                    <input v-model="form.client_signature_name" type="text" :class="inputClass(errors.client_signature_name)" @blur="validateField('client_signature_name')" />
                                </FormField>
                                <FormField label="Date" required :error="errors.client_signature_date">
                                    <input v-model="form.client_signature_date" type="date" :max="today" :class="inputClass(errors.client_signature_date)" />
                                </FormField>
                                <div class="md:col-span-2">
                                    <SignatureUpload
                                        v-model="form.client_signature"
                                        label="Client Scanned Signature"
                                        required
                                        :error="errors.client_signature"
                                        @update:model-value="validateField('client_signature')"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>

                <div class="mt-8 flex items-center justify-between">
                    <button
                        v-if="currentStep > 0"
                        type="button"
                        @click="prevStep"
                        class="flex items-center gap-2 rounded-xl px-6 py-3.5 font-bold text-slate-500 hover:bg-white hover:text-slate-800"
                    >
                        Back
                    </button>
                    <div v-else></div>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="rounded-2xl px-10 py-3.5 font-bold text-white shadow-lg disabled:opacity-70"
                        :class="currentStep === steps.length - 1 ? 'bg-slate-900' : 'bg-emerald-500 shadow-emerald-500/30'"
                    >
                        {{ submitting ? 'Processing...' : currentStep === steps.length - 1 ? 'Submit Registration' : 'Continue' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, reactive, ref } from 'vue';
import axios from 'axios';
import FormField from '../../components/FormField.vue';
import SignatureUpload from '../../components/SignatureUpload.vue';

const emptyForm = () => ({
    client_type: '',
    client_type_other: '',
    full_name: '',
    father_mother_name: '',
    spouse_name: '',
    citizenship_no: '',
    nationality: 'Nepali',
    date_of_birth: '',
    gender: '',
    occupation: '',
    mobile_no: '',
    alt_contact_no: '',
    email: '',
    permanent_address: '',
    current_address: '',
    organization_name: '',
    registration_no: '',
    pan_vat_no: '',
    authorized_person: '',
    designation: '',
    office_address: '',
    req_purpose: '',
    req_property_type: '',
    req_preferred_location: '',
    req_required_area: '',
    req_estimated_budget: null as number | null,
    req_purchase_timeline: '',
    available_for: [] as string[],
    property_location: '',
    kitta_no: '',
    land_area: '',
    building_details: '',
    expected_price: null as number | null,
    requested_services: [] as string[],
    document_status: {
        citizenship_copy: 'pending',
        ownership_certificate: 'pending',
        land_house_documents: 'pending',
        passport_photo: 'pending',
        authorization_letter: 'pending',
        other_documents: 'pending',
    } as Record<string, string>,
    other_documents_note: '',
    registration_date: new Date().toISOString().split('T')[0],
    mobile_app_user_id: '',
    mis_entry_status: 'pending',
    registered_by_name: '',
    registered_by_designation: '',
    registered_by_date: '',
    registered_by_signature: null as File | null,
    approved_by_name: '',
    approved_by_designation: '',
    approved_by_date: '',
    approved_by_signature: null as File | null,
    declaration_agreed: false,
    client_signature_name: '',
    client_signature_date: new Date().toISOString().split('T')[0],
    client_signature: null as File | null,
});

const form = reactive(emptyForm());
const errors = reactive<Record<string, string>>({});
const steps = [
    { title: 'Personal', fields: ['client_type', 'client_type_other', 'full_name'] },
    { title: 'Contact', fields: ['mobile_no', 'permanent_address'] },
    { title: 'Services', fields: ['requested_services'] },
    { title: 'Property', fields: ['req_purpose', 'available_for'] },
    { title: 'Documents', fields: [] },
    {
        title: 'Sign',
        fields: ['registration_date', 'declaration_agreed', 'client_signature_name', 'client_signature'],
    },
];

const currentStep = ref(0);
const highestStepReached = ref(0);
const progressPercentage = computed(
    () => (currentStep.value / (steps.length - 1)) * 100,
);
const submitting = ref(false);
const submitted = ref(false);
const globalError = ref(false);
const stepErrors = reactive<Record<string, string>>({});
const clientCode = ref('');
const clientId = ref<number | null>(null);
const today = new Date().toISOString().split('T')[0];

const clientTypes = [
    { value: 'owner', label: 'Property Owner' },
    { value: 'buyer', label: 'Buyer' },
    { value: 'investor', label: 'Investor' },
    { value: 'tenant', label: 'Tenant' },
    { value: 'agent', label: 'Agent / Representative' },
    { value: 'other', label: 'Other' },
];
const purposes = [
    { value: 'purchase', label: 'Purchase' },
    { value: 'investment', label: 'Investment' },
    { value: 'rent', label: 'Rent' },
];
const reqPropertyTypes = [
    { value: 'land', label: 'Land' },
    { value: 'house', label: 'House' },
    { value: 'apartment', label: 'Apartment' },
    { value: 'commercial', label: 'Commercial' },
];
const availableOptions = [
    { value: 'sale', label: 'Sale' },
    { value: 'rent', label: 'Rent' },
    { value: 'lease', label: 'Lease' },
];
const serviceOptions = [
    { value: 'listing', label: 'Property Listing Service' },
    { value: 'verification', label: 'Property Verification Service' },
    { value: 'valuation', label: 'Property Valuation Service' },
    { value: 'digital_marketing', label: 'Digital Marketing Service' },
    { value: 'consultation', label: 'Property Consultation' },
    { value: 'documentation', label: 'Documentation Support' },
];
const documentOptions = [
    { value: 'citizenship_copy', label: 'Citizenship Copy' },
    { value: 'ownership_certificate', label: 'Ownership Certificate Copy (Lalpurja)' },
    { value: 'land_house_documents', label: 'Land / House Documents' },
    { value: 'passport_photo', label: 'Passport Size Photo' },
    { value: 'authorization_letter', label: 'Authorization Letter' },
    { value: 'other_documents', label: 'Other Documents' },
];

const inputClass = (error?: string) =>
    `w-full px-4 py-3 rounded-xl border text-slate-800 font-medium bg-white ${
        error
            ? 'border-red-300 bg-red-50'
            : 'border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10'
    }`;

const buyerTypes = ['buyer', 'investor', 'tenant'];
const rules: Record<string, (v: any) => string | true> = {
    client_type: (v) => (!v ? 'Please select a client type' : true),
    client_type_other: (v) =>
        form.client_type === 'other' && !v ? 'Please specify the client type' : true,
    full_name: (v) => (!v ? 'Full name is required' : true),
    mobile_no: (v) =>
        !v || !/^9[0-9]{9}$/.test(v)
            ? 'Enter a valid 10-digit mobile number starting with 9'
            : true,
    alt_contact_no: (v) =>
        !v || /^9[0-9]{9}$/.test(v)
            ? true
            : 'Alternate contact must be 10 digits starting with 9',
    permanent_address: (v) =>
        !v || v.trim().length < 8 ? 'Permanent address is required' : true,
    requested_services: (v) =>
        !v?.length ? 'Please select at least one service' : true,
    req_purpose: (v) =>
        buyerTypes.includes(form.client_type) && !v
            ? 'Purpose is required for buyer, investor or tenant'
            : true,
    available_for: (v) =>
        form.client_type === 'owner' && (!v || !v.length)
            ? 'Select what the property is available for'
            : true,
    registration_date: (v) => (!v ? 'Registration date is required' : true),
    declaration_agreed: (v) => (!v ? 'You must agree to the declaration' : true),
    client_signature_name: (v) => (!v ? 'Signature name is required' : true),
    client_signature: (v) => {
        if (!(v instanceof File)) return 'Please upload a scanned signature';
        if (!['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(v.type))
            return 'Signature must be JPG, PNG or WEBP';
        if (v.size > 2 * 1024 * 1024) return 'Signature must be under 2 MB';
        return true;
    },
};

function validateField(field: string) {
    if (!rules[field]) {
        delete errors[field];
        return true;
    }
    const result = rules[field]((form as any)[field]);
    if (result !== true) {
        errors[field] = result;
        return false;
    }
    delete errors[field];
    return true;
}

function validateCurrentStep() {
    let ok = true;
    Object.keys(stepErrors).forEach((k) => delete stepErrors[k]);
    globalError.value = false;
    steps[currentStep.value].fields.forEach((field) => {
        if (!validateField(field)) {
            ok = false;
            stepErrors[field] = errors[field];
        }
    });
    if (!ok) globalError.value = true;
    return ok;
}

function goToStep(index: number) {
    if (index < currentStep.value || index <= highestStepReached.value) {
        currentStep.value = index;
        globalError.value = false;
    }
}
function prevStep() {
    if (currentStep.value > 0) currentStep.value--;
    globalError.value = false;
}

async function handleNextOrSubmit() {
    if (!validateCurrentStep()) return;
    if (currentStep.value < steps.length - 1) {
        currentStep.value++;
        highestStepReached.value = Math.max(highestStepReached.value, currentStep.value);
        if (currentStep.value === steps.length - 1 && !form.client_signature_name) {
            form.client_signature_name = form.full_name;
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return;
    }
    submitting.value = true;
    try {
        const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content;
        const res = await axios.post('/client-registration', toFormData(form as any), {
            headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
        });
        clientCode.value = res.data.client_code;
        clientId.value = res.data.client_id;
        submitted.value = true;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (err: any) {
        if (err.response?.status === 422) {
            Object.entries(err.response.data.errors || {}).forEach(([key, msgs]) => {
                stepErrors[key] = (msgs as string[])[0];
                errors[key] = (msgs as string[])[0];
            });
            globalError.value = true;
        }
    } finally {
        submitting.value = false;
    }
}

function resetForm() {
    Object.assign(form, emptyForm());
    Object.keys(errors).forEach((k) => delete errors[k]);
    currentStep.value = 0;
    highestStepReached.value = 0;
    submitted.value = false;
}

function toFormData(data: Record<string, unknown>): FormData {
    const fd = new FormData();
    Object.entries(data).forEach(([key, value]) => {
        if (value === null || value === undefined) return;
        if (value instanceof File) {
            fd.append(key, value);
            return;
        }
        if (Array.isArray(value)) {
            value.forEach((item) => fd.append(`${key}[]`, String(item)));
            return;
        }
        if (typeof value === 'object') {
            Object.entries(value as Record<string, unknown>).forEach(([k, v]) => {
                if (v) fd.append(`${key}[${k}]`, String(v));
            });
            return;
        }
        if (typeof value === 'boolean') {
            fd.append(key, value ? '1' : '0');
            return;
        }
        fd.append(key, String(value));
    });
    return fd;
}
</script>

<style>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.4s ease-in-out;
}
.fade-slide-enter-from {
    opacity: 0;
    transform: translateY(20px);
}
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}
</style>
