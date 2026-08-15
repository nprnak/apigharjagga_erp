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
                        Application Submitted
                    </h2>
                    <p
                        class="relative z-10 text-lg font-medium text-emerald-100"
                    >
                        सफलतापूर्वक पेश गरियो
                    </p>
                </div>
                <div class="px-8 py-10 text-center">
                    <p
                        class="mb-3 text-xs font-bold tracking-[0.2em] text-slate-400 uppercase"
                    >
                        Your Application Number
                    </p>
                    <div
                        class="mb-8 inline-block rounded-2xl border border-slate-100 bg-slate-50 px-6 py-4 shadow-sm"
                    >
                        <p
                            class="font-mono text-3xl font-black tracking-widest text-slate-800"
                        >
                            {{ applicationNo }}
                        </p>
                    </div>
                    <p
                        class="mx-auto mb-10 max-w-sm text-sm leading-relaxed text-slate-500"
                    >
                        Your property listing application is under review.
                        Download your official document copy below.
                    </p>
                    <div class="flex flex-col gap-4">
                        <a
                            :href="`/property-listing/${listingId}/pdf`"
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
                            Submit Another Property
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
                    AGJ-FRM-001
                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                    Version 1.0
                </div>
                <h1
                    class="mb-4 text-4xl font-extrabold tracking-tight text-slate-900 md:text-5xl"
                >
                    Property Listing Application
                </h1>
                <p class="text-lg font-medium text-slate-500">
                    सम्पत्ति सूचीकरण आवेदन फाराम
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
                <!-- ──────────────────────────────────────────────────────── -->
                <!-- STEP 1: APPLICANT DETAILS -->
                <!-- ──────────────────────────────────────────────────────── -->
                <transition name="fade-slide" mode="out-in">
                    <div
                        v-if="currentStep === 0"
                        key="step1"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">
                                Applicant Details
                            </h2>
                            <p class="mb-8 text-sm text-slate-500">
                                Please provide the primary contact and applicant
                                information.
                            </p>

                            <div
                                class="grid grid-cols-1 gap-x-8 gap-y-6 md:grid-cols-2"
                            >
                                <FormField
                                    label="Full Name (English)"
                                    required
                                    :error="errors.full_name_en"
                                >
                                    <input
                                        v-model="form.full_name_en"
                                        type="text"
                                        placeholder="Enter full name"
                                        :class="inputClass(errors.full_name_en)"
                                        @blur="validateField('full_name_en')"
                                    />
                                </FormField>

                                <FormField
                                    label="Full Name (Nepali) / नेपालीमा"
                                    :error="errors.full_name_np"
                                >
                                    <input
                                        v-model="form.full_name_np"
                                        type="text"
                                        placeholder="नेपालीमा लेख्नुहोस्"
                                        :class="inputClass(errors.full_name_np)"
                                    />
                                </FormField>

                                <FormField
                                    label="Citizenship No."
                                    required
                                    :error="errors.citizenship_no"
                                >
                                    <input
                                        v-model="form.citizenship_no"
                                        type="text"
                                        placeholder="e.g. 12-01-75-12345"
                                        :class="
                                            inputClass(errors.citizenship_no)
                                        "
                                        @blur="validateField('citizenship_no')"
                                    />
                                </FormField>

                                <FormField
                                    label="Date of Birth"
                                    required
                                    :error="errors.date_of_birth"
                                >
                                    <input
                                        v-model="form.date_of_birth"
                                        type="date"
                                        :max="today"
                                        :class="
                                            inputClass(errors.date_of_birth)
                                        "
                                        @blur="validateField('date_of_birth')"
                                    />
                                </FormField>

                                <FormField
                                    label="Mobile No."
                                    required
                                    :error="errors.mobile_no"
                                    hint="10 digits starting with 9"
                                >
                                    <input
                                        v-model="form.mobile_no"
                                        type="tel"
                                        placeholder="98XXXXXXXX"
                                        :class="inputClass(errors.mobile_no)"
                                        @blur="validateField('mobile_no')"
                                        @input="
                                            form.mobile_no = form.mobile_no
                                                .replace(/\D/g, '')
                                                .slice(0, 10)
                                        "
                                    />
                                </FormField>

                                <FormField
                                    label="Email Address"
                                    :error="errors.email"
                                >
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        placeholder="example@email.com"
                                        :class="inputClass(errors.email)"
                                        @blur="validateField('email')"
                                    />
                                </FormField>

                                <div
                                    class="mt-4 grid grid-cols-1 gap-8 border-t border-slate-100 pt-6 md:col-span-2 md:grid-cols-2"
                                >
                                    <FormField
                                        label="Permanent Address"
                                        :error="errors.permanent_address"
                                    >
                                        <textarea
                                            v-model="form.permanent_address"
                                            rows="3"
                                            placeholder="Enter full permanent address"
                                            :class="
                                                inputClass(
                                                    errors.permanent_address,
                                                ) + ' resize-none'
                                            "
                                        />
                                    </FormField>
                                    <FormField
                                        label="Current Address"
                                        :error="errors.current_address"
                                    >
                                        <textarea
                                            v-model="form.current_address"
                                            rows="3"
                                            placeholder="Enter full current address"
                                            :class="
                                                inputClass(
                                                    errors.current_address,
                                                ) + ' resize-none'
                                            "
                                        />
                                    </FormField>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ──────────────────────────────────────────────────────── -->
                    <!-- STEP 2: PROPERTY DETAILS -->
                    <!-- ──────────────────────────────────────────────────────── -->
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
                                Tell us about the property you are listing.
                            </p>

                            <FormField
                                label="Ownership Role"
                                required
                                :error="errors.ownership_role"
                                class="mb-8"
                            >
                                <div
                                    class="mt-2 grid grid-cols-2 gap-4 md:grid-cols-4"
                                >
                                    <label
                                        v-for="opt in ownershipRoles"
                                        :key="opt.value"
                                        class="group relative cursor-pointer"
                                    >
                                        <input
                                            type="radio"
                                            :value="opt.value"
                                            v-model="form.ownership_role"
                                            class="peer sr-only"
                                        />
                                        <div
                                            class="flex h-full flex-col items-center justify-center gap-2 rounded-2xl border-2 border-slate-200 bg-white p-4 text-center transition-all duration-300 group-hover:border-slate-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 peer-checked:shadow-[0_0_0_4px_rgba(16,185,129,0.1)]"
                                        >
                                            <span class="mb-1 text-2xl">{{
                                                opt.icon
                                            }}</span>
                                            <span
                                                class="text-sm font-bold text-slate-700 peer-checked:text-emerald-700"
                                                >{{ opt.label }}</span
                                            >
                                        </div>
                                    </label>
                                </div>
                            </FormField>

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
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                    </svg>
                                    Location Data
                                </h3>
                                <div
                                    class="grid grid-cols-1 gap-6 md:grid-cols-3"
                                >
                                    <FormField
                                        label="Province"
                                        required
                                        :error="errors.province"
                                    >
                                        <input
                                            v-model="form.province"
                                            type="text"
                                            :class="inputClass(errors.province)"
                                            @blur="validateField('province')"
                                        />
                                    </FormField>
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
                                        label="Municipality"
                                        required
                                        :error="errors.municipality"
                                    >
                                        <input
                                            v-model="form.municipality"
                                            type="text"
                                            :class="
                                                inputClass(errors.municipality)
                                            "
                                            @blur="
                                                validateField('municipality')
                                            "
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
                                                form.ward_no =
                                                    form.ward_no.replace(
                                                        /\D/g,
                                                        '',
                                                    )
                                            "
                                        />
                                    </FormField>
                                    <FormField
                                        label="Tole / Locality"
                                        :error="errors.tole"
                                    >
                                        <input
                                            v-model="form.tole"
                                            type="text"
                                            :class="inputClass(errors.tole)"
                                        />
                                    </FormField>
                                    <FormField
                                        label="GPS Coordinates"
                                        :error="errors.gps_location"
                                        hint="e.g. 27.7172, 85.3240"
                                    >
                                        <input
                                            v-model="form.gps_location"
                                            type="text"
                                            placeholder="Lat, Lng"
                                            :class="
                                                inputClass(errors.gps_location)
                                            "
                                            @blur="
                                                validateField('gps_location')
                                            "
                                        />
                                    </FormField>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ──────────────────────────────────────────────────────── -->
                    <!-- STEP 3: PRICING & FEATURES -->
                    <!-- ──────────────────────────────────────────────────────── -->
                    <div
                        v-else-if="currentStep === 2"
                        key="step3"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">
                                Listing Details
                            </h2>
                            <p class="mb-8 text-sm text-slate-500">
                                Set your listing goals, pricing, and highlight
                                features.
                            </p>

                            <FormField
                                label="Purpose of Listing"
                                required
                                :error="errors.purpose_of_listing"
                                class="mb-8"
                            >
                                <div
                                    class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-5"
                                >
                                    <label
                                        v-for="opt in listingPurposes"
                                        :key="opt.value"
                                        class="group relative cursor-pointer text-center"
                                    >
                                        <input
                                            type="radio"
                                            :value="opt.value"
                                            v-model="form.purpose_of_listing"
                                            class="peer sr-only"
                                        />
                                        <div
                                            class="rounded-2xl border-2 border-slate-200 px-2 py-4 transition-all duration-300 group-hover:border-slate-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50"
                                        >
                                            <span class="mb-2 block text-2xl">{{
                                                opt.icon
                                            }}</span>
                                            <span
                                                class="block text-sm font-bold text-slate-700"
                                                >{{ opt.label }}</span
                                            >
                                        </div>
                                    </label>
                                </div>
                            </FormField>

                            <div
                                class="mb-8 rounded-2xl border border-slate-100 bg-slate-50 p-6"
                            >
                                <div
                                    class="grid grid-cols-1 gap-6 md:grid-cols-2"
                                >
                                    <FormField
                                        label="Expected Selling Price"
                                        :error="errors.expected_selling_price"
                                    >
                                        <div class="relative">
                                            <span
                                                class="absolute top-1/2 left-4 -translate-y-1/2 font-bold text-slate-400"
                                                >NPR</span
                                            >
                                            <input
                                                v-model.number="
                                                    form.expected_selling_price
                                                "
                                                type="number"
                                                min="0"
                                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pl-14 font-medium text-slate-800 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                                                placeholder="0.00"
                                                @blur="
                                                    validateField(
                                                        'expected_selling_price',
                                                    )
                                                "
                                            />
                                        </div>
                                    </FormField>
                                    <FormField
                                        label="Rental Amount (per month)"
                                        :error="errors.rental_amount"
                                    >
                                        <div class="relative">
                                            <span
                                                class="absolute top-1/2 left-4 -translate-y-1/2 font-bold text-slate-400"
                                                >NPR</span
                                            >
                                            <input
                                                v-model.number="
                                                    form.rental_amount
                                                "
                                                type="number"
                                                min="0"
                                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pl-14 font-medium text-slate-800 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                                                placeholder="0.00"
                                                @blur="
                                                    validateField(
                                                        'rental_amount',
                                                    )
                                                "
                                            />
                                        </div>
                                    </FormField>
                                </div>
                            </div>

                            <FormField label="Property Features" class="mb-4">
                                <div
                                    class="mt-2 grid grid-cols-2 gap-3 md:grid-cols-3"
                                >
                                    <label
                                        v-for="feat in featureTypes"
                                        :key="feat.value"
                                        class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3.5 transition-all hover:bg-slate-50 [&:has(input:checked)]:border-emerald-500 [&:has(input:checked)]:bg-emerald-50/30"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="feat.value"
                                            v-model="form.property_features"
                                            class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0"
                                        />
                                        <span
                                            class="text-sm font-medium text-slate-700"
                                            >{{ feat.label }}</span
                                        >
                                    </label>
                                </div>
                            </FormField>
                        </div>
                    </div>

                    <!-- ──────────────────────────────────────────────────────── -->
                    <!-- STEP 4: DECLARATION & REVIEW -->
                    <!-- ──────────────────────────────────────────────────────── -->
                    <div
                        v-else-if="currentStep === 3"
                        key="step4"
                        class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
                    >
                        <div class="px-8 py-10 md:px-12">
                            <div
                                class="mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600"
                            >
                                <svg
                                    class="h-8 w-8"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                    />
                                </svg>
                            </div>
                            <h2 class="mb-2 text-2xl font-bold text-slate-800">
                                Final Declaration
                            </h2>
                            <p class="mb-8 text-sm text-slate-500">
                                Please review your agreement and sign to submit
                                the application.
                            </p>

                            <div
                                class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 p-6 text-sm leading-relaxed text-slate-700 shadow-inner"
                            >
                                <p class="mb-4">
                                    I hereby declare that the information
                                    provided in this application is true and
                                    correct to the best of my knowledge. I
                                    confirm that I am the lawful owner or
                                    authorized representative of the property
                                    and authorize
                                    <strong class="text-slate-900"
                                        >Api Ghar Jagga</strong
                                    >
                                    to inspect, market, advertise, and
                                    facilitate the sale, rental, lease, or
                                    transfer of the property in accordance with
                                    the agreed terms and applicable laws.
                                </p>
                                <p
                                    class="border-t border-slate-200 pt-4 text-xs text-slate-500 italic"
                                >
                                    म यस आवेदनमा उल्लेख गरिएका सम्पूर्ण विवरणहरू
                                    सत्य तथा सही रहेको घोषणा गर्दछु।
                                </p>
                            </div>

                            <label
                                class="group flex cursor-pointer items-start gap-4 rounded-2xl border p-4 transition-all"
                                :class="
                                    errors.declaration_agreed
                                        ? 'border-red-300 bg-red-50'
                                        : 'border-slate-200 hover:bg-slate-50'
                                "
                            >
                                <div class="flex h-6 items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.declaration_agreed"
                                        class="h-6 w-6 cursor-pointer rounded border-slate-300 text-emerald-600 transition-all focus:ring-emerald-500"
                                    />
                                </div>
                                <div>
                                    <span
                                        class="mb-1 block text-base font-bold text-slate-800"
                                        >I agree to the declaration</span
                                    >
                                    <span
                                        class="block text-xs font-medium text-slate-500"
                                        >माथिको घोषणामा सहमत छु</span
                                    >
                                </div>
                            </label>
                            <p
                                v-if="errors.declaration_agreed"
                                class="mt-2 text-sm font-medium text-red-600"
                            >
                                {{ errors.declaration_agreed }}
                            </p>

                            <div class="mt-10 border-t border-slate-100 pt-8">
                                <FormField
                                    label="Electronic Signature (Type Full Name)"
                                    required
                                    :error="errors.applicant_name"
                                >
                                    <input
                                        v-model="form.applicant_name"
                                        type="text"
                                        class="w-full border-b-2 border-slate-200 bg-transparent py-4 text-center text-xl font-medium tracking-wide placeholder-slate-300 transition-colors focus:border-emerald-500 focus:outline-none"
                                        placeholder="Type your name to sign"
                                        @blur="validateField('applicant_name')"
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
                                      ? 'Submit Application'
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

// ── Form State ─────────────────────────────────────────────────────────────
const emptyForm = () => ({
    full_name_en: '',
    full_name_np: '',
    citizenship_no: '',
    date_of_birth: '',
    permanent_address: '',
    current_address: '',
    mobile_no: '',
    email: '',
    ownership_role: '',
    property_type: '',
    province: '',
    district: '',
    municipality: '',
    ward_no: '',
    tole: '',
    gps_location: '',
    purpose_of_listing: '',
    expected_selling_price: null as number | null,
    rental_amount: null as number | null,
    property_features: [] as string[],
    declaration_agreed: false,
    applicant_name: '',
});

const form = reactive(emptyForm());
const errors = reactive<Record<string, string>>({});

// ── Wizard State ───────────────────────────────────────────────────────────
const steps = [
    {
        title: 'Applicant',
        fields: ['full_name_en', 'citizenship_no', 'mobile_no'],
    },
    {
        title: 'Property',
        fields: [
            'ownership_role',
            'property_type',
            'province',
            'district',
            'municipality',
            'ward_no',
        ],
    },
    { title: 'Details', fields: ['purpose_of_listing'] },
    { title: 'Review', fields: ['declaration_agreed', 'applicant_name'] },
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
const applicationNo = ref('');
const listingId = ref<number | null>(null);

const today = new Date().toISOString().split('T')[0];

// ── Static Data ────────────────────────────────────────────────────────────
const ownershipRoles = [
    { value: 'self', label: 'Self', icon: '👤' },
    { value: 'family_member', label: 'Family', icon: '👨‍👩‍👧‍👦' },
    { value: 'authorized_representative', label: 'Representative', icon: '🤝' },
    { value: 'company', label: 'Company', icon: '🏢' },
];

const propertyTypes = [
    { value: 'land', label: 'Plot / Land' },
    { value: 'house', label: 'House / Villa' },
    { value: 'apartment', label: 'Apartment' },
    { value: 'commercial_building', label: 'Commercial' },
];

const listingPurposes = [
    { value: 'sale', label: 'Sale', icon: '🏷️' },
    { value: 'rent', label: 'Rent', icon: '🏠' },
    { value: 'lease', label: 'Lease', icon: '📋' },
    { value: 'exchange', label: 'Exchange', icon: '🔄' },
];

const featureTypes = [
    { value: 'corner_plot', label: 'Corner Plot' },
    { value: 'blacktopped_road', label: 'Paved Road' },
    { value: 'drinking_water', label: 'Drinking Water' },
    { value: 'electricity', label: 'Electricity' },
    { value: 'sewer', label: 'Sewerage' },
];

// ── Helpers ────────────────────────────────────────────────────────────────
const inputClass = (error?: string) =>
    `w-full px-4 py-3 rounded-xl border text-slate-800 font-medium transition-all duration-200 focus:outline-none focus:ring-4 bg-white ${
        error
            ? 'border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-500/10'
            : 'border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/10 hover:border-slate-300'
    }`;

// ── Validation ─────────────────────────────────────────────────────────────
const rules: Record<string, (v: any) => string | true> = {
    full_name_en: (v) => (!v ? 'Full name is required' : true),
    citizenship_no: (v) => (!v ? 'Citizenship number is required' : true),
    mobile_no: (v) =>
        !v || !/^9[0-9]{9}$/.test(v)
            ? 'Enter a valid 10-digit mobile number'
            : true,
    ownership_role: (v) => (!v ? 'Please select your role' : true),
    property_type: (v) => (!v ? 'Please select property type' : true),
    province: (v) => (!v ? 'Province is required' : true),
    district: (v) => (!v ? 'District is required' : true),
    municipality: (v) => (!v ? 'Municipality is required' : true),
    ward_no: (v) => (!v ? 'Ward No is required' : true),
    purpose_of_listing: (v) => (!v ? 'Please select listing purpose' : true),
    declaration_agreed: (v) => (!v ? 'You must agree to the terms' : true),
    applicant_name: (v) => (!v ? 'Signature is required' : true),
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
        const res = await axios.post('/property-listing', form, {
            headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
        });

        applicationNo.value = res.data.application_no;
        listingId.value = res.data.listing_id;
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
