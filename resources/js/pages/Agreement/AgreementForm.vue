<template>
    <div
        class="min-h-screen bg-slate-50 font-sans text-slate-800 selection:bg-emerald-500 selection:text-white"
    >
        <!-- ================================================================ -->
        <!-- SUCCESS STATE -->
        <!-- ================================================================ -->
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
                        <svg
                            class="h-12 w-12 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                    </div>
                    <h2
                        class="relative z-10 mb-2 text-3xl font-extrabold tracking-tight text-white"
                    >
                        Agreement Submitted
                    </h2>
                    <p
                        class="relative z-10 text-lg font-medium text-emerald-100"
                    >
                        सम्झौता सफलतापूर्वक पेश गरियो
                    </p>
                </div>
                <div class="px-8 py-10 text-center">
                    <p
                        class="mb-3 text-xs font-bold tracking-[0.2em] text-slate-400 uppercase"
                    >
                        Agreement Reference No.
                    </p>
                    <div
                        class="mb-8 inline-block rounded-2xl border border-slate-100 bg-slate-50 px-6 py-4 shadow-sm"
                    >
                        <p
                            class="font-mono text-3xl font-black tracking-widest text-slate-800"
                        >
                            {{ agreementNo }}
                        </p>
                    </div>
                    <p
                        class="mx-auto mb-10 max-w-sm text-sm leading-relaxed text-slate-500"
                    >
                        The sale/purchase agreement has been recorded. Download
                        your official document copy below.
                    </p>
                    <div class="flex flex-col gap-4">
                        <a
                            :href="`/agreement/${agreementId}/pdf`"
                            target="_blank"
                            class="group relative inline-flex items-center justify-center gap-3 overflow-hidden rounded-2xl bg-slate-900 px-8 py-4 font-semibold text-white shadow-xl shadow-slate-900/20 transition-all duration-300 hover:-translate-y-0.5 hover:bg-slate-800 hover:shadow-slate-900/40"
                        >
                            <div
                                class="absolute inset-0 w-full translate-x-[-100%] bg-white/10 transition-transform duration-700 ease-in-out group-hover:translate-x-[100%]"
                            ></div>
                            <svg
                                class="relative z-10 h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                            <span class="relative z-10"
                                >Download PDF Document</span
                            >
                        </a>
                        <button
                            @click="resetForm"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-8 py-4 font-semibold text-slate-600 transition-all duration-200 hover:border-slate-300 hover:bg-slate-50"
                        >
                            Create Another Agreement
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================ -->
        <!-- FORM STATE (MULTI-STEP WIZARD) -->
        <!-- ================================================================ -->
        <div v-else class="mx-auto max-w-4xl px-4 py-12 sm:px-6">
            <!-- Header -->
            <div class="mb-12 text-center">
                <div
                    class="mb-6 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-1.5 text-[10px] font-semibold tracking-widest text-slate-500 uppercase shadow-sm"
                >
                    AGJ-FRM-002
                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                    Version 1.0
                </div>
                <h1
                    class="mb-4 text-4xl font-extrabold tracking-tight text-slate-900 md:text-5xl"
                >
                    House &amp; Land Sale/Purchase Agreement
                </h1>
                <p class="text-lg font-medium text-slate-500">
                    घर जग्गा खरिद–बिक्री सम्झौता पत्र
                </p>
            </div>

            <!-- Progress Tracker -->
            <div class="relative mx-auto mb-12 max-w-3xl">
                <div
                    class="absolute top-1/2 left-0 z-0 h-1 w-full -translate-y-1/2 rounded-full bg-slate-200"
                ></div>
                <div
                    class="absolute top-1/2 left-0 z-0 h-1 -translate-y-1/2 rounded-full bg-emerald-500 transition-all duration-500 ease-out"
                    :style="{ width: progressPercentage + '%' }"
                ></div>

                <div class="relative z-10 flex justify-between">
                    <div
                        v-for="(step, index) in steps"
                        :key="index"
                        class="flex flex-col items-center gap-2"
                    >
                        <button
                            @click="goToStep(index)"
                            :disabled="index > highestStepReached"
                            class="flex h-10 w-10 items-center justify-center rounded-full border-[3px] text-sm font-bold transition-all duration-300"
                            :class="[
                                currentStep === index
                                    ? 'border-emerald-200 bg-emerald-500 text-white shadow-[0_0_0_4px_rgba(16,185,129,0.1)]'
                                    : index < currentStep
                                      ? 'cursor-pointer border-emerald-500 bg-emerald-500 text-white'
                                      : index <= highestStepReached
                                        ? 'cursor-pointer border-slate-300 bg-white text-slate-500 hover:border-slate-400'
                                        : 'cursor-not-allowed border-slate-200 bg-slate-50 text-slate-400',
                            ]"
                        >
                            <svg
                                v-if="index < currentStep"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2.5"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                            <span v-else>{{ index + 1 }}</span>
                        </button>
                        <span
                            class="hidden text-[10px] font-bold tracking-wider uppercase transition-colors duration-300 sm:block"
                            :class="
                                currentStep >= index
                                    ? 'text-slate-800'
                                    : 'text-slate-400'
                            "
                        >
                            {{ step.title }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Global Error Banner -->
            <div
                v-if="globalError"
                class="animate-in slide-in-from-top-4 mb-8 flex items-start gap-4 rounded-2xl border border-red-200 bg-red-50 p-5"
            >
                <div class="shrink-0 rounded-full bg-red-100 p-1.5">
                    <svg
                        class="h-5 w-5 text-red-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>
                <div>
                    <p class="mb-1 text-sm font-bold text-red-800">
                        Please resolve the following errors to continue:
                    </p>
                    <ul class="space-y-1">
                        <li
                            v-for="(msg, field) in stepErrors"
                            :key="field"
                            class="flex items-center gap-2 text-sm font-medium text-red-600"
                        >
                            <span
                                class="h-1 w-1 rounded-full bg-red-400"
                            ></span>
                            {{ msg }}
                        </li>
                    </ul>
                </div>
            </div>

            <form
                @submit.prevent="handleNextOrSubmit"
                novalidate
                class="relative min-h-[400px]"
            >
                <transition name="fade-slide" mode="out-in">
                    <!-- ──────────────────────────────────────────────────── -->
                    <!-- STEP 1: PARTIES (SELLER & BUYER) -->
                    <!-- ──────────────────────────────────────────────────── -->
                    <div
                        v-if="currentStep === 0"
                        key="step1"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">
                                Parties to the Agreement
                            </h2>
                            <p class="mb-8 text-sm text-slate-500">
                                Enter the details of the Seller and the Buyer.
                            </p>

                            <h3
                                class="mb-4 flex items-center gap-2 text-base font-bold text-slate-800"
                            >
                                <span
                                    class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-xs text-emerald-700"
                                    >1</span
                                >
                                First Party (Seller)
                                <span class="text-xs font-normal text-slate-400"
                                    >/ पहिलो पक्ष (विक्रेता)</span
                                >
                            </h3>
                            <div
                                class="mb-8 grid grid-cols-1 gap-x-8 gap-y-6 md:grid-cols-2"
                            >
                                <FormField
                                    label="Full Name"
                                    required
                                    :error="errors.seller_full_name"
                                >
                                    <input
                                        v-model="form.seller_full_name"
                                        type="text"
                                        placeholder="Enter seller's full name"
                                        :class="
                                            inputClass(errors.seller_full_name)
                                        "
                                        @blur="
                                            validateField('seller_full_name')
                                        "
                                    />
                                </FormField>
                                <FormField
                                    label="Father's / Mother's Name"
                                    :error="errors.seller_father_mother_name"
                                >
                                    <input
                                        v-model="
                                            form.seller_father_mother_name
                                        "
                                        type="text"
                                        :class="
                                            inputClass(
                                                errors.seller_father_mother_name,
                                            )
                                        "
                                    />
                                </FormField>
                                <FormField
                                    label="Citizenship No."
                                    required
                                    :error="errors.seller_citizenship_no"
                                >
                                    <input
                                        v-model="form.seller_citizenship_no"
                                        type="text"
                                        placeholder="e.g. 12-01-75-12345"
                                        :class="
                                            inputClass(
                                                errors.seller_citizenship_no,
                                            )
                                        "
                                        @blur="
                                            validateField(
                                                'seller_citizenship_no',
                                            )
                                        "
                                    />
                                </FormField>
                                <FormField
                                    label="Contact No."
                                    required
                                    hint="10 digits starting with 9"
                                    :error="errors.seller_contact_no"
                                >
                                    <input
                                        v-model="form.seller_contact_no"
                                        type="tel"
                                        placeholder="98XXXXXXXX"
                                        :class="
                                            inputClass(errors.seller_contact_no)
                                        "
                                        @blur="
                                            validateField('seller_contact_no')
                                        "
                                        @input="
                                            form.seller_contact_no =
                                                form.seller_contact_no
                                                    .replace(/\D/g, '')
                                                    .slice(0, 10)
                                        "
                                    />
                                </FormField>
                                <div class="md:col-span-2">
                                    <FormField
                                        label="Permanent Address"
                                        required
                                        :error="errors.seller_permanent_address"
                                    >
                                        <textarea
                                            v-model="
                                                form.seller_permanent_address
                                            "
                                            rows="2"
                                            placeholder="Enter seller's permanent address"
                                            :class="
                                                inputClass(
                                                    errors.seller_permanent_address,
                                                ) + ' resize-none'
                                            "
                                            @blur="
                                                validateField(
                                                    'seller_permanent_address',
                                                )
                                            "
                                        />
                                    </FormField>
                                </div>
                            </div>

                            <h3
                                class="mb-4 flex items-center gap-2 border-t border-slate-100 pt-8 text-base font-bold text-slate-800"
                            >
                                <span
                                    class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-xs text-emerald-700"
                                    >2</span
                                >
                                Second Party (Buyer)
                                <span class="text-xs font-normal text-slate-400"
                                    >/ दोस्रो पक्ष (खरिदकर्ता)</span
                                >
                            </h3>
                            <div
                                class="grid grid-cols-1 gap-x-8 gap-y-6 md:grid-cols-2"
                            >
                                <FormField
                                    label="Full Name"
                                    required
                                    :error="errors.buyer_full_name"
                                >
                                    <input
                                        v-model="form.buyer_full_name"
                                        type="text"
                                        placeholder="Enter buyer's full name"
                                        :class="
                                            inputClass(errors.buyer_full_name)
                                        "
                                        @blur="
                                            validateField('buyer_full_name')
                                        "
                                    />
                                </FormField>
                                <FormField
                                    label="Father's / Mother's Name"
                                    :error="errors.buyer_father_mother_name"
                                >
                                    <input
                                        v-model="
                                            form.buyer_father_mother_name
                                        "
                                        type="text"
                                        :class="
                                            inputClass(
                                                errors.buyer_father_mother_name,
                                            )
                                        "
                                    />
                                </FormField>
                                <FormField
                                    label="Citizenship No."
                                    required
                                    :error="errors.buyer_citizenship_no"
                                >
                                    <input
                                        v-model="form.buyer_citizenship_no"
                                        type="text"
                                        placeholder="e.g. 12-01-75-12345"
                                        :class="
                                            inputClass(
                                                errors.buyer_citizenship_no,
                                            )
                                        "
                                        @blur="
                                            validateField(
                                                'buyer_citizenship_no',
                                            )
                                        "
                                    />
                                </FormField>
                                <FormField
                                    label="Contact No."
                                    required
                                    hint="10 digits starting with 9"
                                    :error="errors.buyer_contact_no"
                                >
                                    <input
                                        v-model="form.buyer_contact_no"
                                        type="tel"
                                        placeholder="98XXXXXXXX"
                                        :class="
                                            inputClass(errors.buyer_contact_no)
                                        "
                                        @blur="
                                            validateField('buyer_contact_no')
                                        "
                                        @input="
                                            form.buyer_contact_no =
                                                form.buyer_contact_no
                                                    .replace(/\D/g, '')
                                                    .slice(0, 10)
                                        "
                                    />
                                </FormField>
                                <div class="md:col-span-2">
                                    <FormField
                                        label="Permanent Address"
                                        required
                                        :error="errors.buyer_permanent_address"
                                    >
                                        <textarea
                                            v-model="
                                                form.buyer_permanent_address
                                            "
                                            rows="2"
                                            placeholder="Enter buyer's permanent address"
                                            :class="
                                                inputClass(
                                                    errors.buyer_permanent_address,
                                                ) + ' resize-none'
                                            "
                                            @blur="
                                                validateField(
                                                    'buyer_permanent_address',
                                                )
                                            "
                                        />
                                    </FormField>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ──────────────────────────────────────────────────── -->
                    <!-- STEP 2: PROPERTY DETAILS -->
                    <!-- ──────────────────────────────────────────────────── -->
                    <div
                        v-else-if="currentStep === 1"
                        key="step2"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">
                                Property Details
                            </h2>
                            <p class="mb-8 text-sm text-slate-500">
                                Describe the house/land being sold.
                            </p>

                            <FormField
                                label="Property Type"
                                required
                                :error="errors.property_type"
                                class="mb-8"
                            >
                                <div class="mt-2 flex flex-wrap gap-3">
                                    <label
                                        v-for="opt in propertyTypes"
                                        :key="opt.value"
                                        class="cursor-pointer"
                                    >
                                        <input
                                            type="radio"
                                            :value="opt.value"
                                            v-model="form.property_type"
                                            class="peer sr-only"
                                        />
                                        <span
                                            class="inline-block rounded-full border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition-all peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white peer-checked:shadow-md hover:bg-slate-50"
                                        >
                                            {{ opt.label }}
                                        </span>
                                    </label>
                                </div>
                            </FormField>

                            <div
                                class="grid grid-cols-1 gap-6 md:grid-cols-3"
                            >
                                <FormField
                                    label="District"
                                    required
                                    :error="errors.district"
                                >
                                    <input
                                        v-model="form.district"
                                        type="text"
                                        :class="inputClass(errors.district)"
                                        @blur="validateField('district')"
                                    />
                                </FormField>
                                <FormField
                                    label="Municipality / Rural Municipality"
                                    required
                                    :error="errors.municipality"
                                >
                                    <input
                                        v-model="form.municipality"
                                        type="text"
                                        :class="
                                            inputClass(errors.municipality)
                                        "
                                        @blur="validateField('municipality')"
                                    />
                                </FormField>
                                <FormField
                                    label="Ward No."
                                    required
                                    :error="errors.ward_no"
                                >
                                    <input
                                        v-model="form.ward_no"
                                        type="text"
                                        :class="inputClass(errors.ward_no)"
                                        @blur="validateField('ward_no')"
                                        @input="
                                            form.ward_no = form.ward_no.replace(
                                                /\D/g,
                                                '',
                                            )
                                        "
                                    />
                                </FormField>
                                <FormField
                                    label="Kitta (Parcel) No."
                                    required
                                    :error="errors.kitta_no"
                                >
                                    <input
                                        v-model="form.kitta_no"
                                        type="text"
                                        :class="inputClass(errors.kitta_no)"
                                        @blur="validateField('kitta_no')"
                                    />
                                </FormField>
                                <FormField
                                    label="Area"
                                    required
                                    :error="errors.area"
                                    hint="e.g. 4 aana, 500 sqft"
                                >
                                    <input
                                        v-model="form.area"
                                        type="text"
                                        :class="inputClass(errors.area)"
                                        @blur="validateField('area')"
                                    />
                                </FormField>
                            </div>

                            <div class="mt-6">
                                <FormField
                                    label="House Description (if any)"
                                    :error="errors.house_description"
                                >
                                    <textarea
                                        v-model="form.house_description"
                                        rows="2"
                                        placeholder="Describe the house structure, if applicable"
                                        :class="
                                            inputClass(
                                                errors.house_description,
                                            ) + ' resize-none'
                                        "
                                    />
                                </FormField>
                            </div>

                            <div class="mt-8 border-t border-slate-100 pt-8">
                                <h3
                                    class="mb-6 flex items-center gap-2 text-base font-bold text-slate-800"
                                >
                                    <svg
                                        class="h-5 w-5 text-emerald-500"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"
                                        />
                                    </svg>
                                    Boundaries
                                    <span
                                        class="text-xs font-normal text-slate-400 normal-case"
                                        >/ चार किल्ला</span
                                    >
                                </h3>
                                <div
                                    class="grid grid-cols-1 gap-6 md:grid-cols-2"
                                >
                                    <FormField
                                        label="East"
                                        :required="form.property_type === 'land'"
                                        :error="errors.boundary_east"
                                    >
                                        <input
                                            v-model="form.boundary_east"
                                            type="text"
                                            :class="
                                                inputClass(errors.boundary_east)
                                            "
                                            @blur="
                                                validateField('boundary_east')
                                            "
                                        />
                                    </FormField>
                                    <FormField
                                        label="West"
                                        :required="form.property_type === 'land'"
                                        :error="errors.boundary_west"
                                    >
                                        <input
                                            v-model="form.boundary_west"
                                            type="text"
                                            :class="
                                                inputClass(errors.boundary_west)
                                            "
                                            @blur="
                                                validateField('boundary_west')
                                            "
                                        />
                                    </FormField>
                                    <FormField
                                        label="North"
                                        :required="form.property_type === 'land'"
                                        :error="errors.boundary_north"
                                    >
                                        <input
                                            v-model="form.boundary_north"
                                            type="text"
                                            :class="
                                                inputClass(
                                                    errors.boundary_north,
                                                )
                                            "
                                            @blur="
                                                validateField('boundary_north')
                                            "
                                        />
                                    </FormField>
                                    <FormField
                                        label="South"
                                        :required="form.property_type === 'land'"
                                        :error="errors.boundary_south"
                                    >
                                        <input
                                            v-model="form.boundary_south"
                                            type="text"
                                            :class="
                                                inputClass(
                                                    errors.boundary_south,
                                                )
                                            "
                                            @blur="
                                                validateField('boundary_south')
                                            "
                                        />
                                    </FormField>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ──────────────────────────────────────────────────── -->
                    <!-- STEP 3: PURCHASE PRICE & PAYMENT TERMS -->
                    <!-- ──────────────────────────────────────────────────── -->
                    <div
                        v-else-if="currentStep === 2"
                        key="step3"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">
                                Purchase Price &amp; Payment Terms
                            </h2>
                            <p class="mb-8 text-sm text-slate-500">
                                Specify the agreed price and payment schedule.
                            </p>

                            <div
                                class="mb-8 rounded-2xl border border-slate-100 bg-slate-50 p-6"
                            >
                                <div
                                    class="grid grid-cols-1 gap-6 md:grid-cols-2"
                                >
                                    <FormField
                                        label="Total Purchase Price"
                                        required
                                        :error="errors.total_price"
                                    >
                                        <div class="relative">
                                            <span
                                                class="absolute top-1/2 left-4 -translate-y-1/2 font-bold text-slate-400"
                                                >NPR</span
                                            >
                                            <input
                                                v-model.number="
                                                    form.total_price
                                                "
                                                type="number"
                                                min="0"
                                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pl-14 font-medium text-slate-800 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                                                placeholder="0.00"
                                                @blur="
                                                    validateField(
                                                        'total_price',
                                                    )
                                                "
                                            />
                                        </div>
                                    </FormField>
                                    <FormField
                                        label="Amount in Words"
                                        required
                                        :error="errors.total_price_words"
                                    >
                                        <input
                                            v-model="form.total_price_words"
                                            type="text"
                                            placeholder="e.g. Fifty Lakh Rupees Only"
                                            :class="
                                                inputClass(
                                                    errors.total_price_words,
                                                )
                                            "
                                            @blur="
                                                validateField(
                                                    'total_price_words',
                                                )
                                            "
                                        />
                                    </FormField>
                                </div>
                            </div>

                            <div
                                class="grid grid-cols-1 gap-6 md:grid-cols-3"
                            >
                                <FormField
                                    label="Advance Payment"
                                    :error="errors.advance_payment"
                                >
                                    <div class="relative">
                                        <span
                                            class="absolute top-1/2 left-4 -translate-y-1/2 font-bold text-slate-400"
                                            >NPR</span
                                        >
                                        <input
                                            v-model.number="
                                                form.advance_payment
                                            "
                                            type="number"
                                            min="0"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pl-14 font-medium text-slate-800 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                                            placeholder="0.00"
                                        />
                                    </div>
                                </FormField>
                                <FormField
                                    label="Balance Payment"
                                    :error="errors.balance_payment"
                                >
                                    <div class="relative">
                                        <span
                                            class="absolute top-1/2 left-4 -translate-y-1/2 font-bold text-slate-400"
                                            >NPR</span
                                        >
                                        <input
                                            v-model.number="
                                                form.balance_payment
                                            "
                                            type="number"
                                            min="0"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pl-14 font-medium text-slate-800 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                                            placeholder="0.00"
                                        />
                                    </div>
                                </FormField>
                                <FormField
                                    label="Final Payment Date"
                                    :error="errors.final_payment_date"
                                >
                                    <input
                                        v-model="form.final_payment_date"
                                        type="date"
                                        :class="
                                            inputClass(
                                                errors.final_payment_date,
                                            )
                                        "
                                    />
                                </FormField>
                            </div>

                            <div
                                class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-5 text-sm leading-relaxed text-slate-600"
                            >
                                <strong class="text-slate-800"
                                    >Transfer of Ownership —</strong
                                >
                                Upon receipt of the full purchase price, the
                                Seller shall appear before the concerned Land
                                Revenue Office (Malpot Office) and complete the
                                registration and transfer of ownership in
                                accordance with the prevailing laws of Nepal.
                            </div>
                        </div>
                    </div>

                    <!-- ──────────────────────────────────────────────────── -->
                    <!-- STEP 4: DECLARATIONS & SIGNATURES -->
                    <!-- ──────────────────────────────────────────────────── -->
                    <div
                        v-else-if="currentStep === 3"
                        key="step4"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">
                                Declarations &amp; Signatures
                            </h2>
                            <p class="mb-8 text-sm text-slate-500">
                                Both parties must declare and sign to proceed.
                            </p>

                            <div
                                class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 p-6 text-sm leading-relaxed text-slate-700 shadow-inner"
                            >
                                <p class="mb-2 font-bold text-slate-800">
                                    Seller's Declaration
                                </p>
                                <p>
                                    The Seller declares that the property is
                                    under lawful ownership, is free from
                                    mortgages, liens, encumbrances, disputes,
                                    or legal claims (unless disclosed), and
                                    that the Seller has full legal authority
                                    to sell the property.
                                </p>
                            </div>

                            <label
                                class="group mb-4 flex cursor-pointer items-start gap-4 rounded-2xl border p-4 transition-all"
                                :class="
                                    errors.seller_declaration_agreed
                                        ? 'border-red-300 bg-red-50'
                                        : 'border-slate-200 hover:bg-slate-50'
                                "
                            >
                                <div class="flex h-6 items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.seller_declaration_agreed"
                                        class="h-6 w-6 cursor-pointer rounded border-slate-300 text-emerald-600 transition-all focus:ring-emerald-500"
                                    />
                                </div>
                                <span class="text-sm font-bold text-slate-800">
                                    Seller agrees to the declaration
                                </span>
                            </label>

                            <FormField
                                label="Seller's Signature Date"
                                required
                                :error="errors.seller_signature_date"
                                class="mb-4"
                            >
                                <input
                                    v-model="form.seller_signature_date"
                                    type="date"
                                    :max="today"
                                    :class="
                                        inputClass(errors.seller_signature_date)
                                    "
                                />
                            </FormField>
                            <div class="mb-8">
                                <SignatureUpload
                                    v-model="form.seller_signature"
                                    label="Seller's Scanned Signature"
                                    required
                                    :error="errors.seller_signature"
                                    @update:model-value="
                                        validateField('seller_signature')
                                    "
                                />
                            </div>

                            <div
                                class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 p-6 text-sm leading-relaxed text-slate-700 shadow-inner"
                            >
                                <p class="mb-2 font-bold text-slate-800">
                                    Buyer's Declaration
                                </p>
                                <p>
                                    The Buyer confirms that the property has
                                    been inspected and is accepted in its
                                    present condition.
                                </p>
                            </div>

                            <label
                                class="group mb-4 flex cursor-pointer items-start gap-4 rounded-2xl border p-4 transition-all"
                                :class="
                                    errors.buyer_declaration_agreed
                                        ? 'border-red-300 bg-red-50'
                                        : 'border-slate-200 hover:bg-slate-50'
                                "
                            >
                                <div class="flex h-6 items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.buyer_declaration_agreed"
                                        class="h-6 w-6 cursor-pointer rounded border-slate-300 text-emerald-600 transition-all focus:ring-emerald-500"
                                    />
                                </div>
                                <span class="text-sm font-bold text-slate-800">
                                    Buyer agrees to the declaration
                                </span>
                            </label>

                            <FormField
                                label="Buyer's Signature Date"
                                required
                                :error="errors.buyer_signature_date"
                                class="mb-4"
                            >
                                <input
                                    v-model="form.buyer_signature_date"
                                    type="date"
                                    :max="today"
                                    :class="
                                        inputClass(errors.buyer_signature_date)
                                    "
                                />
                            </FormField>
                            <SignatureUpload
                                v-model="form.buyer_signature"
                                label="Buyer's Scanned Signature"
                                required
                                :error="errors.buyer_signature"
                                @update:model-value="
                                    validateField('buyer_signature')
                                "
                            />
                        </div>
                    </div>

                    <!-- ──────────────────────────────────────────────────── -->
                    <!-- STEP 5: WITNESSES & FINALIZE -->
                    <!-- ──────────────────────────────────────────────────── -->
                    <div
                        v-else-if="currentStep === 4"
                        key="step5"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">
                                Witnesses &amp; Finalization
                            </h2>
                            <p class="mb-8 text-sm text-slate-500">
                                Add witnesses and confirm where the agreement
                                is being made.
                            </p>

                            <div
                                class="mb-8 rounded-2xl border border-slate-200 bg-slate-50 p-5 text-xs leading-relaxed text-slate-600"
                            >
                                <p class="mb-2">
                                    <strong class="text-slate-800"
                                        >Taxes &amp; Fees —</strong
                                    >
                                    All government taxes, registration fees,
                                    transfer charges, and other expenses shall
                                    be borne by the parties as mutually
                                    agreed.
                                </p>
                                <p class="mb-2">
                                    <strong class="text-slate-800"
                                        >Breach of Agreement —</strong
                                    >
                                    If either party breaches this Agreement,
                                    the non-defaulting party shall be entitled
                                    to claim damages and seek legal remedies
                                    under the prevailing laws of Nepal.
                                </p>
                                <p class="mb-2">
                                    <strong class="text-slate-800"
                                        >Dispute Resolution —</strong
                                    >
                                    Any dispute shall first be settled through
                                    mutual negotiation; if unresolved, it
                                    shall be submitted to the competent court
                                    of Nepal.
                                </p>
                                <p>
                                    <strong class="text-slate-800"
                                        >Governing Law —</strong
                                    >
                                    This Agreement shall be governed by and
                                    construed in accordance with the
                                    prevailing laws of Nepal.
                                </p>
                            </div>

                            <div class="mb-8">
                                <h3
                                    class="mb-4 flex items-center gap-2 text-base font-bold text-slate-800"
                                >
                                    <span
                                        class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-xs text-emerald-700"
                                        >1</span
                                    >
                                    Witness One
                                    <span
                                        class="text-xs font-normal text-slate-400"
                                        >/ साक्षी १</span
                                    >
                                </h3>
                                <div
                                    class="grid grid-cols-1 gap-6 md:grid-cols-2"
                                >
                                    <FormField
                                        label="Full Name"
                                        :error="errors.witness1_name"
                                    >
                                        <input
                                            v-model="form.witness1_name"
                                            type="text"
                                            :class="
                                                inputClass(
                                                    errors.witness1_name,
                                                )
                                            "
                                        />
                                    </FormField>
                                    <FormField
                                        label="Citizenship No."
                                        :error="errors.witness1_citizenship_no"
                                    >
                                        <input
                                            v-model="
                                                form.witness1_citizenship_no
                                            "
                                            type="text"
                                            :class="
                                                inputClass(
                                                    errors.witness1_citizenship_no,
                                                )
                                            "
                                            @blur="
                                                validateField(
                                                    'witness1_citizenship_no',
                                                )
                                            "
                                        />
                                    </FormField>
                                    <div class="md:col-span-2">
                                        <SignatureUpload
                                            v-model="form.witness1_signature"
                                            label="Witness 1 Scanned Signature"
                                            :error="errors.witness1_signature"
                                            hint="Required if Witness 1 name is filled"
                                            @update:model-value="
                                                validateField(
                                                    'witness1_signature',
                                                )
                                            "
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h3
                                    class="mb-4 flex items-center gap-2 text-base font-bold text-slate-800"
                                >
                                    <span
                                        class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-xs text-emerald-700"
                                        >2</span
                                    >
                                    Witness Two
                                    <span
                                        class="text-xs font-normal text-slate-400"
                                        >/ साक्षी २</span
                                    >
                                </h3>
                                <div
                                    class="grid grid-cols-1 gap-6 md:grid-cols-2"
                                >
                                    <FormField
                                        label="Full Name"
                                        :error="errors.witness2_name"
                                    >
                                        <input
                                            v-model="form.witness2_name"
                                            type="text"
                                            :class="
                                                inputClass(
                                                    errors.witness2_name,
                                                )
                                            "
                                        />
                                    </FormField>
                                    <FormField
                                        label="Citizenship No."
                                        :error="errors.witness2_citizenship_no"
                                    >
                                        <input
                                            v-model="
                                                form.witness2_citizenship_no
                                            "
                                            type="text"
                                            :class="
                                                inputClass(
                                                    errors.witness2_citizenship_no,
                                                )
                                            "
                                            @blur="
                                                validateField(
                                                    'witness2_citizenship_no',
                                                )
                                            "
                                        />
                                    </FormField>
                                    <div class="md:col-span-2">
                                        <SignatureUpload
                                            v-model="form.witness2_signature"
                                            label="Witness 2 Scanned Signature"
                                            :error="errors.witness2_signature"
                                            hint="Required if Witness 2 name is filled"
                                            @update:model-value="
                                                validateField(
                                                    'witness2_signature',
                                                )
                                            "
                                        />
                                    </div>
                                </div>
                            </div>

                            <div
                                class="grid grid-cols-1 gap-6 border-t border-slate-100 pt-8 md:grid-cols-2"
                            >
                                <FormField
                                    label="Place of Agreement"
                                    required
                                    :error="errors.place"
                                >
                                    <input
                                        v-model="form.place"
                                        type="text"
                                        placeholder="e.g. Kathmandu"
                                        :class="inputClass(errors.place)"
                                        @blur="validateField('place')"
                                    />
                                </FormField>
                                <FormField
                                    label="Date of Agreement"
                                    required
                                    :error="errors.agreement_date"
                                >
                                    <input
                                        v-model="form.agreement_date"
                                        type="date"
                                        :max="today"
                                        :class="
                                            inputClass(errors.agreement_date)
                                        "
                                        @blur="
                                            validateField('agreement_date')
                                        "
                                    />
                                </FormField>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- ──────────────────────────────────────────────────────── -->
                <!-- BOTTOM NAVIGATION BAR -->
                <!-- ──────────────────────────────────────────────────────── -->
                <div class="mt-8 flex items-center justify-between">
                    <button
                        type="button"
                        @click="prevStep"
                        v-if="currentStep > 0"
                        class="flex items-center gap-2 rounded-xl border border-transparent px-6 py-3.5 font-bold text-slate-500 shadow-sm transition-all hover:border-slate-200 hover:bg-white hover:text-slate-800"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                        Back
                    </button>
                    <div v-else></div>
                    <!-- Spacer -->

                    <button
                        type="submit"
                        :disabled="submitting"
                        class="group relative flex items-center gap-3 overflow-hidden rounded-2xl px-10 py-3.5 font-bold text-white shadow-lg shadow-emerald-500/30 transition-all disabled:cursor-not-allowed disabled:opacity-70"
                        :class="
                            currentStep === steps.length - 1
                                ? 'bg-slate-900 hover:bg-slate-800 hover:shadow-slate-900/30'
                                : 'bg-emerald-500 hover:bg-emerald-600'
                        "
                    >
                        <div
                            class="absolute inset-0 w-full translate-x-[-100%] bg-white/20 transition-transform duration-700 ease-in-out group-hover:translate-x-[100%]"
                        ></div>
                        <span class="relative z-10">
                            {{
                                submitting
                                    ? 'Processing...'
                                    : currentStep === steps.length - 1
                                      ? 'Submit Agreement'
                                      : 'Continue'
                            }}
                        </span>
                        <svg
                            v-if="submitting"
                            class="relative z-10 h-5 w-5 animate-spin"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                            />
                        </svg>
                        <svg
                            v-else-if="currentStep < steps.length - 1"
                            class="relative z-10 h-5 w-5 transition-transform group-hover:translate-x-1"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                        <svg
                            v-else
                            class="relative z-10 h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
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

// ── Form State ─────────────────────────────────────────────────────────────
const emptyForm = () => ({
    // First Party (Seller)
    seller_full_name: '',
    seller_father_mother_name: '',
    seller_citizenship_no: '',
    seller_permanent_address: '',
    seller_contact_no: '',
    // Second Party (Buyer)
    buyer_full_name: '',
    buyer_father_mother_name: '',
    buyer_citizenship_no: '',
    buyer_permanent_address: '',
    buyer_contact_no: '',
    // Property Details
    property_type: '',
    district: '',
    municipality: '',
    ward_no: '',
    kitta_no: '',
    area: '',
    house_description: '',
    boundary_east: '',
    boundary_west: '',
    boundary_north: '',
    boundary_south: '',
    // Purchase Price
    total_price: null as number | null,
    total_price_words: '',
    // Payment Terms
    advance_payment: null as number | null,
    balance_payment: null as number | null,
    final_payment_date: '',
    // Declarations & Signatures
    seller_declaration_agreed: false,
    seller_signature_date: new Date().toISOString().split('T')[0],
    seller_signature: null as File | null,
    buyer_declaration_agreed: false,
    buyer_signature_date: new Date().toISOString().split('T')[0],
    buyer_signature: null as File | null,
    // Witnesses
    witness1_name: '',
    witness1_citizenship_no: '',
    witness1_signature: null as File | null,
    witness2_name: '',
    witness2_citizenship_no: '',
    witness2_signature: null as File | null,
    // Finalization
    place: '',
    agreement_date: new Date().toISOString().split('T')[0],
});

const form = reactive(emptyForm());
const errors = reactive<Record<string, string>>({});

// ── Wizard State ───────────────────────────────────────────────────────────
const steps = [
    {
        title: 'Parties',
        fields: [
            'seller_full_name',
            'seller_citizenship_no',
            'seller_permanent_address',
            'seller_contact_no',
            'buyer_full_name',
            'buyer_citizenship_no',
            'buyer_permanent_address',
            'buyer_contact_no',
        ],
    },
    {
        title: 'Property',
        fields: [
            'property_type',
            'district',
            'municipality',
            'ward_no',
            'kitta_no',
            'area',
            'boundary_east',
            'boundary_west',
            'boundary_north',
            'boundary_south',
        ],
    },
    { title: 'Price', fields: ['total_price', 'total_price_words', 'advance_payment', 'balance_payment'] },
    {
        title: 'Declare',
        fields: [
            'seller_declaration_agreed',
            'buyer_declaration_agreed',
            'seller_signature',
            'buyer_signature',
            'seller_signature_date',
            'buyer_signature_date',
        ],
    },
    {
        title: 'Finalize',
        fields: [
            'place',
            'agreement_date',
            'witness1_name',
            'witness1_citizenship_no',
            'witness1_signature',
            'witness2_name',
            'witness2_citizenship_no',
            'witness2_signature',
        ],
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
const agreementNo = ref('');
const agreementId = ref<number | null>(null);

const today = new Date().toISOString().split('T')[0];

// ── Static Data ────────────────────────────────────────────────────────────
const propertyTypes = [
    { value: 'land', label: 'Land' },
    { value: 'house', label: 'House' },
];

// ── Helpers ────────────────────────────────────────────────────────────────
const inputClass = (error?: string) =>
    `w-full px-4 py-3 rounded-xl border text-slate-800 font-medium transition-all duration-200 focus:outline-none focus:ring-4 bg-white ${
        error
            ? 'border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-500/10'
            : 'border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/10 hover:border-slate-300'
    }`;

// ── Validation ─────────────────────────────────────────────────────────────
const NAME_RE = /^[\p{L}][\p{L}\s.\-']*$/u;
const CITIZENSHIP_RE = /^[A-Za-z0-9][A-Za-z0-9\-\/]*$/;
const LOCATION_RE = /^[\p{L}][\p{L}\s.\-]*$/u;
const WORDS_RE = /^[\p{L}][\p{L}\s,\-]*$/u;
const ALLOWED_IMAGE_TYPES = [
    'image/jpeg',
    'image/jpg',
    'image/png',
    'image/webp',
];

function validateImage(file: unknown, required: boolean, label: string) {
    if (!file) return required ? `Please upload ${label}` : true;
    if (!(file instanceof File)) return `Please upload ${label}`;
    if (!ALLOWED_IMAGE_TYPES.includes(file.type))
        return 'Signature must be JPG, PNG or WEBP';
    if (file.size > 2 * 1024 * 1024)
        return 'Signature image must be under 2 MB';
    return true;
}

const rules: Record<string, (v: any) => string | true> = {
    seller_full_name: (v) =>
        !v
            ? "Seller's full name is required"
            : v.length < 2
              ? 'Name must be at least 2 characters'
              : !NAME_RE.test(v)
                ? 'Name may contain letters, spaces, hyphen, apostrophe and period only'
                : true,
    seller_father_mother_name: (v) =>
        !v
            ? true
            : !NAME_RE.test(v)
              ? "Father's/Mother's name format is invalid"
              : true,
    seller_citizenship_no: (v) =>
        !v
            ? "Seller's citizenship number is required"
            : !CITIZENSHIP_RE.test(v)
              ? 'Citizenship number format is invalid'
              : true,
    seller_permanent_address: (v) =>
        !v
            ? "Seller's permanent address is required"
            : v.trim().length < 8
              ? 'Address must be at least 8 characters'
              : true,
    seller_contact_no: (v) =>
        !v || !/^9[0-9]{9}$/.test(v)
            ? 'Enter a valid 10-digit mobile number starting with 9'
            : true,
    buyer_full_name: (v) =>
        !v
            ? "Buyer's full name is required"
            : v.length < 2
              ? 'Name must be at least 2 characters'
              : !NAME_RE.test(v)
                ? 'Name may contain letters, spaces, hyphen, apostrophe and period only'
                : true,
    buyer_father_mother_name: (v) =>
        !v
            ? true
            : !NAME_RE.test(v)
              ? "Father's/Mother's name format is invalid"
              : true,
    buyer_citizenship_no: (v) =>
        !v
            ? "Buyer's citizenship number is required"
            : !CITIZENSHIP_RE.test(v)
              ? 'Citizenship number format is invalid'
              : v === form.seller_citizenship_no
                ? 'Buyer citizenship number must be different from the seller'
                : true,
    buyer_permanent_address: (v) =>
        !v
            ? "Buyer's permanent address is required"
            : v.trim().length < 8
              ? 'Address must be at least 8 characters'
              : true,
    buyer_contact_no: (v) =>
        !v || !/^9[0-9]{9}$/.test(v)
            ? 'Enter a valid 10-digit mobile number starting with 9'
            : true,
    property_type: (v) => (!v ? 'Please select property type' : true),
    district: (v) =>
        !v
            ? 'District is required'
            : !LOCATION_RE.test(v)
              ? 'District may contain letters and spaces only'
              : true,
    municipality: (v) =>
        !v
            ? 'Municipality is required'
            : !LOCATION_RE.test(v)
              ? 'Municipality may contain letters and spaces only'
              : true,
    ward_no: (v) => {
        if (!v) return 'Ward No is required';
        const n = Number(v);
        return Number.isInteger(n) && n >= 1 && n <= 33
            ? true
            : 'Ward number must be between 1 and 33';
    },
    kitta_no: (v) =>
        !v
            ? 'Kitta No is required'
            : !/^[A-Za-z0-9\-\/]+$/.test(v)
              ? 'Kitta number may contain letters, digits, hyphen and slash only'
              : true,
    area: (v) => (!v ? 'Area is required' : true),
    boundary_east: (v) =>
        form.property_type === 'land' && !v
            ? 'Eastern boundary is required for land'
            : true,
    boundary_west: (v) =>
        form.property_type === 'land' && !v
            ? 'Western boundary is required for land'
            : true,
    boundary_north: (v) =>
        form.property_type === 'land' && !v
            ? 'Northern boundary is required for land'
            : true,
    boundary_south: (v) =>
        form.property_type === 'land' && !v
            ? 'Southern boundary is required for land'
            : true,
    total_price: (v) =>
        v === null || v === '' || Number(v) <= 0
            ? 'Total purchase price must be greater than zero'
            : true,
    total_price_words: (v) =>
        !v
            ? 'Please spell out the amount in words'
            : v.trim().length < 5
              ? 'Amount in words is too short'
              : !WORDS_RE.test(v)
                ? 'Amount in words may contain letters, spaces, commas and hyphens only'
                : true,
    advance_payment: (v) => {
        if (v === null || v === undefined || v === '') return true;
        if (Number(v) < 0) return 'Advance payment cannot be negative';
        if (form.total_price && Number(v) > Number(form.total_price))
            return 'Advance cannot exceed the total purchase price';
        return true;
    },
    balance_payment: (v) => {
        if (v === null || v === undefined || v === '') return true;
        if (Number(v) < 0) return 'Balance payment cannot be negative';
        if (form.total_price && Number(v) > Number(form.total_price))
            return 'Balance cannot exceed the total purchase price';
        if (
            form.advance_payment !== null &&
            form.total_price &&
            Math.abs(
                Number(form.advance_payment) +
                    Number(v) -
                    Number(form.total_price),
            ) > 0.01
        )
            return 'Advance plus balance must equal the total purchase price';
        return true;
    },
    seller_declaration_agreed: (v) =>
        !v ? 'The Seller must agree to the declaration' : true,
    buyer_declaration_agreed: (v) =>
        !v ? 'The Buyer must agree to the declaration' : true,
    seller_signature: (v) =>
        validateImage(v, true, "the seller's scanned signature"),
    buyer_signature: (v) =>
        validateImage(v, true, "the buyer's scanned signature"),
    seller_signature_date: (v) =>
        !v
            ? "Seller's signature date is required"
            : v > today
              ? 'Date cannot be in the future'
              : true,
    buyer_signature_date: (v) =>
        !v
            ? "Buyer's signature date is required"
            : v > today
              ? 'Date cannot be in the future'
              : true,
    witness1_name: (v) =>
        !v
            ? true
            : !NAME_RE.test(v)
              ? 'Witness name format is invalid'
              : true,
    witness1_citizenship_no: (v) =>
        form.witness1_name && !v
            ? 'Witness 1 citizenship number is required'
            : v && !CITIZENSHIP_RE.test(v)
              ? 'Citizenship number format is invalid'
              : true,
    witness1_signature: (v) =>
        validateImage(
            v,
            Boolean(form.witness1_name),
            "Witness 1's scanned signature",
        ),
    witness2_name: (v) =>
        !v
            ? true
            : !NAME_RE.test(v)
              ? 'Witness name format is invalid'
              : true,
    witness2_citizenship_no: (v) =>
        form.witness2_name && !v
            ? 'Witness 2 citizenship number is required'
            : v && !CITIZENSHIP_RE.test(v)
              ? 'Citizenship number format is invalid'
              : true,
    witness2_signature: (v) =>
        validateImage(
            v,
            Boolean(form.witness2_name),
            "Witness 2's scanned signature",
        ),
    place: (v) =>
        !v
            ? 'Place of agreement is required'
            : !LOCATION_RE.test(v)
              ? 'Place may contain letters and spaces only'
              : true,
    agreement_date: (v) =>
        !v
            ? 'Date of agreement is required'
            : v > today
              ? 'Agreement date cannot be in the future'
              : true,
};

function validateField(field: string) {
    if (rules[field]) {
        const result = rules[field](form[field as keyof typeof form]);
        if (result !== true) {
            errors[field] = result;
            return false;
        }
    }
    delete errors[field];
    return true;
}

function validateCurrentStep() {
    let isValid = true;
    Object.keys(stepErrors).forEach((k) => delete stepErrors[k]);
    globalError.value = false;

    const fieldsToValidate = steps[currentStep.value].fields;
    fieldsToValidate.forEach((field) => {
        if (!validateField(field)) {
            isValid = false;
            stepErrors[field] = errors[field];
        }
    });

    if (!isValid) globalError.value = true;
    return isValid;
}

// ── Navigation & Submission ────────────────────────────────────────────────
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
        if (currentStep.value > highestStepReached.value) {
            highestStepReached.value = currentStep.value;
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
        await submitForm();
    }
}

async function submitForm() {
    submitting.value = true;
    try {
        const csrfToken = (
            document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement
        )?.content;
        const fd = toFormData(form as unknown as Record<string, unknown>);
        const res = await axios.post('/agreement', fd, {
            headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
        });

        agreementNo.value = res.data.agreement_no;
        agreementId.value = res.data.agreement_id;
        submitted.value = true;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (err: any) {
        if (err.response?.status === 422) {
            const fieldErrors = err.response.data.errors;
            Object.entries(fieldErrors).forEach(([key, msgs]) => {
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
    window.scrollTo({ top: 0, behavior: 'smooth' });
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
/* Animations for Step Transitions */
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
