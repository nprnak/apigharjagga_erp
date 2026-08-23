<template>
    <div class="agj-root">
        <!-- ================================================================ -->
        <!-- SUCCESS STATE — official registry receipt -->
        <!-- ================================================================ -->
        <div v-if="submitted" class="agj-receipt-wrap">
            <div class="agj-receipt">
                <div class="agj-receipt__head">
                    <div class="agj-receipt__masthead">
                        <span class="agj-eyebrow">Api Ghar Jagga · Registry</span>
                        <span class="agj-eyebrow agj-eyebrow--np">दर्ता प्रमाण</span>
                    </div>
                    <div class="agj-seal" aria-hidden="true">
                        <svg viewBox="0 0 96 96" class="agj-seal__ring">
                            <defs>
                                <path
                                    id="sealArc"
                                    d="M48,48 m-34,0 a34,34 0 1,1 68,0 a34,34 0 1,1 -68,0"
                                />
                            </defs>
                            <circle cx="48" cy="48" r="45" />
                            <circle cx="48" cy="48" r="38" />
                            <text class="agj-seal__text">
                                <textPath href="#sealArc" startOffset="0">
                                    ·  RECEIVED FOR VERIFICATION  ·  प्राप्त
                                    भयो  ·
                                </textPath>
                            </text>
                        </svg>
                        <svg class="agj-seal__check" viewBox="0 0 24 24">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <h2 class="agj-receipt__title">Application Lodged</h2>
                <p class="agj-receipt__np">सम्पत्ति सूचीकरण आवेदन दर्ता भयो</p>

                <div class="agj-receipt__field">
                    <span class="agj-field-label">Application Number</span>
                    <p class="agj-appno">{{ applicationNo }}</p>
                </div>

                <p class="agj-receipt__note">
                    Your property listing is entered in the register and awaits
                    review. Keep the application number for reference and
                    download the official copy below.
                </p>

                <div class="agj-receipt__actions">
                    <a
                        :href="`/property-listing/${listingId}/pdf`"
                        target="_blank"
                        class="agj-btn agj-btn--primary"
                    >
                        <svg class="agj-btn__icon" viewBox="0 0 24 24">
                            <path
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                        Download official copy
                    </a>
                    <button
                        @click="resetForm"
                        type="button"
                        class="agj-btn agj-btn--ghost"
                    >
                        Lodge another property
                    </button>
                </div>
            </div>
        </div>

        <!-- ================================================================ -->
        <!-- FORM STATE — field ledger -->
        <!-- ================================================================ -->
        <div v-else class="agj-page">
            <!-- Masthead -->
            <header class="agj-masthead">
                <div class="agj-masthead__meta">
                    <span class="agj-stamp">AGJ·FRM·001</span>
                    <span class="agj-stamp agj-stamp--muted">Rev. 1.0</span>
                    <span class="agj-masthead__seals">Annex · घरजग्गा</span>
                </div>
                <h1 class="agj-title">Property Listing Application</h1>
                <p class="agj-title__np">सम्पत्ति सूचीकरण आवेदन फाराम</p>
                <div class="agj-rule" aria-hidden="true"></div>
            </header>

            <!-- Survey traverse (step tracker) -->
            <nav class="agj-traverse" aria-label="Application progress">
                <div class="agj-traverse__line">
                    <span
                        class="agj-traverse__line-fill"
                        :style="{ width: progressPercentage + '%' }"
                    ></span>
                </div>
                <ol class="agj-traverse__stations">
                    <li
                        v-for="(step, index) in steps"
                        :key="index"
                        class="agj-station"
                    >
                        <button
                            type="button"
                            @click="goToStep(index)"
                            :disabled="index > highestStepReached"
                            class="agj-station__mark"
                            :class="{
                                'is-active': currentStep === index,
                                'is-done': index < currentStep,
                                'is-reachable':
                                    index <= highestStepReached &&
                                    index > currentStep,
                            }"
                            :aria-current="
                                currentStep === index ? 'step' : undefined
                            "
                        >
                            <svg
                                v-if="index < currentStep"
                                viewBox="0 0 24 24"
                                class="agj-station__check"
                            >
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                            <span v-else>{{ devanagariNum[index] }}</span>
                        </button>
                        <span class="agj-station__label">
                            <span class="agj-station__en">{{ step.title }}</span>
                            <span class="agj-station__np">{{ step.np }}</span>
                        </span>
                    </li>
                </ol>
            </nav>

            <!-- Error ledger -->
            <div v-if="globalError" class="agj-errors" role="alert">
                <div class="agj-errors__mark">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                        />
                    </svg>
                </div>
                <div>
                    <p class="agj-errors__title">
                        Resolve these entries to continue
                    </p>
                    <ul class="agj-errors__list">
                        <li v-for="(msg, field) in stepErrors" :key="field">
                            {{ msg }}
                        </li>
                    </ul>
                </div>
            </div>

            <form
                @submit.prevent="handleNextOrSubmit"
                novalidate
                class="agj-form"
            >
                <transition name="fade-slide" mode="out-in">
                    <!-- ─────────── STEP 1: APPLICANT ─────────── -->
                    <section v-if="currentStep === 0" key="step1" class="agj-card">
                        <div class="agj-card__body">
                            <div class="agj-step-head">
                                <span class="agj-step-head__idx">०१</span>
                                <div>
                                    <h2 class="agj-step-head__title">
                                        Applicant Details
                                    </h2>
                                    <p class="agj-step-head__desc">
                                        Primary contact and applicant
                                        information, as recorded on official
                                        identity documents.
                                    </p>
                                </div>
                            </div>

                            <div class="agj-grid agj-grid--2">
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
                                            inputClass(errors.citizenship_no) +
                                            ' agj-input--mono'
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
                                        :class="inputClass(errors.date_of_birth)"
                                        @blur="validateField('date_of_birth')"
                                    />
                                </FormField>

                                <FormField
                                    label="Father's Name"
                                    :error="errors.father_name"
                                >
                                    <input
                                        v-model="form.father_name"
                                        type="text"
                                        placeholder="Enter father's name"
                                        :class="inputClass(errors.father_name)"
                                    />
                                </FormField>

                                <FormField
                                    label="Grandfather's Name"
                                    :error="errors.grandfather_name"
                                >
                                    <input
                                        v-model="form.grandfather_name"
                                        type="text"
                                        placeholder="Enter grandfather's name"
                                        :class="
                                            inputClass(errors.grandfather_name)
                                        "
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
                                        :class="
                                            inputClass(errors.mobile_no) +
                                            ' agj-input--mono'
                                        "
                                        @blur="validateField('mobile_no')"
                                        @input="
                                            form.mobile_no = form.mobile_no
                                                .replace(/\D/g, '')
                                                .slice(0, 10)
                                        "
                                    />
                                </FormField>

                                <FormField
                                    label="Telephone No."
                                    :error="errors.telephone_no"
                                    hint="Landline, 7–10 digits"
                                >
                                    <input
                                        v-model="form.telephone_no"
                                        type="tel"
                                        placeholder="e.g. 014XXXXXX"
                                        :class="
                                            inputClass(errors.telephone_no) +
                                            ' agj-input--mono'
                                        "
                                        @blur="validateField('telephone_no')"
                                        @input="
                                            form.telephone_no = form.telephone_no
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

                                <FormField
                                    label="Occupation"
                                    :error="errors.occupation"
                                >
                                    <input
                                        v-model="form.occupation"
                                        type="text"
                                        placeholder="Enter occupation"
                                        :class="inputClass(errors.occupation)"
                                    />
                                </FormField>

                                <div class="agj-span-2 agj-subgrid">
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
                                                )
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
                                                )
                                            "
                                        />
                                    </FormField>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- ─────────── STEP 2: PROPERTY ─────────── -->
                    <section
                        v-else-if="currentStep === 1"
                        key="step2"
                        class="agj-card"
                    >
                        <div class="agj-card__body">
                            <div class="agj-step-head">
                                <span class="agj-step-head__idx">०२</span>
                                <div>
                                    <h2 class="agj-step-head__title">
                                        Property Particulars
                                    </h2>
                                    <p class="agj-step-head__desc">
                                        Land, building and location details of
                                        the property being listed.
                                    </p>
                                </div>
                            </div>

                            <FormField
                                label="Ownership Role"
                                required
                                :error="errors.ownership_role"
                                class="agj-block"
                            >
                                <div class="agj-choices agj-choices--4">
                                    <label
                                        v-for="opt in ownershipRoles"
                                        :key="opt.value"
                                        class="agj-choice"
                                    >
                                        <input
                                            type="radio"
                                            :value="opt.value"
                                            v-model="form.ownership_role"
                                            class="agj-choice__input"
                                        />
                                        <span class="agj-choice__tile">
                                            <svg
                                                class="agj-choice__icon"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    v-for="(d, i) in opt.icon"
                                                    :key="i"
                                                    :d="d"
                                                />
                                            </svg>
                                            <span class="agj-choice__label">{{
                                                opt.label
                                            }}</span>
                                        </span>
                                    </label>
                                </div>
                            </FormField>

                            <FormField
                                label="Property Type"
                                required
                                :error="errors.property_type"
                                class="agj-block"
                            >
                                <div class="agj-pills">
                                    <label
                                        v-for="opt in propertyTypes"
                                        :key="opt.value"
                                        class="agj-pill"
                                    >
                                        <input
                                            type="radio"
                                            :value="opt.value"
                                            v-model="form.property_type"
                                            class="agj-pill__input"
                                        />
                                        <span class="agj-pill__label">{{
                                            opt.label
                                        }}</span>
                                    </label>
                                </div>
                                <input
                                    v-if="form.property_type === 'other'"
                                    v-model="form.property_type_other"
                                    type="text"
                                    placeholder="Please specify property type"
                                    :class="
                                        inputClass(errors.property_type_other) +
                                        ' agj-mt'
                                    "
                                    @blur="validateField('property_type_other')"
                                />
                            </FormField>

                            <div class="agj-subhead">
                                <span class="agj-subhead__tag">क</span>
                                <h3>Land Information</h3>
                            </div>
                            <div class="agj-grid agj-grid--3">
                                <FormField
                                    label="Kitta No."
                                    :error="errors.kitta_no"
                                >
                                    <input
                                        v-model="form.kitta_no"
                                        type="text"
                                        :class="
                                            inputClass(errors.kitta_no) +
                                            ' agj-input--mono'
                                        "
                                    />
                                </FormField>
                                <FormField
                                    label="Area"
                                    :error="errors.area"
                                    hint="e.g. 4 aana, 500 sqft"
                                >
                                    <input
                                        v-model="form.area"
                                        type="text"
                                        :class="inputClass(errors.area)"
                                    />
                                </FormField>
                                <FormField
                                    label="Map Sheet No."
                                    :error="errors.map_sheet_no"
                                >
                                    <input
                                        v-model="form.map_sheet_no"
                                        type="text"
                                        :class="
                                            inputClass(errors.map_sheet_no) +
                                            ' agj-input--mono'
                                        "
                                    />
                                </FormField>
                                <FormField
                                    label="Ownership Type"
                                    :error="errors.ownership_type"
                                >
                                    <select
                                        v-model="form.ownership_type"
                                        :class="inputClass(errors.ownership_type)"
                                    >
                                        <option value="">Select</option>
                                        <option value="private">Private</option>
                                        <option value="joint">Joint</option>
                                        <option value="other">Other</option>
                                    </select>
                                </FormField>
                                <FormField
                                    label="Road Access"
                                    :error="errors.road_access"
                                >
                                    <select
                                        v-model="form.road_access"
                                        :class="inputClass(errors.road_access)"
                                    >
                                        <option value="">Select</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </FormField>
                                <FormField
                                    label="Road Width"
                                    :error="errors.road_width"
                                    hint="e.g. 12 ft"
                                >
                                    <input
                                        v-model="form.road_width"
                                        type="text"
                                        :class="inputClass(errors.road_width)"
                                    />
                                </FormField>
                                <FormField
                                    label="Facing Direction"
                                    :error="errors.facing_direction"
                                >
                                    <select
                                        v-model="form.facing_direction"
                                        :class="
                                            inputClass(errors.facing_direction)
                                        "
                                    >
                                        <option value="">Select</option>
                                        <option
                                            v-for="dir in [
                                                'North',
                                                'South',
                                                'East',
                                                'West',
                                                'North-East',
                                                'North-West',
                                                'South-East',
                                                'South-West',
                                            ]"
                                            :key="dir"
                                            :value="dir"
                                        >
                                            {{ dir }}
                                        </option>
                                    </select>
                                </FormField>
                            </div>

                            <div class="agj-subhead">
                                <span class="agj-subhead__tag">ख</span>
                                <h3>Building Details</h3>
                                <span class="agj-subhead__opt"
                                    >if applicable</span
                                >
                            </div>
                            <div class="agj-grid agj-grid--3">
                                <FormField
                                    label="Year of Construction"
                                    :error="errors.year_of_construction"
                                >
                                    <input
                                        v-model.number="
                                            form.year_of_construction
                                        "
                                        type="number"
                                        :max="new Date().getFullYear() + 1"
                                        min="1900"
                                        :class="
                                            inputClass(
                                                errors.year_of_construction,
                                            ) + ' agj-input--mono'
                                        "
                                        @blur="
                                            validateField('year_of_construction')
                                        "
                                    />
                                </FormField>
                                <FormField
                                    label="No. of Floors"
                                    :error="errors.no_of_floors"
                                >
                                    <input
                                        v-model.number="form.no_of_floors"
                                        type="number"
                                        min="1"
                                        :class="
                                            inputClass(errors.no_of_floors) +
                                            ' agj-input--mono'
                                        "
                                    />
                                </FormField>
                                <FormField
                                    label="Covered Area"
                                    :error="errors.covered_area"
                                    hint="e.g. 1200 sqft"
                                >
                                    <input
                                        v-model="form.covered_area"
                                        type="text"
                                        :class="inputClass(errors.covered_area)"
                                    />
                                </FormField>
                                <FormField
                                    label="Structure Type"
                                    :error="errors.structure_type"
                                >
                                    <select
                                        v-model="form.structure_type"
                                        :class="
                                            inputClass(errors.structure_type)
                                        "
                                    >
                                        <option value="">Select</option>
                                        <option value="RCC">RCC</option>
                                        <option value="Load Bearing">
                                            Load Bearing
                                        </option>
                                        <option value="Steel">Steel</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </FormField>
                                <FormField
                                    label="Roof Type"
                                    :error="errors.roof_type"
                                >
                                    <input
                                        v-model="form.roof_type"
                                        type="text"
                                        :class="inputClass(errors.roof_type)"
                                    />
                                </FormField>
                                <FormField
                                    label="Parking"
                                    :error="errors.parking"
                                >
                                    <input
                                        v-model="form.parking"
                                        type="text"
                                        placeholder="e.g. 2 cars"
                                        :class="inputClass(errors.parking)"
                                    />
                                </FormField>
                                <FormField
                                    label="Water Supply"
                                    :error="errors.water_supply"
                                >
                                    <input
                                        v-model="form.water_supply"
                                        type="text"
                                        :class="inputClass(errors.water_supply)"
                                    />
                                </FormField>
                                <FormField
                                    label="Electricity"
                                    :error="errors.electricity"
                                >
                                    <input
                                        v-model="form.electricity"
                                        type="text"
                                        :class="inputClass(errors.electricity)"
                                    />
                                </FormField>
                                <FormField
                                    label="Internet"
                                    :error="errors.internet"
                                >
                                    <input
                                        v-model="form.internet"
                                        type="text"
                                        :class="inputClass(errors.internet)"
                                    />
                                </FormField>
                                <FormField
                                    label="Drainage"
                                    :error="errors.drainage"
                                >
                                    <input
                                        v-model="form.drainage"
                                        type="text"
                                        :class="inputClass(errors.drainage)"
                                    />
                                </FormField>
                            </div>

                            <div class="agj-subhead">
                                <span class="agj-subhead__tag">ग</span>
                                <h3>Location Data</h3>
                            </div>
                            <div class="agj-grid agj-grid--3">
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
                                        :class="inputClass(errors.municipality)"
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
                                        :class="
                                            inputClass(errors.ward_no) +
                                            ' agj-input--mono'
                                        "
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
                                            inputClass(errors.gps_location) +
                                            ' agj-input--mono'
                                        "
                                        @blur="validateField('gps_location')"
                                    />
                                </FormField>
                            </div>
                        </div>
                    </section>

                    <!-- ─────────── STEP 3: LISTING DETAILS ─────────── -->
                    <section
                        v-else-if="currentStep === 2"
                        key="step3"
                        class="agj-card"
                    >
                        <div class="agj-card__body">
                            <div class="agj-step-head">
                                <span class="agj-step-head__idx">०३</span>
                                <div>
                                    <h2 class="agj-step-head__title">
                                        Listing Terms
                                    </h2>
                                    <p class="agj-step-head__desc">
                                        The intent of the listing, pricing and
                                        supporting documents.
                                    </p>
                                </div>
                            </div>

                            <FormField
                                label="Purpose of Listing"
                                required
                                :error="errors.purpose_of_listing"
                                class="agj-block"
                            >
                                <div class="agj-choices agj-choices--6">
                                    <label
                                        v-for="opt in listingPurposes"
                                        :key="opt.value"
                                        class="agj-choice"
                                    >
                                        <input
                                            type="radio"
                                            :value="opt.value"
                                            v-model="form.purpose_of_listing"
                                            class="agj-choice__input"
                                        />
                                        <span class="agj-choice__tile">
                                            <svg
                                                class="agj-choice__icon"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    v-for="(d, i) in opt.icon"
                                                    :key="i"
                                                    :d="d"
                                                />
                                            </svg>
                                            <span class="agj-choice__label">{{
                                                opt.label
                                            }}</span>
                                        </span>
                                    </label>
                                </div>
                                <input
                                    v-if="form.purpose_of_listing === 'other'"
                                    v-model="form.purpose_other"
                                    type="text"
                                    placeholder="Please specify purpose"
                                    :class="
                                        inputClass(errors.purpose_other) +
                                        ' agj-mt'
                                    "
                                    @blur="validateField('purpose_other')"
                                />
                            </FormField>

                            <div class="agj-pricing">
                                <div class="agj-grid agj-grid--2">
                                    <FormField
                                        label="Expected Selling Price"
                                        :error="errors.expected_selling_price"
                                    >
                                        <div class="agj-money">
                                            <span class="agj-money__unit"
                                                >NPR</span
                                            >
                                            <input
                                                v-model.number="
                                                    form.expected_selling_price
                                                "
                                                type="number"
                                                min="0"
                                                class="agj-input agj-input--mono agj-money__input"
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
                                        label="Minimum Acceptable Price"
                                        :error="errors.minimum_acceptable_price"
                                    >
                                        <div class="agj-money">
                                            <span class="agj-money__unit"
                                                >NPR</span
                                            >
                                            <input
                                                v-model.number="
                                                    form.minimum_acceptable_price
                                                "
                                                type="number"
                                                min="0"
                                                class="agj-input agj-input--mono agj-money__input"
                                                placeholder="0.00"
                                                @blur="
                                                    validateField(
                                                        'minimum_acceptable_price',
                                                    )
                                                "
                                            />
                                        </div>
                                    </FormField>
                                    <FormField
                                        label="Rental Amount (per month)"
                                        :error="errors.rental_amount"
                                    >
                                        <div class="agj-money">
                                            <span class="agj-money__unit"
                                                >NPR</span
                                            >
                                            <input
                                                v-model.number="
                                                    form.rental_amount
                                                "
                                                type="number"
                                                min="0"
                                                class="agj-input agj-input--mono agj-money__input"
                                                placeholder="0.00"
                                                @blur="
                                                    validateField(
                                                        'rental_amount',
                                                    )
                                                "
                                            />
                                        </div>
                                    </FormField>
                                    <FormField
                                        label="Negotiable"
                                        :error="errors.negotiable"
                                    >
                                        <div class="agj-radio-row">
                                            <label class="agj-radio-inline">
                                                <input
                                                    type="radio"
                                                    value="yes"
                                                    v-model="form.negotiable"
                                                />
                                                <span>Yes</span>
                                            </label>
                                            <label class="agj-radio-inline">
                                                <input
                                                    type="radio"
                                                    value="no"
                                                    v-model="form.negotiable"
                                                />
                                                <span>No</span>
                                            </label>
                                        </div>
                                    </FormField>
                                </div>
                            </div>

                            <div class="agj-subhead">
                                <span class="agj-subhead__tag">क</span>
                                <h3>Documents Submitted</h3>
                            </div>
                            <div class="agj-checks">
                                <label
                                    v-for="doc in documentTypes"
                                    :key="doc.value"
                                    class="agj-check"
                                >
                                    <input
                                        type="checkbox"
                                        :value="doc.value"
                                        v-model="form.submitted_documents"
                                        class="agj-check__input"
                                    />
                                    <span class="agj-check__box">
                                        <svg viewBox="0 0 24 24">
                                            <path d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                    <span class="agj-check__label">{{
                                        doc.label
                                    }}</span>
                                </label>
                            </div>
                            <input
                                v-model="form.other_documents"
                                type="text"
                                placeholder="Other documents (please specify)"
                                :class="
                                    inputClass(errors.other_documents) + ' agj-mt'
                                "
                            />

                            <div class="agj-subhead">
                                <span class="agj-subhead__tag">ख</span>
                                <h3>Property Features</h3>
                            </div>
                            <div class="agj-checks">
                                <label
                                    v-for="feat in featureTypes"
                                    :key="feat.value"
                                    class="agj-check"
                                >
                                    <input
                                        type="checkbox"
                                        :value="feat.value"
                                        v-model="form.property_features"
                                        class="agj-check__input"
                                    />
                                    <span class="agj-check__box">
                                        <svg viewBox="0 0 24 24">
                                            <path d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                    <span class="agj-check__label">{{
                                        feat.label
                                    }}</span>
                                </label>
                            </div>
                            <input
                                v-model="form.other_features"
                                type="text"
                                placeholder="Other features (please specify)"
                                :class="
                                    inputClass(errors.other_features) + ' agj-mt'
                                "
                            />
                        </div>
                    </section>

                    <!-- ─────────── STEP 4: DECLARATION ─────────── -->
                    <section
                        v-else-if="currentStep === 3"
                        key="step4"
                        class="agj-card"
                    >
                        <div class="agj-card__body">
                            <div class="agj-step-head">
                                <span class="agj-step-head__idx">०४</span>
                                <div>
                                    <h2 class="agj-step-head__title">
                                        Declaration &amp; Signature
                                    </h2>
                                    <p class="agj-step-head__desc">
                                        Review the undertaking, then sign to
                                        lodge the application.
                                    </p>
                                </div>
                            </div>

                            <div class="agj-declaration">
                                <span class="agj-declaration__mark"
                                    >§</span
                                >
                                <p>
                                    I hereby declare that the information
                                    provided in this application is true and
                                    correct to the best of my knowledge. I
                                    confirm that I am the lawful owner or
                                    authorised representative of the property
                                    and authorise
                                    <strong>Api Ghar Jagga</strong> to inspect,
                                    market, advertise, and facilitate the sale,
                                    rental, lease, or transfer of the property in
                                    accordance with the agreed terms and
                                    applicable laws.
                                </p>
                                <p class="agj-declaration__np">
                                    म यस आवेदनमा उल्लेख गरिएका सम्पूर्ण विवरणहरू
                                    सत्य तथा सही रहेको घोषणा गर्दछु।
                                </p>
                            </div>

                            <label
                                class="agj-agree"
                                :class="{
                                    'is-error': errors.declaration_agreed,
                                }"
                            >
                                <input
                                    type="checkbox"
                                    v-model="form.declaration_agreed"
                                    class="agj-check__input"
                                />
                                <span class="agj-check__box agj-check__box--lg">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <span class="agj-agree__text">
                                    <span class="agj-agree__en"
                                        >I agree to the declaration</span
                                    >
                                    <span class="agj-agree__np"
                                        >माथिको घोषणामा सहमत छु</span
                                    >
                                </span>
                            </label>
                            <p
                                v-if="errors.declaration_agreed"
                                class="agj-field-error"
                            >
                                {{ errors.declaration_agreed }}
                            </p>

                            <div class="agj-sign">
                                <div class="agj-sign__name">
                                    <span class="agj-field-label"
                                        >Electronic Signature — type full
                                        name</span
                                    >
                                    <input
                                        v-model="form.applicant_name"
                                        type="text"
                                        class="agj-signline"
                                        placeholder="Type your name to sign"
                                        @blur="validateField('applicant_name')"
                                    />
                                    <p
                                        v-if="errors.applicant_name"
                                        class="agj-field-error"
                                    >
                                        {{ errors.applicant_name }}
                                    </p>
                                </div>
                                <FormField
                                    label="Date"
                                    :error="errors.applicant_date"
                                >
                                    <input
                                        v-model="form.applicant_date"
                                        type="date"
                                        :max="today"
                                        :class="
                                            inputClass(errors.applicant_date) +
                                            ' agj-input--mono'
                                        "
                                    />
                                </FormField>
                                <div class="agj-span-2">
                                    <SignatureUpload
                                        v-model="form.applicant_signature"
                                        label="Scanned Signature"
                                        required
                                        :error="errors.applicant_signature"
                                        @update:model-value="
                                            validateField('applicant_signature')
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                    </section>
                </transition>

                <!-- Navigation -->
                <div class="agj-nav">
                    <button
                        type="button"
                        @click="prevStep"
                        v-if="currentStep > 0"
                        class="agj-btn agj-btn--ghost"
                    >
                        <svg class="agj-btn__icon" viewBox="0 0 24 24">
                            <path d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </button>
                    <div v-else></div>

                    <button
                        type="submit"
                        :disabled="submitting"
                        class="agj-btn agj-btn--primary"
                    >
                        <span>{{
                            submitting
                                ? 'Processing…'
                                : currentStep === steps.length - 1
                                  ? 'Lodge application'
                                  : 'Continue'
                        }}</span>
                        <svg
                            v-if="submitting"
                            class="agj-btn__icon agj-spin"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                                fill="none"
                                stroke-opacity="0.25"
                            />
                            <path
                                d="M21 12a9 9 0 00-9-9"
                                fill="none"
                            />
                        </svg>
                        <svg
                            v-else-if="currentStep < steps.length - 1"
                            class="agj-btn__icon"
                            viewBox="0 0 24 24"
                        >
                            <path d="M9 5l7 7-7 7" />
                        </svg>
                        <svg v-else class="agj-btn__icon" viewBox="0 0 24 24">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </div>
            </form>

            <p class="agj-foot">
                Api Ghar Jagga · Property Registry System — form recorded under
                Annex reference AGJ·FRM·001
            </p>
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
    // Applicant Details
    full_name_en: '',
    full_name_np: '',
    citizenship_no: '',
    date_of_birth: '',
    father_name: '',
    grandfather_name: '',
    permanent_address: '',
    current_address: '',
    mobile_no: '',
    telephone_no: '',
    email: '',
    occupation: '',
    // Property Owner
    ownership_role: '',
    // Property Details
    property_type: '',
    property_type_other: '',
    province: '',
    district: '',
    municipality: '',
    ward_no: '',
    tole: '',
    gps_location: '',
    kitta_no: '',
    area: '',
    map_sheet_no: '',
    ownership_type: '',
    road_access: '',
    road_width: '',
    facing_direction: '',
    year_of_construction: null as number | null,
    no_of_floors: null as number | null,
    covered_area: '',
    structure_type: '',
    roof_type: '',
    parking: '',
    water_supply: '',
    electricity: '',
    internet: '',
    drainage: '',
    // Purpose of Listing
    purpose_of_listing: '',
    purpose_other: '',
    // Expected Price
    expected_selling_price: null as number | null,
    negotiable: '',
    minimum_acceptable_price: null as number | null,
    rental_amount: null as number | null,
    // Documents
    submitted_documents: [] as string[],
    other_documents: '',
    // Features
    property_features: [] as string[],
    other_features: '',
    // Declaration & Signature
    declaration_agreed: false,
    applicant_name: '',
    applicant_date: new Date().toISOString().split('T')[0],
    applicant_signature: null as File | null,
});

const form = reactive(emptyForm());
const errors = reactive<Record<string, string>>({});

// ── Wizard State ───────────────────────────────────────────────────────────
const steps = [
    {
        title: 'Applicant',
        np: 'आवेदक',
        fields: ['full_name_en', 'citizenship_no', 'mobile_no'],
    },
    {
        title: 'Property',
        np: 'सम्पत्ति',
        fields: [
            'ownership_role',
            'property_type',
            'property_type_other',
            'province',
            'district',
            'municipality',
            'ward_no',
        ],
    },
    {
        title: 'Terms',
        np: 'विवरण',
        fields: ['purpose_of_listing', 'purpose_other'],
    },
    {
        title: 'Declaration',
        np: 'घोषणा',
        fields: ['declaration_agreed', 'applicant_name', 'applicant_signature'],
    },
];

const devanagariNum = ['०१', '०२', '०३', '०४'];

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
// Icons are stroked line-paths (viewBox 24) drawn as an official instrument set.
const ownershipRoles = [
    {
        value: 'self',
        label: 'Self',
        icon: [
            'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z',
            'M4.5 20.25a7.5 7.5 0 0115 0',
        ],
    },
    {
        value: 'family_member',
        label: 'Family',
        icon: [
            'M12 6.75a3 3 0 11-6 0 3 3 0 016 0z',
            'M4 19.5a5 5 0 0110 0',
            'M17 11a2.5 2.5 0 10-2-4',
            'M15.5 19.5a5 5 0 015-3.2',
        ],
    },
    {
        value: 'authorized_representative',
        label: 'Representative',
        icon: [
            'M4.5 19.5h15a2 2 0 002-2v-11a2 2 0 00-2-2h-15a2 2 0 00-2 2v11a2 2 0 002 2z',
            'M14 9h4M14 12h4M9.5 9.4a1.6 1.6 0 11-3.2 0 1.6 1.6 0 013.2 0z',
            'M10.6 14.4a3 3 0 00-5.4 0',
        ],
    },
    {
        value: 'company',
        label: 'Company',
        icon: [
            'M3.5 20.5h17M5 20.5V4.5h9v16M14 20.5h5V9h-5',
            'M8 8h2M8 11h2M8 14h2',
        ],
    },
];

const propertyTypes = [
    { value: 'land', label: 'Land' },
    { value: 'house', label: 'House' },
    { value: 'apartment', label: 'Apartment' },
    { value: 'commercial_building', label: 'Commercial Building' },
    { value: 'office_space', label: 'Office Space' },
    { value: 'industrial_property', label: 'Industrial Property' },
    { value: 'agricultural_land', label: 'Agricultural Land' },
    { value: 'other', label: 'Other' },
];

const listingPurposes = [
    {
        value: 'sale',
        label: 'Sale',
        icon: [
            'M9.5 3H5.2A2.2 2.2 0 003 5.2v4.3c0 .6.2 1.2.7 1.6l9.5 9.5c.7.7 1.8.9 2.6.3a18 18 0 005.2-5.2c.5-.8.4-1.9-.3-2.6L11.2 3.7A2.2 2.2 0 009.5 3z',
            'M7 7h.01',
        ],
    },
    {
        value: 'rent',
        label: 'Rent',
        icon: [
            'M15.75 5.25a3 3 0 013 3',
            'M21.75 8.25a6 6 0 01-7 5.9c-.6-.1-1.2 0-1.6.4L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.8c0-.6.2-1.2.7-1.6l6.5-6.5c.4-.4.5-1 .4-1.6a6 6 0 1111.9-1z',
        ],
    },
    {
        value: 'lease',
        label: 'Lease',
        icon: [
            'M8 2.75H5.6c-.6 0-1.1.5-1.1 1.1v16.3c0 .6.5 1.1 1.1 1.1h12.8c.6 0 1.1-.5 1.1-1.1V11.2a9 9 0 00-9-9z',
            'M8 12h8M8 15.5h8M8 8.5h3',
        ],
    },
    {
        value: 'exchange',
        label: 'Exchange',
        icon: [
            'M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5',
            'M16.5 3L21 7.5m0 0L16.5 12M21 7.5H7.5',
        ],
    },
    {
        value: 'investment',
        label: 'Investment',
        icon: [
            'M2.5 18L9 11.25l4.3 4.3a12 12 0 015.8-5.5l2.4-1.05',
            'M17.5 8.5H21.5V12.5',
        ],
    },
    { value: 'other', label: 'Other', icon: ['M12 4.5v15m7.5-7.5h-15'] },
];

const featureTypes = [
    { value: 'corner_plot', label: 'Corner Plot' },
    { value: 'blacktopped_road', label: 'Blacktopped Road' },
    { value: 'drinking_water', label: 'Drinking Water' },
    { value: 'electricity', label: 'Electricity' },
    { value: 'sewer', label: 'Sewer' },
    { value: 'internet', label: 'Internet' },
    { value: 'school_nearby', label: 'School Nearby' },
    { value: 'hospital_nearby', label: 'Hospital Nearby' },
    { value: 'market_nearby', label: 'Market Nearby' },
    { value: 'public_transport', label: 'Public Transport' },
    { value: 'bank_nearby', label: 'Bank Nearby' },
    { value: 'temple', label: 'Temple' },
    { value: 'park', label: 'Park' },
];

const documentTypes = [
    { value: 'citizenship_copy', label: 'Citizenship Copy' },
    {
        value: 'land_ownership_certificate',
        label: 'Land Ownership Certificate (Lalpurja)',
    },
    { value: 'tax_clearance', label: 'Tax Clearance' },
    { value: 'blueprint', label: 'Blueprint' },
    {
        value: 'building_completion_certificate',
        label: 'Building Completion Certificate',
    },
    { value: 'valuation_report', label: 'Valuation Report' },
    { value: 'power_of_attorney', label: 'Power of Attorney' },
    { value: 'utility_bills', label: 'Utility Bills' },
    { value: 'photographs', label: 'Photographs' },
];

// ── Helpers ────────────────────────────────────────────────────────────────
const inputClass = (error?: string) =>
    `agj-input${error ? ' agj-input--error' : ''}`;

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
    property_type_other: (v) =>
        form.property_type === 'other' && !v
            ? 'Please specify the property type'
            : true,
    province: (v) => (!v ? 'Province is required' : true),
    district: (v) => (!v ? 'District is required' : true),
    municipality: (v) => (!v ? 'Municipality is required' : true),
    ward_no: (v) => (!v ? 'Ward No is required' : true),
    purpose_of_listing: (v) => (!v ? 'Please select listing purpose' : true),
    purpose_other: (v) =>
        form.purpose_of_listing === 'other' && !v
            ? 'Please specify the purpose'
            : true,
    declaration_agreed: (v) => (!v ? 'You must agree to the terms' : true),
    applicant_name: (v) => (!v ? 'Signature name is required' : true),
    applicant_signature: (v) => {
        if (!(v instanceof File))
            return 'Please upload a scanned signature image';
        if (
            !['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(
                v.type,
            )
        )
            return 'Signature must be JPG, PNG or WEBP';
        if (v.size > 2 * 1024 * 1024) return 'Signature image must be under 2 MB';
        return true;
    },
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
        const res = await axios.post('/property-listing', fd, {
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

<style scoped>
/* ══════════════════════════════════════════════════════════════════
   CADASTRAL REGISTER — design tokens
   ══════════════════════════════════════════════════════════════════ */
.agj-root {
    --paper: #eef0ea;
    --paper-card: #fafbf7;
    --ink: #16211c;
    --ink-soft: #47554e;
    --ink-faint: #7c887f;
    --land: #1f5641;
    --land-600: #2c6b52;
    --land-tint: #e6ede8;
    --brass: #a6772e;
    --brass-tint: #f1e7d4;
    --seal: #9e2b20;
    --rule: #d3d9cf;
    --rule-strong: #b7c0b5;
    --error: #c0392b;
    --error-tint: #f9e9e6;

    --font-display: 'Fraunces', Georgia, serif;
    --font-np: 'Noto Serif Devanagari', 'Noto Sans Devanagari', serif;
    --font-np-body: 'Noto Sans Devanagari', sans-serif;
    --font-mono: 'IBM Plex Mono', ui-monospace, monospace;

    min-height: 100vh;
    background-color: var(--paper);
    background-image:
        linear-gradient(var(--rule) 1px, transparent 1px),
        linear-gradient(90deg, var(--rule) 1px, transparent 1px);
    background-size: 26px 26px;
    background-position: -1px -1px;
    color: var(--ink);
    font-family:
        'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    -webkit-font-smoothing: antialiased;
}
.agj-root::selection {
    background: var(--land);
    color: #fff;
}
.agj-root ::selection {
    background: var(--land);
    color: #fff;
}

.agj-page {
    max-width: 60rem;
    margin: 0 auto;
    padding: 3.5rem 1.25rem 4rem;
}

/* ── Masthead ─────────────────────────────────────────────────────── */
.agj-masthead {
    text-align: center;
    margin-bottom: 2.75rem;
}
.agj-masthead__meta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1.1rem;
    flex-wrap: wrap;
}
.agj-stamp {
    font-family: var(--font-mono);
    font-size: 0.66rem;
    font-weight: 500;
    letter-spacing: 0.18em;
    color: var(--land);
    border: 1px solid var(--rule-strong);
    background: var(--paper-card);
    padding: 0.28rem 0.6rem;
    border-radius: 2px;
}
.agj-stamp--muted {
    color: var(--ink-faint);
}
.agj-masthead__seals {
    font-family: var(--font-np);
    font-size: 0.72rem;
    letter-spacing: 0.04em;
    color: var(--brass);
    padding-left: 0.3rem;
}
.agj-title {
    font-family: var(--font-display);
    font-optical-sizing: auto;
    font-weight: 600;
    font-size: clamp(2rem, 5vw, 3.1rem);
    line-height: 1.04;
    letter-spacing: -0.015em;
    color: var(--ink);
    margin: 0;
}
.agj-title__np {
    font-family: var(--font-np);
    font-weight: 500;
    font-size: clamp(0.95rem, 2.5vw, 1.2rem);
    color: var(--ink-soft);
    margin: 0.55rem 0 0;
}
.agj-rule {
    height: 0;
    border-top: 2px solid var(--ink);
    border-bottom: 1px solid var(--ink);
    padding-top: 3px;
    width: 3.5rem;
    margin: 1.4rem auto 0;
}

/* ── Survey traverse ──────────────────────────────────────────────── */
.agj-traverse {
    position: relative;
    max-width: 46rem;
    margin: 0 auto 2.75rem;
    padding: 0 1.2rem;
}
.agj-traverse__line {
    position: absolute;
    top: 1.35rem;
    left: 2.6rem;
    right: 2.6rem;
    height: 2px;
    background-image: repeating-linear-gradient(
        90deg,
        var(--rule-strong) 0 6px,
        transparent 6px 12px
    );
}
.agj-traverse__line-fill {
    position: absolute;
    inset: 0 auto 0 0;
    height: 100%;
    background: var(--land);
    transition: width 0.5s ease;
}
.agj-traverse__stations {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    list-style: none;
    margin: 0;
    padding: 0;
}
.agj-station {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.55rem;
    flex: 1;
}
.agj-station__mark {
    width: 2.7rem;
    height: 2.7rem;
    border-radius: 50%;
    border: 1.5px solid var(--rule-strong);
    background: var(--paper-card);
    color: var(--ink-faint);
    font-family: var(--font-mono);
    font-size: 0.82rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition:
        transform 0.2s ease,
        border-color 0.2s ease,
        background 0.2s ease,
        color 0.2s ease,
        box-shadow 0.2s ease;
}
.agj-station__mark:disabled {
    cursor: not-allowed;
}
.agj-station__mark.is-reachable:hover {
    border-color: var(--land);
    color: var(--land);
}
.agj-station__mark.is-done {
    background: var(--land);
    border-color: var(--land);
    color: #fff;
}
.agj-station__mark.is-active {
    border-color: var(--brass);
    background: var(--paper-card);
    color: var(--brass);
    box-shadow:
        0 0 0 4px var(--brass-tint),
        0 1px 2px rgba(0, 0, 0, 0.06);
    transform: translateY(-1px);
}
.agj-station__check {
    width: 1.15rem;
    height: 1.15rem;
    fill: none;
    stroke: currentColor;
    stroke-width: 2.6;
    stroke-linecap: round;
    stroke-linejoin: round;
}
.agj-station__label {
    display: flex;
    flex-direction: column;
    align-items: center;
    line-height: 1.1;
}
.agj-station__en {
    font-size: 0.66rem;
    font-weight: 700;
    letter-spacing: 0.09em;
    text-transform: uppercase;
    color: var(--ink-soft);
}
.agj-station__np {
    font-family: var(--font-np-body);
    font-size: 0.66rem;
    color: var(--ink-faint);
    margin-top: 1px;
}
.agj-station__mark.is-active + .agj-station__label .agj-station__en {
    color: var(--ink);
}

/* ── Error ledger ─────────────────────────────────────────────────── */
.agj-errors {
    display: flex;
    gap: 0.9rem;
    max-width: 100%;
    margin-bottom: 1.75rem;
    padding: 1rem 1.1rem;
    background: var(--error-tint);
    border: 1px solid #eec4bd;
    border-left: 3px solid var(--error);
    border-radius: 3px;
}
.agj-errors__mark svg {
    width: 1.25rem;
    height: 1.25rem;
    fill: none;
    stroke: var(--error);
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
}
.agj-errors__title {
    font-weight: 700;
    font-size: 0.85rem;
    color: #8a2318;
    margin: 0 0 0.3rem;
}
.agj-errors__list {
    margin: 0;
    padding-left: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}
.agj-errors__list li {
    font-size: 0.82rem;
    color: var(--error);
}

/* ── Ledger card ──────────────────────────────────────────────────── */
.agj-card {
    position: relative;
    background: var(--paper-card);
    border: 1px solid var(--rule-strong);
    border-left: 3px solid var(--land);
    border-radius: 3px;
    box-shadow:
        0 1px 0 var(--rule),
        0 18px 40px -28px rgba(22, 33, 28, 0.45);
}
/* binding-margin double line */
.agj-card::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 14px;
    width: 1px;
    background: var(--rule);
}
.agj-card__body {
    padding: 2.25rem 2.25rem 2.5rem;
    position: relative;
}
@media (max-width: 640px) {
    .agj-card__body {
        padding: 1.5rem 1.25rem 1.75rem;
    }
    .agj-card::before {
        display: none;
    }
    .agj-card {
        border-left-width: 3px;
    }
}

.agj-step-head {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid var(--rule);
}
.agj-step-head__idx {
    flex-shrink: 0;
    font-family: var(--font-np);
    font-weight: 700;
    font-size: 1.6rem;
    line-height: 1;
    color: var(--brass);
    width: 2.6rem;
    height: 2.6rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid var(--brass);
    border-radius: 50%;
    background: var(--brass-tint);
}
.agj-step-head__title {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 1.5rem;
    letter-spacing: -0.01em;
    color: var(--ink);
    margin: 0.15rem 0 0.35rem;
}
.agj-step-head__desc {
    font-size: 0.86rem;
    line-height: 1.5;
    color: var(--ink-soft);
    margin: 0;
    max-width: 40ch;
}

/* ── Sub-section headers ─────────────────────────────────────────── */
.agj-subhead {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    margin: 2.25rem 0 1.35rem;
}
.agj-subhead__tag {
    font-family: var(--font-np);
    font-weight: 700;
    font-size: 0.85rem;
    color: var(--land);
    width: 1.7rem;
    height: 1.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid var(--land);
    border-radius: 4px;
    background: var(--land-tint);
    flex-shrink: 0;
}
.agj-subhead h3 {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 1.08rem;
    color: var(--ink);
    margin: 0;
}
.agj-subhead__opt {
    font-size: 0.72rem;
    color: var(--ink-faint);
    font-style: italic;
}
.agj-subhead::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--rule);
}

/* ── Grid ─────────────────────────────────────────────────────────── */
.agj-grid {
    display: grid;
    gap: 1.15rem 1.5rem;
}
.agj-grid--2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}
.agj-grid--3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}
.agj-span-2 {
    grid-column: 1 / -1;
}
.agj-subgrid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1.15rem 1.5rem;
    padding-top: 1.25rem;
    margin-top: 0.35rem;
    border-top: 1px solid var(--rule);
}
.agj-block {
    display: block;
    margin-bottom: 1.75rem;
}
.agj-mt {
    margin-top: 0.75rem;
}
@media (max-width: 720px) {
    .agj-grid--2,
    .agj-grid--3,
    .agj-subgrid {
        grid-template-columns: 1fr;
    }
}

/* ── Inputs ───────────────────────────────────────────────────────── */
.agj-input {
    width: 100%;
    padding: 0.68rem 0.85rem;
    background: #fff;
    border: 1px solid var(--rule-strong);
    border-radius: 3px;
    color: var(--ink);
    font-size: 0.9rem;
    font-family: inherit;
    transition:
        border-color 0.18s ease,
        box-shadow 0.18s ease,
        background 0.18s ease;
}
.agj-input::placeholder {
    color: #aab3a9;
}
.agj-input:hover {
    border-color: var(--ink-faint);
}
.agj-input:focus {
    outline: none;
    border-color: var(--land);
    box-shadow: 0 0 0 3px var(--land-tint);
    background: #fff;
}
textarea.agj-input {
    resize: none;
    line-height: 1.5;
}
select.agj-input {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2347554e' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.7rem center;
    background-size: 1.05rem;
    padding-right: 2.2rem;
    cursor: pointer;
}
.agj-input--mono {
    font-family: var(--font-mono);
    letter-spacing: 0.01em;
}
.agj-input--error {
    border-color: var(--error);
    background: var(--error-tint);
}
.agj-input--error:focus {
    border-color: var(--error);
    box-shadow: 0 0 0 3px #f4d3cd;
}

/* ── Money field ──────────────────────────────────────────────────── */
.agj-pricing {
    margin: 1.75rem 0 0.5rem;
    padding: 1.5rem;
    background: var(--land-tint);
    border: 1px solid #cdddd3;
    border-radius: 3px;
}
.agj-money {
    position: relative;
}
.agj-money__unit {
    position: absolute;
    top: 50%;
    left: 0.75rem;
    transform: translateY(-50%);
    font-family: var(--font-mono);
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    color: var(--land);
    pointer-events: none;
}
.agj-money__input {
    padding-left: 3.1rem;
}

.agj-radio-row {
    display: flex;
    gap: 1.5rem;
    padding-top: 0.55rem;
}
.agj-radio-inline {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    cursor: pointer;
    font-size: 0.88rem;
    color: var(--ink-soft);
}
.agj-radio-inline input {
    accent-color: var(--land);
    width: 1rem;
    height: 1rem;
}

/* ── Choice tiles (radio cards) ───────────────────────────────────── */
.agj-choices {
    display: grid;
    gap: 0.7rem;
    margin-top: 0.6rem;
}
.agj-choices--4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
}
.agj-choices--6 {
    grid-template-columns: repeat(6, minmax(0, 1fr));
}
@media (max-width: 720px) {
    .agj-choices--4 {
        grid-template-columns: repeat(2, 1fr);
    }
    .agj-choices--6 {
        grid-template-columns: repeat(3, 1fr);
    }
}
.agj-choice {
    cursor: pointer;
}
.agj-choice__input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}
.agj-choice__tile {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    height: 100%;
    padding: 0.95rem 0.5rem;
    border: 1px solid var(--rule-strong);
    border-radius: 3px;
    background: #fff;
    text-align: center;
    transition:
        border-color 0.18s ease,
        background 0.18s ease,
        box-shadow 0.18s ease;
}
.agj-choice__icon {
    width: 1.55rem;
    height: 1.55rem;
    fill: none;
    stroke: var(--ink-faint);
    stroke-width: 1.5;
    stroke-linecap: round;
    stroke-linejoin: round;
    transition: stroke 0.18s ease;
}
.agj-choice__label {
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--ink-soft);
}
.agj-choice:hover .agj-choice__tile {
    border-color: var(--ink-faint);
}
.agj-choice__input:checked + .agj-choice__tile {
    border-color: var(--land);
    background: var(--land-tint);
    box-shadow: inset 0 0 0 1px var(--land);
}
.agj-choice__input:checked + .agj-choice__tile .agj-choice__icon {
    stroke: var(--land);
}
.agj-choice__input:checked + .agj-choice__tile .agj-choice__label {
    color: var(--land);
}
.agj-choice__input:focus-visible + .agj-choice__tile {
    box-shadow: 0 0 0 3px var(--brass-tint);
}

/* ── Pills (property type) ────────────────────────────────────────── */
.agj-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 0.55rem;
    margin-top: 0.6rem;
}
.agj-pill {
    cursor: pointer;
}
.agj-pill__input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}
.agj-pill__label {
    display: inline-block;
    padding: 0.55rem 1rem;
    border: 1px solid var(--rule-strong);
    border-radius: 999px;
    background: #fff;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--ink-soft);
    transition: all 0.16s ease;
}
.agj-pill:hover .agj-pill__label {
    border-color: var(--ink-faint);
}
.agj-pill__input:checked + .agj-pill__label {
    background: var(--ink);
    border-color: var(--ink);
    color: #fff;
}
.agj-pill__input:focus-visible + .agj-pill__label {
    box-shadow: 0 0 0 3px var(--brass-tint);
}

/* ── Check tiles ──────────────────────────────────────────────────── */
.agj-checks {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.6rem;
}
@media (max-width: 720px) {
    .agj-checks {
        grid-template-columns: repeat(2, 1fr);
    }
}
.agj-check {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.7rem 0.8rem;
    border: 1px solid var(--rule-strong);
    border-radius: 3px;
    background: #fff;
    cursor: pointer;
    transition:
        border-color 0.16s ease,
        background 0.16s ease;
}
.agj-check:hover {
    border-color: var(--ink-faint);
}
.agj-check__input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}
.agj-check__box {
    flex-shrink: 0;
    width: 1.15rem;
    height: 1.15rem;
    border: 1.5px solid var(--rule-strong);
    border-radius: 3px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.16s ease;
}
.agj-check__box svg {
    width: 0.8rem;
    height: 0.8rem;
    fill: none;
    stroke: #fff;
    stroke-width: 3;
    stroke-linecap: round;
    stroke-linejoin: round;
    opacity: 0;
    transition: opacity 0.14s ease;
}
.agj-check__box--lg {
    width: 1.5rem;
    height: 1.5rem;
}
.agj-check__box--lg svg {
    width: 1rem;
    height: 1rem;
}
.agj-check__label {
    font-size: 0.83rem;
    font-weight: 500;
    color: var(--ink-soft);
}
.agj-check__input:checked + .agj-check__box {
    background: var(--land);
    border-color: var(--land);
}
.agj-check__input:checked + .agj-check__box svg {
    opacity: 1;
}
.agj-check:has(.agj-check__input:checked) {
    border-color: var(--land);
    background: var(--land-tint);
}
.agj-check__input:focus-visible + .agj-check__box {
    box-shadow: 0 0 0 3px var(--brass-tint);
}

/* ── Declaration ──────────────────────────────────────────────────── */
.agj-declaration {
    position: relative;
    margin-bottom: 1.5rem;
    padding: 1.5rem 1.6rem 1.5rem 2.4rem;
    background: #fff;
    border: 1px solid var(--rule);
    border-radius: 3px;
    font-size: 0.88rem;
    line-height: 1.65;
    color: var(--ink-soft);
}
.agj-declaration__mark {
    position: absolute;
    top: 1rem;
    left: 0.9rem;
    font-family: var(--font-display);
    font-size: 1.4rem;
    color: var(--brass);
    line-height: 1;
}
.agj-declaration p {
    margin: 0;
}
.agj-declaration strong {
    color: var(--ink);
    font-weight: 700;
}
.agj-declaration__np {
    font-family: var(--font-np-body);
    font-size: 0.8rem;
    font-style: normal;
    color: var(--ink-faint);
    margin-top: 0.9rem !important;
    padding-top: 0.9rem;
    border-top: 1px solid var(--rule);
}
.agj-agree {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 1rem 1.1rem;
    border: 1px solid var(--rule-strong);
    border-radius: 3px;
    background: #fff;
    cursor: pointer;
    transition: all 0.16s ease;
}
.agj-agree:hover {
    border-color: var(--land);
    background: var(--land-tint);
}
.agj-agree.is-error {
    border-color: var(--error);
    background: var(--error-tint);
}
.agj-agree__text {
    display: flex;
    flex-direction: column;
}
.agj-agree__en {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--ink);
}
.agj-agree__np {
    font-family: var(--font-np-body);
    font-size: 0.78rem;
    color: var(--ink-faint);
}

/* ── Signature block ──────────────────────────────────────────────── */
.agj-sign {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1.5rem;
    margin-top: 1.75rem;
    padding-top: 1.75rem;
    border-top: 1px solid var(--rule);
}
@media (max-width: 720px) {
    .agj-sign {
        grid-template-columns: 1fr;
    }
}
.agj-signline {
    width: 100%;
    margin-top: 0.6rem;
    padding: 0.4rem 0.25rem 0.6rem;
    background: transparent;
    border: none;
    border-bottom: 1.5px solid var(--rule-strong);
    font-family: var(--font-display);
    font-size: 1.35rem;
    font-style: italic;
    color: var(--ink);
    text-align: center;
    transition: border-color 0.18s ease;
}
.agj-signline::placeholder {
    font-style: italic;
    color: #b4bcb2;
    font-size: 1rem;
}
.agj-signline:focus {
    outline: none;
    border-bottom-color: var(--land);
}

.agj-field-label {
    display: block;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--ink-faint);
    margin-bottom: 0.35rem;
}
.agj-field-error {
    margin-top: 0.4rem;
    font-size: 0.78rem;
    color: var(--error);
}

/* ── Navigation & buttons ─────────────────────────────────────────── */
.agj-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 1.75rem;
}
.agj-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.8rem 1.6rem;
    border-radius: 3px;
    font-size: 0.88rem;
    font-weight: 700;
    letter-spacing: 0.01em;
    cursor: pointer;
    border: 1px solid transparent;
    transition:
        transform 0.15s ease,
        background 0.18s ease,
        box-shadow 0.18s ease,
        border-color 0.18s ease;
}
.agj-btn__icon {
    width: 1.1rem;
    height: 1.1rem;
    fill: none;
    stroke: currentColor;
    stroke-width: 2.2;
    stroke-linecap: round;
    stroke-linejoin: round;
}
.agj-btn--primary {
    background: var(--ink);
    color: #f3f5f0;
    box-shadow: 0 2px 0 #0c130f;
}
.agj-btn--primary:hover:not(:disabled) {
    background: var(--land);
    transform: translateY(-1px);
    box-shadow: 0 4px 0 #123324;
}
.agj-btn--primary:active:not(:disabled) {
    transform: translateY(1px);
    box-shadow: 0 1px 0 #0c130f;
}
.agj-btn--primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.agj-btn--ghost {
    background: transparent;
    color: var(--ink-soft);
    border-color: var(--rule-strong);
}
.agj-btn--ghost:hover {
    background: #fff;
    border-color: var(--ink-faint);
    color: var(--ink);
}
.agj-spin {
    animation: agj-spin 0.8s linear infinite;
}
@keyframes agj-spin {
    to {
        transform: rotate(360deg);
    }
}

.agj-foot {
    text-align: center;
    margin-top: 2.5rem;
    font-family: var(--font-mono);
    font-size: 0.68rem;
    letter-spacing: 0.04em;
    color: var(--ink-faint);
}

/* ══════════════════════════════════════════════════════════════════
   Success receipt
   ══════════════════════════════════════════════════════════════════ */
.agj-receipt-wrap {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
}
.agj-receipt {
    width: 100%;
    max-width: 34rem;
    background: var(--paper-card);
    border: 1px solid var(--rule-strong);
    border-top: 4px solid var(--land);
    border-radius: 3px;
    padding: 2.5rem 2.5rem 2.75rem;
    box-shadow: 0 30px 60px -30px rgba(22, 33, 28, 0.5);
    animation: agj-rise 0.5s ease;
}
@keyframes agj-rise {
    from {
        opacity: 0;
        transform: translateY(14px);
    }
    to {
        opacity: 1;
        transform: none;
    }
}
.agj-receipt__head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--rule);
}
.agj-receipt__masthead {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}
.agj-eyebrow {
    font-family: var(--font-mono);
    font-size: 0.64rem;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--ink-faint);
}
.agj-eyebrow--np {
    font-family: var(--font-np-body);
    text-transform: none;
    letter-spacing: 0.02em;
    color: var(--brass);
}
.agj-seal {
    position: relative;
    width: 4.5rem;
    height: 4.5rem;
    flex-shrink: 0;
}
.agj-seal__ring {
    width: 100%;
    height: 100%;
    fill: none;
    stroke: var(--seal);
    stroke-width: 1;
    opacity: 0.85;
    animation: agj-seal-in 0.6s ease 0.15s both;
}
.agj-seal__text {
    font-family: var(--font-mono);
    font-size: 6.1px;
    letter-spacing: 0.5px;
    fill: var(--seal);
    stroke: none;
}
.agj-seal__check {
    position: absolute;
    inset: 0;
    margin: auto;
    width: 1.7rem;
    height: 1.7rem;
    fill: none;
    stroke: var(--seal);
    stroke-width: 2.4;
    stroke-linecap: round;
    stroke-linejoin: round;
}
@keyframes agj-seal-in {
    from {
        opacity: 0;
        transform: rotate(-25deg) scale(0.8);
    }
    to {
        opacity: 0.85;
        transform: none;
    }
}
.agj-receipt__title {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 2rem;
    color: var(--ink);
    margin: 1.75rem 0 0.3rem;
}
.agj-receipt__np {
    font-family: var(--font-np);
    font-size: 0.95rem;
    color: var(--ink-soft);
    margin: 0 0 1.75rem;
}
.agj-receipt__field {
    padding: 1.1rem 1.25rem;
    background: var(--land-tint);
    border: 1px dashed var(--land-600);
    border-radius: 3px;
    text-align: center;
}
.agj-appno {
    font-family: var(--font-mono);
    font-size: 1.7rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    color: var(--ink);
    margin: 0.3rem 0 0;
}
.agj-receipt__note {
    font-size: 0.85rem;
    line-height: 1.6;
    color: var(--ink-soft);
    margin: 1.5rem 0 1.75rem;
}
.agj-receipt__actions {
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
}
.agj-receipt__actions .agj-btn {
    justify-content: center;
    width: 100%;
    padding: 0.85rem;
}

/* ── Deep overrides for shared child components (scoped to this page) ── */
.agj-root :deep(.block.text-xs) {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: var(--ink-faint);
    text-transform: uppercase;
    margin-bottom: 0.4rem;
}
.agj-root :deep(label .text-red-500),
.agj-root :deep(.text-red-500) {
    color: var(--error);
}
.agj-root :deep(.text-slate-400) {
    color: var(--ink-faint);
}
/* SignatureUpload dropzone → ledger look */
.agj-root :deep(.border-dashed) {
    border-radius: 3px;
    border-color: var(--rule-strong);
    background: #fff;
}
.agj-root :deep(.border-emerald-300) {
    border-color: var(--land) !important;
    background: var(--land-tint) !important;
}
.agj-root :deep(.text-emerald-600) {
    color: var(--land);
}

/* ── Step transition ──────────────────────────────────────────────── */
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.35s ease;
}
.fade-slide-enter-from {
    opacity: 0;
    transform: translateY(16px);
}
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-16px);
}
@media (prefers-reduced-motion: reduce) {
    .fade-slide-enter-active,
    .fade-slide-leave-active,
    .agj-station__mark,
    .agj-btn,
    .agj-traverse__line-fill,
    .agj-seal__ring,
    .agj-receipt {
        transition: none !important;
        animation: none !important;
    }
}
</style>
