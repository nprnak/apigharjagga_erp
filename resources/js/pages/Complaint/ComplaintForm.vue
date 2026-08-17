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
                        Complaint Registered
                    </h2>
                    <p class="relative z-10 text-lg font-medium text-emerald-100">
                        गुनासो सफलतापूर्वक दर्ता भयो
                    </p>
                </div>
                <div class="px-8 py-10 text-center">
                    <p class="mb-3 text-xs font-bold tracking-[0.2em] text-slate-400 uppercase">
                        Complaint ID
                    </p>
                    <div class="mb-8 inline-block rounded-2xl border border-slate-100 bg-slate-50 px-6 py-4 shadow-sm">
                        <p class="font-mono text-3xl font-black tracking-widest text-slate-800">
                            {{ complaintCode }}
                        </p>
                    </div>
                    <p class="mx-auto mb-10 max-w-sm text-sm leading-relaxed text-slate-500">
                        The complaint has been added to the database. Download the
                        official form copy below.
                    </p>
                    <div class="flex flex-col gap-4">
                        <a
                            :href="`/complaint/${complaintId}/pdf`"
                            target="_blank"
                            class="group relative inline-flex items-center justify-center gap-3 overflow-hidden rounded-2xl bg-slate-900 px-8 py-4 font-semibold text-white shadow-xl shadow-slate-900/20 transition-all duration-300 hover:-translate-y-0.5 hover:bg-slate-800"
                        >
                            <span class="relative z-10">Download PDF Document</span>
                        </a>
                        <button
                            @click="resetForm"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-8 py-4 font-semibold text-slate-600 hover:bg-slate-50"
                        >
                            Register Another Complaint
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
                    AGJ-FRM-08
                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                    Version 1.0
                </div>
                <h1 class="mb-4 text-4xl font-extrabold tracking-tight text-slate-900 md:text-5xl">
                    Customer Complaint Form
                </h1>
                <p class="text-lg font-medium text-slate-500">ग्राहक गुनासो / उजुरी फारम</p>
                <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-400">
                    Project / Service: Api Ghar Jagga Property Listing, Verification, Valuation &amp; Digital Service
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
                    <!-- STEP 1: REGISTRATION -->
                    <div
                        v-if="currentStep === 0"
                        key="s1"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">Complaint Registration Details</h2>
                            <p class="mb-8 text-sm text-slate-500">गुनासो दर्ता विवरण</p>

                            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Complaint ID" hint="Assigned automatically after save">
                                    <input type="text" value="Auto-generated (CMP-YYYYMMDD-####)" disabled class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-medium text-slate-500" />
                                </FormField>
                                <FormField label="Date of Complaint" required :error="errors.complaint_date">
                                    <input v-model="form.complaint_date" type="date" :max="today" :class="inputClass(errors.complaint_date)" @blur="validateField('complaint_date')" />
                                </FormField>
                                <FormField label="Time" required :error="errors.complaint_time">
                                    <input v-model="form.complaint_time" type="time" :class="inputClass(errors.complaint_time)" @blur="validateField('complaint_time')" />
                                </FormField>
                            </div>

                            <FormField label="Received Through" required :error="errors.received_through" class="mb-6">
                                <div class="mt-2 grid grid-cols-2 gap-3 md:grid-cols-3">
                                    <label v-for="opt in receivedOptions" :key="opt.value" class="cursor-pointer">
                                        <input type="radio" :value="opt.value" v-model="form.received_through" class="peer sr-only" />
                                        <span class="block rounded-2xl border-2 border-slate-200 px-3 py-4 text-center text-sm font-bold text-slate-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50">
                                            {{ opt.label }}
                                        </span>
                                    </label>
                                </div>
                                <input
                                    v-if="form.received_through === 'other'"
                                    v-model="form.received_through_other"
                                    type="text"
                                    placeholder="Please specify"
                                    :class="inputClass(errors.received_through_other) + ' mt-3'"
                                />
                            </FormField>

                            <h3 class="mb-4 text-base font-bold text-slate-800">Received By / प्राप्त गर्ने कर्मचारी</h3>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Name" :error="errors.received_by_name">
                                    <input v-model="form.received_by_name" type="text" :class="inputClass(errors.received_by_name)" />
                                </FormField>
                                <FormField label="Designation" :error="errors.received_by_designation">
                                    <input v-model="form.received_by_designation" type="text" :class="inputClass(errors.received_by_designation)" />
                                </FormField>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: CUSTOMER -->
                    <div
                        v-else-if="currentStep === 1"
                        key="s2"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">Customer Information</h2>
                            <p class="mb-8 text-sm text-slate-500">ग्राहक विवरण</p>

                            <FormField label="Customer Type" required :error="errors.customer_type" class="mb-8">
                                <div class="mt-2 grid grid-cols-2 gap-3 md:grid-cols-3">
                                    <label v-for="opt in customerTypes" :key="opt.value" class="cursor-pointer">
                                        <input type="radio" :value="opt.value" v-model="form.customer_type" class="peer sr-only" />
                                        <span class="block rounded-2xl border-2 border-slate-200 px-3 py-4 text-center text-sm font-bold text-slate-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50">
                                            {{ opt.label }}
                                        </span>
                                    </label>
                                </div>
                                <input
                                    v-if="form.customer_type === 'other'"
                                    v-model="form.customer_type_other"
                                    type="text"
                                    placeholder="Please specify"
                                    :class="inputClass(errors.customer_type_other) + ' mt-3'"
                                />
                            </FormField>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Full Name" required :error="errors.full_name">
                                    <input v-model="form.full_name" type="text" :class="inputClass(errors.full_name)" @blur="validateField('full_name')" />
                                </FormField>
                                <FormField label="Client ID" hint="Leave blank if not registered" :error="errors.client_code">
                                    <input v-model="form.client_code" type="text" placeholder="CLT-..." :class="inputClass(errors.client_code)" />
                                </FormField>
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
                                <FormField label="Email Address" :error="errors.email">
                                    <input v-model="form.email" type="email" :class="inputClass(errors.email)" />
                                </FormField>
                                <div class="md:col-span-2">
                                    <FormField label="Address" required :error="errors.address">
                                        <textarea v-model="form.address" rows="3" :class="inputClass(errors.address) + ' resize-none'" @blur="validateField('address')" />
                                    </FormField>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: PROPERTY + CATEGORY -->
                    <div
                        v-else-if="currentStep === 2"
                        key="s3"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">Property &amp; Category</h2>
                            <p class="mb-8 text-sm text-slate-500">सम्पत्ति सम्बन्धी विवरण तथा गुनासोको प्रकार</p>

                            <h3 class="mb-4 text-base font-bold text-slate-800">Property Related Details / सम्पत्ति विवरण</h3>
                            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Property ID" hint="Optional — existing property code" :error="errors.property_code">
                                    <input v-model="form.property_code" type="text" placeholder="PROP-..." :class="inputClass(errors.property_code)" />
                                </FormField>
                                <FormField label="Property Location" :error="errors.property_location">
                                    <input v-model="form.property_location" type="text" :class="inputClass(errors.property_location)" />
                                </FormField>
                                <FormField label="Kitta No." :error="errors.kitta_no">
                                    <input v-model="form.kitta_no" type="text" :class="inputClass(errors.kitta_no)" />
                                </FormField>
                                <FormField label="Service Taken" :error="errors.service_reference">
                                    <input v-model="form.service_reference" type="text" :class="inputClass(errors.service_reference)" />
                                </FormField>
                                <FormField label="Date of Service" :error="errors.service_date">
                                    <input v-model="form.service_date" type="date" :max="today" :class="inputClass(errors.service_date)" />
                                </FormField>
                            </div>

                            <FormField label="Complaint Category" required :error="errors.category">
                                <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <label
                                        v-for="opt in categoryOptions"
                                        :key="opt.value"
                                        class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3.5 hover:bg-slate-50 [&:has(input:checked)]:border-emerald-500 [&:has(input:checked)]:bg-emerald-50/30"
                                    >
                                        <input type="radio" :value="opt.value" v-model="form.category" class="h-5 w-5 border-slate-300 text-emerald-600" />
                                        <span class="text-sm font-medium text-slate-700">{{ opt.label }}</span>
                                    </label>
                                </div>
                                <input
                                    v-if="form.category === 'other'"
                                    v-model="form.category_other"
                                    type="text"
                                    placeholder="Please specify"
                                    :class="inputClass(errors.category_other) + ' mt-3'"
                                />
                            </FormField>
                        </div>
                    </div>

                    <!-- STEP 4: DESCRIPTION + EVIDENCE + PRIORITY -->
                    <div
                        v-else-if="currentStep === 3"
                        key="s4"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">Description, Evidence &amp; Priority</h2>
                            <p class="mb-8 text-sm text-slate-500">गुनासोको विवरण, प्रमाण तथा प्राथमिकता</p>

                            <FormField label="Details of Complaint" required :error="errors.description" class="mb-8">
                                <textarea
                                    v-model="form.description"
                                    rows="6"
                                    placeholder="Describe the issue in detail (minimum 20 characters)"
                                    :class="inputClass(errors.description) + ' resize-none'"
                                    @blur="validateField('description')"
                                />
                            </FormField>

                            <h3 class="mb-4 text-base font-bold text-slate-800">Supporting Documents / Evidence</h3>
                            <p class="mb-4 text-xs text-slate-400">Tick attached items. Optional file upload: JPG, PNG, WEBP or PDF — max 5 MB.</p>
                            <div class="mb-6 space-y-4">
                                <div
                                    v-for="ev in evidenceOptions"
                                    :key="ev.value"
                                    class="rounded-2xl border border-slate-200 p-4"
                                >
                                    <label class="flex cursor-pointer items-center gap-3">
                                        <input type="checkbox" :value="ev.value" v-model="form.attached_evidence" class="h-5 w-5 rounded border-slate-300 text-emerald-600" />
                                        <span class="text-sm font-bold text-slate-700">{{ ev.label }}</span>
                                    </label>
                                    <input
                                        v-if="form.attached_evidence.includes(ev.value)"
                                        type="file"
                                        accept=".jpg,.jpeg,.png,.webp,.pdf"
                                        class="mt-3 block w-full text-sm text-slate-600"
                                        @change="onEvidenceFile(ev.value, $event)"
                                    />
                                </div>
                            </div>
                            <div v-if="form.attached_evidence.includes('other')" class="mb-8">
                                <FormField label="Other Documents (specify)" :error="errors.evidence_other_note">
                                    <input v-model="form.evidence_other_note" type="text" :class="inputClass(errors.evidence_other_note)" />
                                </FormField>
                            </div>

                            <FormField label="Complaint Priority Level" required :error="errors.priority">
                                <div class="mt-2 flex flex-wrap gap-3">
                                    <label v-for="opt in priorityOptions" :key="opt.value" class="cursor-pointer">
                                        <input type="radio" :value="opt.value" v-model="form.priority" class="peer sr-only" />
                                        <span class="inline-block rounded-full border border-slate-200 px-5 py-2.5 text-sm font-semibold peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white">
                                            {{ opt.label }}
                                        </span>
                                    </label>
                                </div>
                            </FormField>
                        </div>
                    </div>

                    <!-- STEP 5: INVESTIGATION + STATUS -->
                    <div
                        v-else-if="currentStep === 4"
                        key="s5"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">Investigation, Action &amp; Status</h2>
                            <p class="mb-8 text-sm text-slate-500">आन्तरिक अनुसन्धान तथा गुनासो स्थिति</p>

                            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Assigned Department" :error="errors.assigned_department">
                                    <input v-model="form.assigned_department" type="text" :class="inputClass(errors.assigned_department)" />
                                </FormField>
                                <FormField label="Assigned Officer" :error="errors.assigned_officer_name">
                                    <input v-model="form.assigned_officer_name" type="text" :class="inputClass(errors.assigned_officer_name)" />
                                </FormField>
                                <FormField label="Investigation Date" :error="errors.investigation_date">
                                    <input v-model="form.investigation_date" type="date" :max="today" :class="inputClass(errors.investigation_date)" />
                                </FormField>
                                <FormField label="Resolution Date" :error="errors.resolution_date">
                                    <input v-model="form.resolution_date" type="date" :max="today" :class="inputClass(errors.resolution_date)" />
                                </FormField>
                                <div class="md:col-span-2">
                                    <FormField label="Findings" :error="errors.findings">
                                        <textarea v-model="form.findings" rows="3" :class="inputClass(errors.findings) + ' resize-none'" />
                                    </FormField>
                                </div>
                                <div class="md:col-span-2">
                                    <FormField label="Corrective Action Taken" :error="errors.corrective_action_taken">
                                        <textarea v-model="form.corrective_action_taken" rows="3" :class="inputClass(errors.corrective_action_taken) + ' resize-none'" />
                                    </FormField>
                                </div>
                            </div>

                            <FormField label="Complaint Status" required :error="errors.status">
                                <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <label
                                        v-for="opt in statusOptions"
                                        :key="opt.value"
                                        class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3.5 hover:bg-slate-50 [&:has(input:checked)]:border-emerald-500 [&:has(input:checked)]:bg-emerald-50/30"
                                    >
                                        <input type="radio" :value="opt.value" v-model="form.status" class="h-5 w-5 border-slate-300 text-emerald-600" />
                                        <span class="text-sm font-medium text-slate-700">{{ opt.label }}</span>
                                    </label>
                                </div>
                            </FormField>
                        </div>
                    </div>

                    <!-- STEP 6: FEEDBACK + DECLARATION -->
                    <div
                        v-else-if="currentStep === 5"
                        key="s6"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">Feedback &amp; Declaration</h2>
                            <p class="mb-8 text-sm text-slate-500">समाधानपछिको प्रतिक्रिया तथा घोषणा</p>

                            <FormField label="Customer Satisfaction Level" :error="errors.satisfaction_level" class="mb-6">
                                <div class="mt-2 flex flex-wrap gap-3">
                                    <label v-for="opt in satisfactionOptions" :key="opt.value" class="cursor-pointer">
                                        <input type="radio" :value="opt.value" v-model="form.satisfaction_level" class="peer sr-only" />
                                        <span class="inline-block rounded-full border border-slate-200 px-5 py-2.5 text-sm font-semibold peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white">
                                            {{ opt.label }}
                                        </span>
                                    </label>
                                </div>
                            </FormField>
                            <div class="mb-8">
                                <FormField label="Customer Remarks" :error="errors.customer_remarks">
                                    <textarea v-model="form.customer_remarks" rows="3" :class="inputClass(errors.customer_remarks) + ' resize-none'" />
                                </FormField>
                            </div>

                            <h3 class="mb-4 text-base font-bold text-slate-800">Reviewed &amp; Approved By / समीक्षा तथा स्वीकृत गर्ने</h3>
                            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Name" :error="errors.reviewed_by_name">
                                    <input v-model="form.reviewed_by_name" type="text" :class="inputClass(errors.reviewed_by_name)" />
                                </FormField>
                                <FormField label="Designation" :error="errors.reviewed_by_designation">
                                    <input v-model="form.reviewed_by_designation" type="text" :class="inputClass(errors.reviewed_by_designation)" />
                                </FormField>
                                <FormField label="Date" :error="errors.reviewed_by_date">
                                    <input v-model="form.reviewed_by_date" type="date" :max="today" :class="inputClass(errors.reviewed_by_date)" />
                                </FormField>
                                <SignatureUpload v-model="form.reviewed_by_signature" label="Reviewer Signature" :error="errors.reviewed_by_signature" />
                            </div>

                            <div class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 p-6 text-sm leading-relaxed text-slate-700">
                                I confirm that the complaint information provided above is accurate and authorize Api Ghar Jagga Pvt. Ltd. to investigate and resolve the issue according to company policies and procedures.
                                <p class="mt-3 text-xs text-slate-500 italic">म घोषणा गर्दछु कि माथि उल्लिखित गुनासो विवरण सही छ र Api Ghar Jagga Pvt. Ltd. लाई कम्पनीको नीति तथा प्रक्रियाअनुसार अनुसन्धान तथा समाधान गर्न अनुमति दिन्छु।</p>
                            </div>
                            <label
                                class="mb-6 flex cursor-pointer items-start gap-4 rounded-2xl border p-4"
                                :class="errors.declaration_agreed ? 'border-red-300 bg-red-50' : 'border-slate-200'"
                            >
                                <input type="checkbox" v-model="form.declaration_agreed" class="mt-0.5 h-6 w-6 rounded border-slate-300 text-emerald-600" />
                                <span class="font-bold text-slate-800">I agree to the declaration / घोषणामा सहमत छु</span>
                            </label>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <FormField label="Customer Name (Signature)" required :error="errors.customer_signature_name">
                                    <input v-model="form.customer_signature_name" type="text" :class="inputClass(errors.customer_signature_name)" @blur="validateField('customer_signature_name')" />
                                </FormField>
                                <FormField label="Date" required :error="errors.customer_signature_date">
                                    <input v-model="form.customer_signature_date" type="date" :max="today" :class="inputClass(errors.customer_signature_date)" />
                                </FormField>
                                <div class="md:col-span-2">
                                    <SignatureUpload
                                        v-model="form.customer_signature"
                                        label="Customer Scanned Signature"
                                        required
                                        :error="errors.customer_signature"
                                        @update:model-value="validateField('customer_signature')"
                                    />
                                </div>
                                <FormField label="Received By Date" :error="errors.received_by_date">
                                    <input v-model="form.received_by_date" type="date" :max="today" :class="inputClass(errors.received_by_date)" />
                                </FormField>
                                <SignatureUpload v-model="form.received_by_signature" label="Received By Signature" :error="errors.received_by_signature" />
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
                        {{ submitting ? 'Processing...' : currentStep === steps.length - 1 ? 'Submit Complaint' : 'Continue' }}
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

const pad = (n: number) => String(n).padStart(2, '0');
const now = new Date();
const currentTime = `${pad(now.getHours())}:${pad(now.getMinutes())}`;
const today = now.toISOString().split('T')[0];

const emptyForm = () => ({
    complaint_date: today,
    complaint_time: currentTime,
    received_through: '',
    received_through_other: '',
    received_by_name: '',
    received_by_designation: '',
    received_by_date: '',
    received_by_signature: null as File | null,
    full_name: '',
    client_code: '',
    mobile_no: '',
    email: '',
    address: '',
    customer_type: '',
    customer_type_other: '',
    property_code: '',
    property_location: '',
    kitta_no: '',
    service_reference: '',
    service_date: '',
    category: '',
    category_other: '',
    description: '',
    attached_evidence: [] as string[],
    evidence_other_note: '',
    evidence_files: {
        photo: null as File | null,
        screenshot: null as File | null,
        agreement_copy: null as File | null,
        payment_receipt: null as File | null,
        other: null as File | null,
    },
    priority: 'medium',
    assigned_department: '',
    assigned_officer_name: '',
    investigation_date: '',
    findings: '',
    corrective_action_taken: '',
    resolution_date: '',
    status: 'registered',
    satisfaction_level: '',
    customer_remarks: '',
    declaration_agreed: false,
    customer_signature_name: '',
    customer_signature_date: today,
    customer_signature: null as File | null,
    reviewed_by_name: '',
    reviewed_by_designation: '',
    reviewed_by_date: '',
    reviewed_by_signature: null as File | null,
});

const form = reactive(emptyForm());
const errors = reactive<Record<string, string>>({});
const steps = [
    { title: 'Register', fields: ['complaint_date', 'complaint_time', 'received_through', 'received_through_other'] },
    { title: 'Customer', fields: ['customer_type', 'customer_type_other', 'full_name', 'mobile_no', 'address'] },
    { title: 'Property', fields: ['category', 'category_other'] },
    { title: 'Details', fields: ['description', 'priority', 'evidence_other_note'] },
    { title: 'Action', fields: ['status', 'findings', 'corrective_action_taken', 'resolution_date'] },
    {
        title: 'Sign',
        fields: ['declaration_agreed', 'customer_signature_name', 'customer_signature', 'satisfaction_level'],
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
const complaintCode = ref('');
const complaintId = ref<number | null>(null);

const receivedOptions = [
    { value: 'mobile_app', label: 'Mobile App' },
    { value: 'website', label: 'Website' },
    { value: 'office', label: 'Office' },
    { value: 'email', label: 'Email' },
    { value: 'phone', label: 'Phone' },
    { value: 'other', label: 'Other' },
];
const customerTypes = [
    { value: 'owner', label: 'Owner' },
    { value: 'buyer', label: 'Buyer' },
    { value: 'investor', label: 'Investor' },
    { value: 'tenant', label: 'Tenant' },
    { value: 'other', label: 'Other' },
];
const categoryOptions = [
    { value: 'property_listing_issue', label: 'Property Listing Issue' },
    { value: 'property_information_incorrect', label: 'Property Information Incorrect' },
    { value: 'valuation_related_issue', label: 'Valuation Related Issue' },
    { value: 'site_visit_issue', label: 'Site Visit Issue' },
    { value: 'digital_platform_issue', label: 'Digital Platform Issue' },
    { value: 'staff_service_behaviour', label: 'Staff / Service Behaviour Issue' },
    { value: 'payment_billing_issue', label: 'Payment / Billing Issue' },
    { value: 'documentation_issue', label: 'Documentation Issue' },
    { value: 'other', label: 'Other' },
];
const evidenceOptions = [
    { value: 'photo', label: 'Photo' },
    { value: 'screenshot', label: 'Screenshot' },
    { value: 'agreement_copy', label: 'Agreement Copy' },
    { value: 'payment_receipt', label: 'Payment Receipt' },
    { value: 'other', label: 'Other Documents' },
];
const priorityOptions = [
    { value: 'low', label: 'Low' },
    { value: 'medium', label: 'Medium' },
    { value: 'high', label: 'High' },
    { value: 'urgent', label: 'Urgent' },
];
const statusOptions = [
    { value: 'registered', label: 'Registered' },
    { value: 'under_investigation', label: 'Under Investigation' },
    { value: 'resolved', label: 'Resolved' },
    { value: 'closed', label: 'Closed' },
    { value: 'pending_customer_response', label: 'Pending Customer Response' },
];
const satisfactionOptions = [
    { value: 'very_satisfied', label: 'Very Satisfied' },
    { value: 'satisfied', label: 'Satisfied' },
    { value: 'neutral', label: 'Neutral' },
    { value: 'dissatisfied', label: 'Dissatisfied' },
];

const inputClass = (error?: string) =>
    `w-full px-4 py-3 rounded-xl border text-slate-800 font-medium bg-white ${
        error
            ? 'border-red-300 bg-red-50'
            : 'border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10'
    }`;

const resolvedStatuses = ['resolved', 'closed'];
const rules: Record<string, (v: any) => string | true> = {
    complaint_date: (v) => (!v ? 'Complaint date is required' : true),
    complaint_time: (v) => (!v ? 'Complaint time is required' : true),
    received_through: (v) => (!v ? 'Please select how the complaint was received' : true),
    received_through_other: (v) =>
        form.received_through === 'other' && !v ? 'Please specify the other channel' : true,
    customer_type: (v) => (!v ? 'Please select the customer type' : true),
    customer_type_other: (v) =>
        form.customer_type === 'other' && !v ? 'Please specify the customer type' : true,
    full_name: (v) => (!v ? 'Full name is required' : true),
    mobile_no: (v) =>
        !v || !/^9[0-9]{9}$/.test(v)
            ? 'Enter a valid 10-digit mobile number starting with 9'
            : true,
    address: (v) => (!v || v.trim().length < 8 ? 'Address is required' : true),
    category: (v) => (!v ? 'Please select a complaint category' : true),
    category_other: (v) =>
        form.category === 'other' && !v ? 'Please specify the other category' : true,
    description: (v) =>
        !v || v.trim().length < 20 ? 'Description must be at least 20 characters' : true,
    priority: (v) => (!v ? 'Please select a priority level' : true),
    evidence_other_note: (v) =>
        form.attached_evidence.includes('other') && !v
            ? 'Please specify the other supporting document'
            : true,
    status: (v) => (!v ? 'Please select the complaint status' : true),
    findings: (v) =>
        resolvedStatuses.includes(form.status) && !v
            ? 'Findings are required when the complaint is resolved or closed'
            : true,
    corrective_action_taken: (v) =>
        resolvedStatuses.includes(form.status) && !v
            ? 'Corrective action is required when the complaint is resolved or closed'
            : true,
    resolution_date: (v) =>
        resolvedStatuses.includes(form.status) && !v
            ? 'Resolution date is required when the complaint is resolved or closed'
            : true,
    satisfaction_level: (v) =>
        resolvedStatuses.includes(form.status) && !v
            ? 'Satisfaction level is required after resolution'
            : true,
    declaration_agreed: (v) => (!v ? 'You must agree to the declaration' : true),
    customer_signature_name: (v) => (!v ? 'Signature name is required' : true),
    customer_signature: (v) => {
        if (!(v instanceof File)) return 'Please upload a scanned signature';
        if (!['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(v.type))
            return 'Signature must be JPG, PNG or WEBP';
        if (v.size > 2 * 1024 * 1024) return 'Signature must be under 2 MB';
        return true;
    },
};

function onEvidenceFile(type: string, event: Event) {
    const input = event.target as HTMLInputElement;
    (form.evidence_files as any)[type] = input.files?.[0] ?? null;
}

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
        if (currentStep.value === steps.length - 1 && !form.customer_signature_name) {
            form.customer_signature_name = form.full_name;
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return;
    }
    submitting.value = true;
    try {
        const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content;
        const res = await axios.post('/complaint', toFormData(form as any), {
            headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
        });
        complaintCode.value = res.data.complaint_code;
        complaintId.value = res.data.complaint_id;
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
        if (key === 'evidence_files') {
            Object.entries(value as Record<string, File | null>).forEach(([k, file]) => {
                if (file instanceof File) fd.append(`evidence_files[${k}]`, file);
            });
            return;
        }
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
        if (value === '') return;
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
