<template>
    <div class="annex-page">

        <!-- ================= HEADER ================= -->
        <header class="page-header">

            <div class="document-code">
                AGJ-FRM-04
                <span></span>
                ANNEX-C
            </div>

            <h1>Property Valuation Request Form</h1>

            <h2>सम्पत्ति मूल्याङ्कन अनुरोध फाराम</h2>

            <p>Api Ghar Jagga — Property Valuation Service</p>

        </header>


        <!-- ================= PROGRESS ================= -->
        <div class="progress-wrapper">

            <div class="progress-line">
                <div
                    class="progress-active"
                    :style="{ width: progress + '%' }"
                ></div>
            </div>

            <div class="steps">

                <button
                    v-for="(step, index) in steps"
                    :key="step.english"
                    type="button"
                    class="step"
                    :class="{ active: index <= currentStep }"
                    @click="goToStep(index)"
                >
                    <span class="step-number">
                        {{ index + 1 }}
                    </span>

                    <span class="step-label">
                        {{ step.english }}
                    </span>

                    <small>
                        {{ step.nepali }}
                    </small>
                </button>

            </div>

        </div>


        <!-- ================= FORM ================= -->
        <form
            class="form-card"
            @submit.prevent="nextOrSubmit"
        >

            <!-- =====================================================
                 STEP 1 — APPLICANT
            ====================================================== -->

            <section v-if="currentStep === 0">

                <SectionHeader
                    number="०१"
                    english="Applicant / Property Owner Details"
                    nepali="आवेदक / सम्पत्ति धनीको विवरण"
                />

                <div class="form-grid">

                    <Field
                        label="Name of Property Owner"
                        nepali="सम्पत्ति धनीको नाम"
                        required
                    >
                        <input
                            v-model="form.full_name"
                            type="text"
                            placeholder="Enter full name / पूरा नाम"
                        />
                    </Field>


                    <Field
                        label="Father's / Husband's Name"
                        nepali="बाबु / श्रीमानको नाम"
                    >
                        <input
                            v-model="form.father_mother_name"
                            type="text"
                            placeholder="Enter name / नाम लेख्नुहोस्"
                        />
                    </Field>


                    <Field
                        label="Citizenship No."
                        nepali="नागरिकता नं."
                        required
                    >
                        <input
                            v-model="form.citizenship_no"
                            type="text"
                            placeholder="Citizenship number / नागरिकता नम्बर"
                        />
                    </Field>


                    <Field
                        label="Contact No."
                        nepali="सम्पर्क नं."
                        required
                    >
                        <input
                            v-model="form.mobile_no"
                            type="tel"
                            placeholder="98XXXXXXXX"
                        />
                    </Field>


                    <Field
                        label="Email Address"
                        nepali="इमेल ठेगाना"
                    >
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="example@email.com"
                        />
                    </Field>

                </div>


                <!-- PERMANENT ADDRESS -->
                <div class="sub-section">

                    <div class="sub-title">
                        <span>Permanent Address / स्थायी ठेगाना</span>
                    </div>

                    <div class="form-grid">

                        <Field
                            label="Province"
                            nepali="प्रदेश"
                        >
                            <input
                                v-model="form.permanent_province"
                                type="text"
                            />
                        </Field>


                        <Field
                            label="District"
                            nepali="जिल्ला"
                        >
                            <input
                                v-model="form.permanent_district"
                                type="text"
                            />
                        </Field>


                        <Field
                            label="Municipality / Rural Municipality"
                            nepali="नगरपालिका / गाउँपालिका"
                        >
                            <input
                                v-model="form.permanent_municipality"
                                type="text"
                            />
                        </Field>


                        <Field
                            label="Ward No."
                            nepali="वडा नं."
                        >
                            <input
                                v-model="form.permanent_ward_no"
                                type="text"
                            />
                        </Field>


                        <Field
                            label="Tole / Area"
                            nepali="टोल / क्षेत्र"
                        >
                            <input
                                v-model="form.permanent_tole"
                                type="text"
                            />
                        </Field>


                        <Field
                            label="Full Address"
                            nepali="पूरा ठेगाना"
                            full
                        >
                            <textarea
                                v-model="form.permanent_full_address"
                                rows="3"
                            ></textarea>
                        </Field>

                    </div>

                </div>


                <!-- CURRENT ADDRESS -->
                <div class="sub-section">

                    <div class="sub-title">
                        <span>Current Address / हालको ठेगाना</span>
                    </div>


                    <label class="same-address">

                        <input
                            v-model="form.same_current_address"
                            type="checkbox"
                            @change="copyPermanentAddress"
                        />

                        <span>
                            Same as Permanent Address
                            <small>
                                स्थायी ठेगाना जस्तै
                            </small>
                        </span>

                    </label>


                    <div class="form-grid">

                        <Field
                            label="Province"
                            nepali="प्रदेश"
                        >
                            <input
                                v-model="form.current_province"
                                type="text"
                            />
                        </Field>


                        <Field
                            label="District"
                            nepali="जिल्ला"
                        >
                            <input
                                v-model="form.current_district"
                                type="text"
                            />
                        </Field>


                        <Field
                            label="Municipality / Rural Municipality"
                            nepali="नगरपालिका / गाउँपालिका"
                        >
                            <input
                                v-model="form.current_municipality"
                                type="text"
                            />
                        </Field>


                        <Field
                            label="Ward No."
                            nepali="वडा नं."
                        >
                            <input
                                v-model="form.current_ward_no"
                                type="text"
                            />
                        </Field>


                        <Field
                            label="Tole / Area"
                            nepali="टोल / क्षेत्र"
                        >
                            <input
                                v-model="form.current_tole"
                                type="text"
                            />
                        </Field>


                        <Field
                            label="Full Address"
                            nepali="पूरा ठेगाना"
                            full
                        >
                            <textarea
                                v-model="form.current_full_address"
                                rows="3"
                            ></textarea>
                        </Field>

                    </div>

                </div>

            </section>


            <!-- =====================================================
                 STEP 2 — PROPERTY
            ====================================================== -->

            <section v-else-if="currentStep === 1">

                <SectionHeader
                    number="०२"
                    english="Property Details"
                    nepali="सम्पत्तिको विवरण"
                />


                <div class="info-banner">

                    <div class="info-icon">
                        i
                    </div>

                    <div>
                        <strong>
                            Property Location and Ownership
                        </strong>

                        <p>
                            सम्पत्तिको स्थान तथा स्वामित्वको सही विवरण भर्नुहोस्।
                        </p>
                    </div>

                </div>


                <div class="form-grid">

                    <!-- PROPERTY TYPE -->
                    <Field
                        label="Type of Property"
                        nepali="सम्पत्तिको प्रकार"
                        required
                        full
                    >

                        <div class="option-grid">

                            <label
                                v-for="item in propertyTypes"
                                :key="item.value"
                                class="option-card"
                                :class="{
                                    selected:
                                        form.property_type === item.value
                                }"
                            >

                                <input
                                    v-model="form.property_type"
                                    type="radio"
                                    :value="item.value"
                                />

                                <span>
                                    <strong>
                                        {{ item.english }}
                                    </strong>

                                    <small>
                                        {{ item.nepali }}
                                    </small>
                                </span>

                            </label>

                        </div>

                    </Field>


                    <Field
                        label="Province"
                        nepali="प्रदेश"
                        required
                    >
                        <input
                            v-model="form.province"
                            type="text"
                        />
                    </Field>


                    <Field
                        label="District"
                        nepali="जिल्ला"
                        required
                    >
                        <input
                            v-model="form.district"
                            type="text"
                        />
                    </Field>


                    <Field
                        label="Municipality / Rural Municipality"
                        nepali="नगरपालिका / गाउँपालिका"
                        required
                    >
                        <input
                            v-model="form.municipality"
                            type="text"
                        />
                    </Field>


                    <Field
                        label="Ward No."
                        nepali="वडा नं."
                        required
                    >
                        <input
                            v-model="form.ward_no"
                            type="text"
                        />
                    </Field>


                    <Field
                        label="Tole / Area"
                        nepali="टोल / क्षेत्र"
                    >
                        <input
                            v-model="form.tole_locality"
                            type="text"
                        />
                    </Field>


                    <Field
                        label="Plot No."
                        nepali="कित्ता नं."
                    >
                        <input
                            v-model="form.kitta_no"
                            type="text"
                        />
                    </Field>


                    <Field
                        label="Land Area"
                        nepali="जग्गाको क्षेत्रफल"
                    >
                        <input
                            v-model="form.area"
                            type="text"
                            placeholder="Example: 4-2-1-0"
                        />
                    </Field>


                    <Field
                        label="Map Sheet No."
                        nepali="नक्सा सिट नं."
                    >
                        <input
                            v-model="form.map_sheet_no"
                            type="text"
                        />
                    </Field>


                    <Field
                        label="Land Ownership Certificate No."
                        nepali="लालपुर्जा नं."
                    >
                        <input
                            v-model="form.ownership_certificate_no"
                            type="text"
                        />
                    </Field>


                    <Field
                        label="Land Ownership Type"
                        nepali="जग्गा स्वामित्व प्रकार"
                        full
                    >

                        <div class="radio-row">

                            <label
                                v-for="item in ownershipTypes"
                                :key="item.value"
                                class="radio-item"
                            >

                                <input
                                    v-model="form.ownership_type"
                                    type="radio"
                                    :value="item.value"
                                />

                                {{ item.english }}
                                /
                                {{ item.nepali }}

                            </label>

                        </div>

                    </Field>

                </div>

            </section>


            <!-- =====================================================
                 STEP 3 — BUILDING
            ====================================================== -->

            <section v-else-if="currentStep === 2">

                <SectionHeader
                    number="०३"
                    english="Building Details"
                    nepali="भवन विवरण"
                />


                <div class="building-note">

                    <span class="note-icon">
                        🏠
                    </span>

                    <div>

                        <strong>
                            Building Information / भवनको जानकारी
                        </strong>

                        <p>
                            घर वा भवन भएको सम्पत्तिको लागि मात्र तलको विवरण भर्नुहोस्।
                        </p>

                    </div>

                </div>


                <div class="form-grid">

                    <Field
                        label="Building Type"
                        nepali="भवनको प्रकार"
                    >
                        <input
                            v-model="form.building_type"
                            type="text"
                            placeholder="Residential / Commercial"
                        />
                    </Field>


                    <Field
                        label="Year of Construction"
                        nepali="निर्माण वर्ष"
                    >
                        <input
                            v-model.number="form.year_of_construction"
                            type="number"
                            min="1800"
                            max="2100"
                            placeholder="YYYY"
                        />
                    </Field>


                    <Field
                        label="Total Floor Area"
                        nepali="कुल क्षेत्रफल"
                    >
                        <input
                            v-model="form.covered_area"
                            type="text"
                            placeholder="Example: 2500 sq.ft"
                        />
                    </Field>


                    <Field
                        label="No. of Floors"
                        nepali="तल्ला संख्या"
                    >
                        <input
                            v-model.number="form.no_of_floors"
                            type="number"
                            min="0"
                        />
                    </Field>


                    <Field
                        label="Construction Material"
                        nepali="निर्माण सामग्री"
                        full
                    >

                        <div class="option-grid">

                            <label
                                v-for="item in structures"
                                :key="item.value"
                                class="option-card"
                                :class="{
                                    selected:
                                        form.structure_type === item.value
                                }"
                            >

                                <input
                                    v-model="form.structure_type"
                                    type="radio"
                                    :value="item.value"
                                />

                                <span>

                                    <strong>
                                        {{ item.english }}
                                    </strong>

                                    <small>
                                        {{ item.nepali }}
                                    </small>

                                </span>

                            </label>

                        </div>

                    </Field>


                    <Field
                        label="Current Condition of Building"
                        nepali="भवनको हालको अवस्था"
                        full
                    >

                        <div class="condition-grid">

                            <label
                                v-for="item in conditions"
                                :key="item.value"
                                class="condition-card"
                                :class="{
                                    selected:
                                        form.current_building_condition === item.value
                                }"
                            >

                                <input
                                    v-model="form.current_building_condition"
                                    type="radio"
                                    :value="item.value"
                                />

                                <span class="condition-icon">
                                    {{ item.icon }}
                                </span>

                                <strong>
                                    {{ item.english }}
                                </strong>

                                <small>
                                    {{ item.nepali }}
                                </small>

                            </label>

                        </div>

                    </Field>


                    <Field
                        label="Building Approval / Naksa Pass No."
                        nepali="भवन अनुमति / नक्सा पास नं."
                    >
                        <input
                            v-model="form.building_permit_no"
                            type="text"
                        />
                    </Field>


                    <Field
                        label="Road Access"
                        nepali="बाटो पहुँच"
                    >
                        <input
                            v-model="form.road_access"
                            type="text"
                            placeholder="Blacktop / Gravel / Other"
                        />
                    </Field>


                    <Field
                        label="Road Width"
                        nepali="बाटोको चौडाइ"
                    >
                        <input
                            v-model="form.road_width"
                            type="text"
                            placeholder="Example: 20 ft"
                        />
                    </Field>


                    <Field
                        label="Facing Direction"
                        nepali="मुख फर्किएको दिशा"
                    >

                        <select v-model="form.facing_direction">

                            <option value="">
                                Select / छान्नुहोस्
                            </option>

                            <option value="East">
                                East / पूर्व
                            </option>

                            <option value="West">
                                West / पश्चिम
                            </option>

                            <option value="North">
                                North / उत्तर
                            </option>

                            <option value="South">
                                South / दक्षिण
                            </option>

                            <option value="North-East">
                                North-East / उत्तर-पूर्व
                            </option>

                            <option value="North-West">
                                North-West / उत्तर-पश्चिम
                            </option>

                            <option value="South-East">
                                South-East / दक्षिण-पूर्व
                            </option>

                            <option value="South-West">
                                South-West / दक्षिण-पश्चिम
                            </option>

                        </select>

                    </Field>

                </div>

            </section>


            <!-- =====================================================
                 STEP 4 — PURPOSE
            ====================================================== -->

            <section v-else-if="currentStep === 3">

                <SectionHeader
                    number="०४"
                    english="Purpose of Valuation"
                    nepali="मूल्याङ्कनको उद्देश्य"
                />


                <div class="purpose-grid">

                    <label
                        v-for="item in purposes"
                        :key="item.value"
                        class="purpose-card"
                        :class="{
                            selected:
                                form.purpose_of_valuation === item.value
                        }"
                    >

                        <input
                            v-model="form.purpose_of_valuation"
                            type="radio"
                            :value="item.value"
                        />

                        <div class="purpose-check">
                            ✓
                        </div>

                        <div>

                            <strong>
                                {{ item.english }}
                            </strong>

                            <small>
                                {{ item.nepali }}
                            </small>

                        </div>

                    </label>

                </div>


                <div class="section-divider"></div>


                <div class="sub-title">
                    <span>
                        Requested Valuation Type / आवश्यक मूल्याङ्कन विवरण
                    </span>
                </div>


                <div class="valuation-grid">

                    <label
                        v-for="item in valuationTypes"
                        :key="item.value"
                        class="valuation-card"
                        :class="{
                            selected:
                                form.requested_valuation_type === item.value
                        }"
                    >

                        <input
                            v-model="form.requested_valuation_type"
                            type="radio"
                            :value="item.value"
                        />

                        <strong>
                            {{ item.english }}
                        </strong>

                        <small>
                            {{ item.nepali }}
                        </small>

                    </label>

                </div>


                <div class="remarks-box">

                    <label>
                        Remarks / थप विवरण
                    </label>

                    <textarea
                        v-model="form.remarks"
                        rows="4"
                        placeholder="Enter additional information / थप जानकारी भएमा यहाँ लेख्नुहोस्..."
                    ></textarea>

                </div>

            </section>


            <!-- =====================================================
                 STEP 5 — DOCUMENTS
            ====================================================== -->

            <section v-else-if="currentStep === 4">

                <SectionHeader
                    number="०५"
                    english="Available Documents Checklist"
                    nepali="उपलब्ध कागजात सूची"
                />


                <div class="document-table">

                    <div class="document-head">

                        <span>
                            S.N.
                        </span>

                        <span>
                            Document / कागजात
                        </span>

                        <span>
                            Available? / उपलब्ध?
                        </span>

                    </div>


                    <div
                        v-for="(document, index) in documents"
                        :key="document.key"
                        class="document-row"
                    >

                        <span class="document-number">
                            {{ index + 1 }}
                        </span>


                        <div class="document-name">

                            <strong>
                                {{ document.english }}
                            </strong>

                            <small>
                                {{ document.nepali }}
                            </small>

                        </div>


                        <div class="yes-no">

                            <label
                                :class="{
                                    checked:
                                        form.documents[document.key] === true
                                }"
                            >

                                <input
                                    v-model="form.documents[document.key]"
                                    type="radio"
                                    :name="document.key"
                                    :value="true"
                                />

                                Yes / हो

                            </label>


                            <label
                                :class="{
                                    checked:
                                        form.documents[document.key] === false
                                }"
                            >

                                <input
                                    v-model="form.documents[document.key]"
                                    type="radio"
                                    :name="document.key"
                                    :value="false"
                                />

                                No / होइन

                            </label>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =====================================================
                 STEP 6 — SITE VISIT
            ====================================================== -->

            <section v-else-if="currentStep === 5">

                <SectionHeader
                    number="०६"
                    english="Site Visit Information"
                    nepali="स्थलगत निरीक्षण विवरण"
                />


                <div class="visit-banner">

                    <div class="visit-icon">
                        📍
                    </div>

                    <div>

                        <strong>
                            Site Visit Schedule / स्थलगत निरीक्षणको समय
                        </strong>

                        <p>
                            मूल्याङ्कनकर्ताले सम्पत्ति निरीक्षण गर्न सक्ने
                            उपयुक्त मिति र समय दिनुहोस्।
                        </p>

                    </div>

                </div>


                <div class="form-grid">

                    <Field
                        label="Preferred Site Visit Date"
                        nepali="स्थलगत भ्रमणको मिति"
                        required
                    >
                        <input
                            v-model="form.preferred_visit_date"
                            type="date"
                        />
                    </Field>


                    <Field
                        label="Preferred Time"
                        nepali="समय"
                    >
                        <input
                            v-model="form.preferred_visit_time"
                            type="time"
                        />
                    </Field>


                    <Field
                        label="Contact Person at Site"
                        nepali="स्थलमा सम्पर्क व्यक्ति"
                        required
                    >
                        <input
                            v-model="form.site_contact_person_name"
                            type="text"
                            placeholder="Name / नाम"
                        />
                    </Field>


                    <Field
                        label="Mobile No."
                        nepali="मोबाइल नं."
                        required
                    >
                        <input
                            v-model="form.site_contact_mobile"
                            type="tel"
                            placeholder="98XXXXXXXX"
                        />
                    </Field>

                </div>

            </section>


            <!-- =====================================================
                 STEP 7 — DECLARATION
            ====================================================== -->

            <section v-else>

                <SectionHeader
                    number="०७"
                    english="Applicant Declaration"
                    nepali="आवेदकको घोषणा"
                />


                <div class="declaration">

                    <div class="declaration-title">
                        Declaration / घोषणा
                    </div>


                    <p>
                        म/हामी माथि उल्लेखित सम्पत्तिको मूल्याङ्कन कार्य गर्न
                        Api Ghar Jagga लाई अनुरोध गर्दछु/गर्दछौं।
                    </p>


                    <p>
                        यस फाराममा उपलब्ध गराइएको सम्पूर्ण विवरण मेरो/हाम्रो
                        जानकारी अनुसार सत्य र सही रहेको घोषणा गर्दछु/गर्दछौं।
                    </p>


                    <p class="english-text">
                        I/We hereby request Api Ghar Jagga to conduct
                        property valuation of the above-mentioned property.
                        I/We confirm that the information provided above is
                        true and correct to the best of my/our knowledge.
                    </p>

                </div>


                <label
                    class="agreement-checkbox"
                    :class="{ accepted: form.declaration_agreed }"
                >

                    <input
                        v-model="form.declaration_agreed"
                        type="checkbox"
                    />

                    <span>

                        I agree to the above declaration.
                        <small>
                            म माथिको घोषणा पढेर सहमत छु।
                        </small>

                    </span>

                </label>


                <div class="signature-grid">

                    <Field
                        label="Applicant Name"
                        nepali="आवेदकको नाम"
                    >
                        <input
                            v-model="form.signature_name"
                            type="text"
                        />
                    </Field>


                    <Field
                        label="Date"
                        nepali="मिति"
                    >
                        <input
                            v-model="form.signature_date"
                            type="date"
                        />
                    </Field>

                </div>


                <div class="final-note">

                    <strong>
                        For Office Use Only / कार्यालय प्रयोजनको लागि मात्र
                    </strong>

                    <p>
                        Application received date, assigned valuator,
                        field visit date and valuation report number
                        will be handled by the office.
                    </p>

                </div>

            </section>


            <!-- ================= ERROR ================= -->

            <div
                v-if="errorMessage"
                class="error-box"
            >
                {{ errorMessage }}
            </div>


            <!-- ================= BUTTONS ================= -->

            <div class="form-footer">

                <button
                    v-if="currentStep > 0"
                    type="button"
                    class="btn-secondary"
                    @click="currentStep--"
                >
                    ← Back / पछाडि
                </button>

                <div v-else></div>


                <button
                    type="submit"
                    class="btn-primary"
                    :disabled="saving"
                >

                    <span v-if="saving">
                        Saving... / सुरक्षित हुँदैछ...
                    </span>

                    <span v-else-if="currentStep < steps.length - 1">
                        Continue / अगाडि बढ्नुहोस् →
                    </span>

                    <span v-else>
                        ✓ Submit Valuation Request / मूल्याङ्कन अनुरोध पठाउनुहोस्
                    </span>

                </button>

            </div>

        </form>

    </div>
</template>


<script setup lang="ts">

import {
    computed,
    reactive,
    ref,
    defineComponent,
    h,
} from 'vue'

import axios from 'axios'


/* =====================================================
   FIELD COMPONENT
===================================================== */

const Field = defineComponent({

    props: {
        label: String,
        nepali: String,
        required: Boolean,
        full: Boolean,
    },

    setup(props, { slots }) {

        return () =>
            h(
                'div',
                {
                    class: props.full
                        ? 'field field-full'
                        : 'field',
                },
                [

                    h(
                        'label',
                        {
                            class: 'field-label',
                        },
                        [

                            h(
                                'span',
                                {},
                                props.label,
                            ),

                            props.required
                                ? h(
                                    'b',
                                    {
                                        class: 'required',
                                    },
                                    '*',
                                )
                                : null,

                            props.nepali
                                ? h(
                                    'small',
                                    {},
                                    `/ ${props.nepali}`,
                                )
                                : null,

                        ],
                    ),

                    ...(slots.default?.() || []),

                ],
            )
    },
})


/* =====================================================
   SECTION HEADER
===================================================== */

const SectionHeader = defineComponent({

    props: {
        number: String,
        english: String,
        nepali: String,
    },

    setup(props) {

        return () =>
            h(
                'div',
                {
                    class: 'section-header',
                },
                [

                    h(
                        'div',
                        {
                            class: 'section-number',
                        },
                        props.number,
                    ),

                    h(
                        'div',
                        {},
                        [

                            h(
                                'h2',
                                {},
                                props.english,
                            ),

                            h(
                                'p',
                                {},
                                `/ ${props.nepali}`,
                            ),

                        ],
                    ),

                ],
            )
    },
})


/* =====================================================
   STEPS
===================================================== */

const steps = [

    {
        english: 'Applicant',
        nepali: 'आवेदक',
    },

    {
        english: 'Property',
        nepali: 'सम्पत्ति',
    },

    {
        english: 'Building',
        nepali: 'भवन',
    },

    {
        english: 'Purpose',
        nepali: 'उद्देश्य',
    },

    {
        english: 'Documents',
        nepali: 'कागजात',
    },

    {
        english: 'Site Visit',
        nepali: 'स्थलगत निरीक्षण',
    },

    {
        english: 'Declaration',
        nepali: 'घोषणा',
    },

]


const currentStep = ref(0)

const maxStep = ref(0)

const saving = ref(false)

const errorMessage = ref('')


const progress = computed(() => {

    return (
        currentStep.value /
        (steps.length - 1)
    ) * 100

})


function goToStep(index: number) {

    if (index <= maxStep.value) {

        currentStep.value = index

    }

}


/* =====================================================
   FORM DATA
===================================================== */

const form = reactive<any>({

    /* Applicant */

    full_name: '',
    father_mother_name: '',
    citizenship_no: '',
    mobile_no: '',
    email: '',


    /* Permanent Address */

    permanent_province: '',
    permanent_district: '',
    permanent_municipality: '',
    permanent_ward_no: '',
    permanent_tole: '',
    permanent_full_address: '',


    /* Current Address */

    same_current_address: false,

    current_province: '',
    current_district: '',
    current_municipality: '',
    current_ward_no: '',
    current_tole: '',
    current_full_address: '',


    /* Property */

    property_type: '',
    province: '',
    district: '',
    municipality: '',
    ward_no: '',
    tole_locality: '',
    kitta_no: '',
    area: '',
    map_sheet_no: '',
    ownership_type: '',
    ownership_certificate_no: '',


    /* Building */

    building_type: '',
    year_of_construction: '',
    covered_area: '',
    no_of_floors: '',
    structure_type: '',
    current_building_condition: '',
    building_permit_no: '',
    road_access: '',
    road_width: '',
    facing_direction: '',


    /* Valuation */

    purpose_of_valuation: '',
    requested_valuation_type: '',
    remarks: '',


    /* Documents */

    documents: {

        land_ownership_certificate: null,

        citizenship_certificate: null,

        land_revenue_receipt: null,

        land_map_trace_map: null,

        building_approval_certificate: null,

        tax_clearance_certificate: null,

        other_documents: null,

    },


    /* Site Visit */

    preferred_visit_date: '',
    preferred_visit_time: '',
    site_contact_person_name: '',
    site_contact_mobile: '',


    /* Declaration */

    declaration_agreed: false,

    signature_name: '',

    signature_date: '',

})


/* =====================================================
   PROPERTY TYPES
===================================================== */

const propertyTypes = [

    {
        value: 'land',
        english: 'Land Only',
        nepali: 'जग्गा मात्र',
    },

    {
        value: 'house',
        english: 'House & Land',
        nepali: 'घर तथा जग्गा',
    },

    {
        value: 'commercial_building',
        english: 'Commercial Property',
        nepali: 'व्यापारिक सम्पत्ति',
    },

    {
        value: 'industrial_property',
        english: 'Industrial Property',
        nepali: 'औद्योगिक सम्पत्ति',
    },

    {
        value: 'other',
        english: 'Other',
        nepali: 'अन्य',
    },

]


/* =====================================================
   OWNERSHIP
===================================================== */

const ownershipTypes = [

    {
        value: 'private',
        english: 'Private Ownership',
        nepali: 'निजी स्वामित्व',
    },

    {
        value: 'joint',
        english: 'Joint Ownership',
        nepali: 'संयुक्त स्वामित्व',
    },

    {
        value: 'other',
        english: 'Other',
        nepali: 'अन्य',
    },

]


/* =====================================================
   BUILDING STRUCTURE
===================================================== */

const structures = [

    {
        value: 'RCC',
        english: 'RCC Frame',
        nepali: 'आर.सी.सी. फ्रेम',
    },

    {
        value: 'Load Bearing',
        english: 'Load Bearing',
        nepali: 'लोड बेयरिङ',
    },

    {
        value: 'Steel',
        english: 'Steel Structure',
        nepali: 'स्टिल संरचना',
    },

    {
        value: 'Other',
        english: 'Other',
        nepali: 'अन्य',
    },

]


/* =====================================================
   BUILDING CONDITION
===================================================== */

const conditions = [

    {
        value: 'excellent',
        icon: '★',
        english: 'Excellent',
        nepali: 'उत्कृष्ट',
    },

    {
        value: 'good',
        icon: '✓',
        english: 'Good',
        nepali: 'राम्रो',
    },

    {
        value: 'fair',
        icon: '●',
        english: 'Fair',
        nepali: 'सामान्य',
    },

    {
        value: 'poor',
        icon: '!',
        english: 'Poor',
        nepali: 'कमजोर',
    },

]


/* =====================================================
   PURPOSE
===================================================== */

const purposes = [

    {
        value: 'bank_loan_mortgage',
        english: 'Bank Loan / Mortgage Purpose',
        nepali: 'बैंक ऋण / धितो प्रयोजन',
    },

    {
        value: 'buying_selling',
        english: 'Buying & Selling Purpose',
        nepali: 'किनबेच प्रयोजन',
    },

    {
        value: 'insurance',
        english: 'Insurance Purpose',
        nepali: 'बीमा प्रयोजन',
    },

    {
        value: 'legal',
        english: 'Legal Purpose',
        nepali: 'कानुनी प्रयोजन',
    },

    {
        value: 'investment_decision',
        english: 'Investment Decision',
        nepali: 'लगानी निर्णय',
    },

    {
        value: 'other',
        english: 'Other',
        nepali: 'अन्य',
    },

]


/* =====================================================
   VALUATION TYPES
===================================================== */

const valuationTypes = [

    {
        value: 'market_value',
        english: 'Market Value',
        nepali: 'बजार मूल्य',
    },

    {
        value: 'forced_sale_value',
        english: 'Forced Sale Value',
        nepali: 'बाध्यकारी बिक्री मूल्य',
    },

    {
        value: 'government_value_reference',
        english: 'Government Value Reference',
        nepali: 'सरकारी मूल्य आधार',
    },

    {
        value: 'rental_value',
        english: 'Rental Value Assessment',
        nepali: 'भाडा मूल्याङ्कन',
    },

]


/* =====================================================
   DOCUMENTS
===================================================== */

const documents = [

    {
        key: 'land_ownership_certificate',
        english: 'Land Ownership Certificate',
        nepali: 'लालपुर्जा',
    },

    {
        key: 'citizenship_certificate',
        english: 'Citizenship Certificate',
        nepali: 'नागरिकता प्रमाणपत्र',
    },

    {
        key: 'land_revenue_receipt',
        english: 'Land Revenue Receipt',
        nepali: 'मालपोत रसिद',
    },

    {
        key: 'land_map_trace_map',
        english: 'Land Map / Trace Map',
        nepali: 'नक्सा / ट्रेस नक्सा',
    },

    {
        key: 'building_approval_certificate',
        english: 'Building Approval Certificate',
        nepali: 'नक्सा स्वीकृति',
    },

    {
        key: 'tax_clearance_certificate',
        english: 'Tax Clearance Certificate',
        nepali: 'कर चुक्ता प्रमाणपत्र',
    },

    {
        key: 'other_documents',
        english: 'Other Documents',
        nepali: 'अन्य कागजात',
    },

]


/* =====================================================
   COPY ADDRESS
===================================================== */

function copyPermanentAddress() {

    if (!form.same_current_address) {

        return

    }

    form.current_province =
        form.permanent_province

    form.current_district =
        form.permanent_district

    form.current_municipality =
        form.permanent_municipality

    form.current_ward_no =
        form.permanent_ward_no

    form.current_tole =
        form.permanent_tole

    form.current_full_address =
        form.permanent_full_address

}


/* =====================================================
   NEXT / SUBMIT
===================================================== */

async function nextOrSubmit() {

    errorMessage.value = ''


    /* Continue to next step */

    if (
        currentStep.value <
        steps.length - 1
    ) {

        currentStep.value++

        maxStep.value =
            Math.max(
                maxStep.value,
                currentStep.value,
            )

        return

    }


    /* Declaration validation */

    if (!form.declaration_agreed) {

        errorMessage.value =
            'Please agree to the declaration. / कृपया घोषणामा सहमत हुनुहोस्।'

        return

    }


    saving.value = true


    try {

        /*
         * IMPORTANT:
         * Your web.php uses:
         *
         * POST /annex-c
         *
         * Therefore we submit to /annex-c.
         */

        const response =
            await axios.post(
                '/annex-c',
                form,
                {
                    headers: {
                        Accept:
                            'application/json',
                    },
                },
            )


        alert(
            'Valuation request submitted successfully.\nमूल्याङ्कन अनुरोध सफलतापूर्वक पठाइयो।'
        )


        console.log(
            'Saved valuation request:',
            response.data,
        )


        window.location.reload()


    } catch (error: any) {

        console.error(error)


        errorMessage.value =
            error.response?.data?.message ||
            'Unable to submit valuation request. / मूल्याङ्कन अनुरोध पठाउन सकिएन।'

    } finally {

        saving.value = false

    }

}

</script>


<style scoped>

/* =====================================================
   PAGE
===================================================== */

.annex-page {

    min-height: 100vh;

    padding: 45px 20px 70px;

    background: #f4f8f6;

    color: #17211d;

    font-family:
        Inter,
        ui-sans-serif,
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;

}


/* =====================================================
   HEADER
===================================================== */

.page-header {

    max-width: 1050px;

    margin: 0 auto 35px;

    text-align: center;

}


.document-code {

    display: inline-flex;

    align-items: center;

    gap: 10px;

    padding: 8px 16px;

    border: 1px solid #cfe4da;

    border-radius: 999px;

    background: white;

    color: #087f5b;

    font-size: 12px;

    font-weight: 800;

    letter-spacing: 1px;

}


.document-code span {

    width: 6px;

    height: 6px;

    border-radius: 50%;

    background: #0ca678;

}


.page-header h1 {

    margin: 18px 0 4px;

    color: #17211d;

    font-size: clamp(28px, 4vw, 42px);

    font-weight: 900;

    letter-spacing: -1px;

}


.page-header h2 {

    margin: 0;

    color: #087f5b;

    font-size: 20px;

    font-weight: 800;

}


.page-header p {

    margin: 8px 0 0;

    color: #75837d;

    font-size: 14px;

}


/* =====================================================
   PROGRESS
===================================================== */

.progress-wrapper {

    position: relative;

    max-width: 900px;

    margin: 0 auto 30px;

}


.progress-line {

    position: absolute;

    top: 20px;

    left: 40px;

    right: 40px;

    height: 3px;

    background: #dce8e2;

    z-index: 0;

}


.progress-active {

    height: 100%;

    background: #0ca678;

    transition: width .3s ease;

}


.steps {

    position: relative;

    z-index: 1;

    display: flex;

    justify-content: space-between;

}


.step {

    display: flex;

    flex-direction: column;

    align-items: center;

    gap: 5px;

    border: 0;

    background: transparent;

    cursor: pointer;

}


.step-number {

    display: flex;

    align-items: center;

    justify-content: center;

    width: 40px;

    height: 40px;

    border: 3px solid #dce8e2;

    border-radius: 50%;

    background: white;

    color: #899791;

    font-size: 13px;

    font-weight: 900;

}


.step.active .step-number {

    border-color: #bcebd8;

    background: #0ca678;

    color: white;

}


.step-label {

    color: #73827b;

    font-size: 11px;

    font-weight: 800;

}


.step small {

    color: #9aa7a1;

    font-size: 9px;

}


.step.active .step-label,

.step.active small {

    color: #087f5b;

}


/* =====================================================
   FORM CARD
===================================================== */

.form-card {

    max-width: 1050px;

    margin: 0 auto;

    padding: 34px;

    border: 1px solid #dce8e2;

    border-radius: 24px;

    background: white;

    box-shadow:
        0 12px 35px
        rgba(20, 45, 35, .07);

}


/* =====================================================
   SECTION HEADER
===================================================== */

.section-header {

    display: flex;

    align-items: center;

    gap: 15px;

    margin-bottom: 28px;

}


.section-number {

    display: flex;

    align-items: center;

    justify-content: center;

    width: 48px;

    height: 48px;

    flex-shrink: 0;

    border-radius: 15px;

    background: #087f5b;

    color: white;

    font-size: 14px;

    font-weight: 900;

}


.section-header h2 {

    margin: 0;

    color: #087f5b;

    font-size: 23px;

    font-weight: 900;

}


.section-header p {

    margin: 4px 0 0;

    color: #7a8781;

    font-size: 13px;

}


/* =====================================================
   GRID
===================================================== */

.form-grid {

    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 22px;

}


.field-full {

    grid-column: 1 / -1;

}


/* =====================================================
   FIELD
===================================================== */

.field {

    min-width: 0;

}


.field-label {

    display: flex;

    flex-wrap: wrap;

    align-items: baseline;

    gap: 5px;

    margin-bottom: 8px;

    color: #087f5b;

    font-size: 13px;

    font-weight: 800;

}


.field-label small {

    width: 100%;

    color: #087f5b;

    font-size: 11px;

    font-weight: 500;

}


.required {

    color: #e05252;

}


.field input,
.field textarea,
.field select {

    width: 100%;

    box-sizing: border-box;

    border: 1px solid #d5e3dc;

    border-radius: 12px;

    outline: none;

    background: #fff;

    color: #1e2b25;

    padding: 12px 14px;

    font-family: inherit;

    font-size: 14px;

    transition: .2s;

}


.field textarea {

    resize: vertical;

}


.field input:focus,
.field textarea:focus,
.field select:focus {

    border-color: #0ca678;

    box-shadow:
        0 0 0 4px
        rgba(12, 166, 120, .10);

}


/* =====================================================
   SUB SECTION
===================================================== */

.sub-section {

    margin-top: 30px;

    padding-top: 26px;

    border-top: 1px solid #e5eee9;

}


.sub-title {

    margin-bottom: 18px;

}


.sub-title span {

    display: block;

    color: #087f5b;

    font-size: 16px;

    font-weight: 900;

}


/* =====================================================
   SAME ADDRESS
===================================================== */

.same-address {

    display: flex;

    align-items: center;

    gap: 10px;

    margin-bottom: 20px;

    padding: 13px 15px;

    border: 1px solid #d8ebe2;

    border-radius: 12px;

    background: #f3fbf7;

    cursor: pointer;

    color: #087f5b;

    font-size: 13px;

    font-weight: 800;

}


.same-address small {

    display: block;

    color: #73827b;

    font-size: 10px;

    font-weight: 500;

}


/* =====================================================
   INFORMATION BANNERS
===================================================== */

.info-banner,
.building-note,
.visit-banner {

    display: flex;

    gap: 14px;

    align-items: flex-start;

    margin-bottom: 25px;

    padding: 16px;

    border: 1px solid #d3eee2;

    border-radius: 15px;

    background: #f1fbf6;

}


.info-icon {

    display: flex;

    align-items: center;

    justify-content: center;

    width: 30px;

    height: 30px;

    flex-shrink: 0;

    border-radius: 50%;

    background: #0ca678;

    color: white;

    font-weight: 900;

}


.note-icon {

    font-size: 25px;

}


.info-banner strong,
.building-note strong,
.visit-banner strong {

    display: block;

    color: #087f5b;

    font-size: 13px;

}


.info-banner p,
.building-note p,
.visit-banner p {

    margin: 3px 0 0;

    color: #698078;

    font-size: 12px;

}


/* =====================================================
   OPTIONS
===================================================== */

.option-grid {

    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 10px;

}


.option-card {

    display: flex;

    align-items: center;

    gap: 12px;

    padding: 14px;

    border: 1px solid #d8e5de;

    border-radius: 13px;

    cursor: pointer;

    transition: .2s;

}


.option-card:hover,
.option-card.selected {

    border-color: #0ca678;

    background: #f1fbf6;

}


.option-card input,
.purpose-card input,
.valuation-card input,
.condition-card input {

    accent-color: #0ca678;

}


.option-card strong,
.condition-card strong {

    display: block;

    color: #087f5b;

    font-size: 13px;

}


.option-card small,
.condition-card small {

    display: block;

    margin-top: 3px;

    color: #087f5b;

    font-size: 10px;

}


/* =====================================================
   RADIO
===================================================== */

.radio-row {

    display: flex;

    flex-wrap: wrap;

    gap: 10px;

}


.radio-item {

    display: flex;

    align-items: center;

    gap: 8px;

    padding: 11px 14px;

    border: 1px solid #d8e5de;

    border-radius: 12px;

    background: white;

    cursor: pointer;

    color: #087f5b;

    font-size: 12px;

    font-weight: 700;

}


/* =====================================================
   CONDITION
===================================================== */

.condition-grid {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 10px;

}


.condition-card {

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    gap: 7px;

    min-height: 105px;

    border: 1px solid #d8e5de;

    border-radius: 14px;

    text-align: center;

    cursor: pointer;

}


.condition-card.selected {

    border-color: #0ca678;

    background: #f1fbf6;

}


.condition-icon {

    display: flex;

    align-items: center;

    justify-content: center;

    width: 30px;

    height: 30px;

    border-radius: 50%;

    background: #e5f7ef;

    color: #087f5b;

    font-weight: 900;

}


/* =====================================================
   PURPOSE
===================================================== */

.purpose-grid {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 12px;

}


.purpose-card {

    display: flex;

    align-items: center;

    gap: 14px;

    padding: 17px;

    border: 1px solid #d8e5de;

    border-radius: 15px;

    cursor: pointer;

}


.purpose-card.selected {

    border-color: #0ca678;

    background: #f1fbf6;

}


.purpose-check {

    display: flex;

    align-items: center;

    justify-content: center;

    width: 32px;

    height: 32px;

    border-radius: 10px;

    background: #edf3f0;

    color: #9ca9a3;

    font-weight: 900;

}


.purpose-card.selected .purpose-check {

    background: #0ca678;

    color: white;

}


.purpose-card strong {

    display: block;

    color: #087f5b;

    font-size: 13px;

}


.purpose-card small {

    display: block;

    margin-top: 3px;

    color: #087f5b;

    font-size: 10px;

}


/* =====================================================
   VALUATION
===================================================== */

.section-divider {

    height: 1px;

    margin: 30px 0;

    background: #e5eee9;

}


.valuation-grid {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 12px;

}


.valuation-card {

    padding: 18px;

    border: 1px solid #d8e5de;

    border-radius: 15px;

    cursor: pointer;

}


.valuation-card.selected {

    border-color: #0ca678;

    background: #f1fbf6;

}


.valuation-card strong {

    display: block;

    margin-top: 10px;

    color: #087f5b;

    font-size: 13px;

}


.valuation-card small {

    display: block;

    margin-top: 4px;

    color: #087f5b;

    font-size: 10px;

}


.remarks-box {

    margin-top: 22px;

}


.remarks-box label {

    display: block;

    margin-bottom: 8px;

    color: #087f5b;

    font-size: 13px;

    font-weight: 800;

}


.remarks-box textarea {

    width: 100%;

    box-sizing: border-box;

    padding: 13px;

    border: 1px solid #d8e5de;

    border-radius: 13px;

    resize: vertical;

    outline: none;

}


/* =====================================================
   DOCUMENT TABLE
===================================================== */

.document-table {

    overflow: hidden;

    border: 1px solid #d8e5de;

    border-radius: 16px;

}


.document-head,
.document-row {

    display: grid;

    grid-template-columns:
        70px 1fr 180px;

    align-items: center;

}


.document-head {

    padding: 14px 18px;

    background: #087f5b;

    color: white;

    font-size: 12px;

    font-weight: 800;

}


.document-row {

    min-height: 72px;

    padding: 0 18px;

    border-top: 1px solid #e5eee9;

}


.document-number {

    color: #087f5b;

    font-weight: 800;

}


.document-name strong {

    display: block;

    color: #087f5b;

    font-size: 13px;

}


.document-name small {

    display: block;

    margin-top: 3px;

    color: #087f5b;

    font-size: 10px;

}


.yes-no {

    display: flex;

    gap: 10px;

}


.yes-no label {

    padding: 8px 12px;

    border: 1px solid #d8e5de;

    border-radius: 9px;

    cursor: pointer;

    color: #087f5b;

    font-size: 12px;

    font-weight: 700;

}


.yes-no label.checked {

    border-color: #0ca678;

    background: #e7f8f0;

    color: #087f5b;

}


/* =====================================================
   VISIT
===================================================== */

.visit-icon {

    font-size: 26px;

}


/* =====================================================
   DECLARATION
===================================================== */

.declaration {

    padding: 24px;

    border: 1px solid #d8e5de;

    border-radius: 18px;

    background: #f6faf8;

    line-height: 1.8;

}


.declaration-title {

    margin-bottom: 12px;

    color: #087f5b;

    font-size: 16px;

    font-weight: 900;

}


.declaration p {

    margin: 8px 0;

    color: #56655e;

    font-size: 13px;

}


.english-text {

    padding-top: 10px;

    border-top: 1px solid #dce8e2;

    font-size: 12px !important;

}


.agreement-checkbox {

    display: flex;

    align-items: center;

    gap: 12px;

    margin-top: 20px;

    padding: 16px;

    border: 1px solid #d8e5de;

    border-radius: 14px;

    cursor: pointer;

    color: #087f5b;

    font-weight: 800;

}


.agreement-checkbox.accepted {

    border-color: #0ca678;

    background: #f1fbf6;

}


.agreement-checkbox small {

    display: block;

    color: #087f5b;

    font-size: 10px;

    font-weight: 500;

}


.signature-grid {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 20px;

    margin-top: 25px;

}


.final-note {

    margin-top: 25px;

    padding: 16px;

    border: 1px dashed #b9d9cc;

    border-radius: 14px;

    background: #f5faf8;

}


.final-note strong {

    color: #087f5b;

    font-size: 12px;

}


.final-note p {

    margin: 5px 0 0;

    color: #7b8982;

    font-size: 11px;

}


/* =====================================================
   ERROR
===================================================== */

.error-box {

    margin-top: 20px;

    padding: 14px 16px;

    border: 1px solid #f0caca;

    border-radius: 12px;

    background: #fff3f3;

    color: #bd4040;

    font-size: 13px;

    font-weight: 700;

}


/* =====================================================
   FOOTER BUTTONS
===================================================== */

.form-footer {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-top: 32px;

    padding-top: 24px;

    border-top: 1px solid #e5eee9;

}


.btn-primary,
.btn-secondary {

    border: 0;

    border-radius: 13px;

    padding: 13px 22px;

    cursor: pointer;

    font-family: inherit;

    font-size: 13px;

    font-weight: 900;

}


.btn-primary {

    background: #087f5b;

    color: white;

    box-shadow:
        0 7px 18px
        rgba(8, 127, 91, .22);

}


.btn-primary:hover {

    background: #066b4d;

}


.btn-primary:disabled {

    opacity: .55;

    cursor: not-allowed;

}


.btn-secondary {

    border: 1px solid #cfe1d8;

    background: white;

    color: #087f5b;

}


.btn-secondary:hover {

    background: #f1fbf6;

}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 800px) {

    .annex-page {

        padding:
            25px
            12px
            50px;

    }


    .form-card {

        padding: 22px;

        border-radius: 18px;

    }


    .form-grid {

        grid-template-columns: 1fr;

    }


    .field-full {

        grid-column: auto;

    }


    .option-grid,
    .purpose-grid {

        grid-template-columns: 1fr;

    }


    .condition-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }


    .valuation-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }


    .document-head,
    .document-row {

        grid-template-columns:
            45px 1fr;

    }


    .document-head span:last-child {

        display: none;

    }


    .yes-no {

        grid-column: 2;

        margin-bottom: 12px;

    }


    .signature-grid {

        grid-template-columns: 1fr;

    }


    .step-label,
    .step small {

        display: none;

    }

}


@media (max-width: 520px) {

    .page-header h1 {

        font-size: 27px;

    }


    .page-header h2 {

        font-size: 16px;

    }


    .steps {

        gap: 4px;

    }


    .step-number {

        width: 32px;

        height: 32px;

        font-size: 11px;

    }


    .progress-line {

        top: 15px;

        left: 16px;

        right: 16px;

    }


    .condition-grid,
    .valuation-grid {

        grid-template-columns:
            1fr 1fr;

    }


    .form-footer {

        gap: 10px;

    }


    .btn-primary,
    .btn-secondary {

        padding: 11px 15px;

    }

}

</style>