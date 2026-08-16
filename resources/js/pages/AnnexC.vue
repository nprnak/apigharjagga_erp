<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    owner_name: '',
    father_husband_name: '',
    citizenship_no: '',
    contact_no: '',
    email: '',

    permanent_province: '',
    permanent_district: '',
    permanent_municipality: '',
    permanent_ward: '',
    permanent_tole: '',

    current_province: '',
    current_district: '',
    current_municipality: '',
    current_ward: '',
    current_tole: '',

    property_type: '',
    property_type_other: '',

    province: '',
    district: '',
    municipality: '',
    ward_no: '',
    tole_area: '',
    kitta_no: '',
    land_area: '',

    ownership_type: '',
    ownership_type_other: '',

    building_type: '',
    year_of_construction: '',
    total_floor_area: '',
    no_of_floors: '',
    construction_material: '',
    construction_material_other: '',
    building_condition: '',

    purpose_of_valuation: '',
    purpose_other: '',
    requested_valuation_type: '',

    lalpurja: false,
    citizenship_document: false,
    land_revenue_receipt: false,
    land_map: false,
    building_approval: false,
    tax_clearance: false,
    other_document: false,

    preferred_visit_date: '',
    preferred_visit_time: '',
    site_contact_person_name: '',
    site_contact_mobile: '',

    declaration: false,
})

const submit = () => {
    form.post('/annex-c', {
        preserveScroll: true,
    })
}
</script>

<template>
    <div class="annex-page">
        <div class="form-wrapper">

            <!-- HEADER -->
            <header class="form-header">
                <div class="header-top">
                    <div class="brand-block">
                        <div class="brand-logo">AGJ</div>

                        <div>
                            <div class="brand-name">
                                API GHAR JAGGA
                            </div>

                            <div class="brand-subtitle">
                                Real Estate Management System
                            </div>
                        </div>
                    </div>

                    <div class="document-info">
                        <div class="document-label">
                            FORM
                        </div>

                        <div class="document-number">
                            ANNEX-C
                        </div>
                    </div>
                </div>

                <div class="header-title">
                    <h1>
                        PROPERTY VALUATION REQUEST FORM
                    </h1>

                    <h2>
                        सम्पत्ति मूल्याङ्कन अनुरोध फाराम
                    </h2>

                    <p>
                        कृपया तलका सबै आवश्यक विवरणहरू स्पष्ट र सही रूपमा भर्नुहोस्।
                    </p>
                </div>
            </header>


            <form @submit.prevent="submit">

                <!-- SECTION 01 -->
                <section class="form-section">

                    <div class="section-heading">
                        <div class="section-number">
                            01
                        </div>

                        <div>
                            <h3>
                                Applicant / Property Owner
                            </h3>

                            <p>
                                आवेदक / सम्पत्ति धनीको विवरण
                            </p>
                        </div>
                    </div>


                    <div class="field-grid field-grid-2">

                        <div class="form-field">
                            <label>
                                Property Owner Name
                                <span>*</span>
                            </label>

                            <input
                                v-model="form.owner_name"
                                type="text"
                                placeholder="सम्पत्ति धनीको पूरा नाम"
                            />
                        </div>


                        <div class="form-field">
                            <label>
                                Father's / Husband's Name
                            </label>

                            <input
                                v-model="form.father_husband_name"
                                type="text"
                                placeholder="बुबा / श्रीमानको नाम"
                            />
                        </div>


                        <div class="form-field">
                            <label>
                                Citizenship Number
                                <span>*</span>
                            </label>

                            <input
                                v-model="form.citizenship_no"
                                type="text"
                                placeholder="नागरिकता नम्बर"
                            />
                        </div>


                        <div class="form-field">
                            <label>
                                Contact Number
                                <span>*</span>
                            </label>

                            <input
                                v-model="form.contact_no"
                                type="text"
                                placeholder="९८XXXXXXXX"
                            />
                        </div>


                        <div class="form-field full-width">
                            <label>
                                Email Address
                            </label>

                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="example@email.com"
                            />
                        </div>

                    </div>


                    <!-- PERMANENT ADDRESS -->
                    <div class="subsection">

                        <div class="subsection-title">
                            <span class="subsection-icon">A</span>

                            <div>
                                <strong>
                                    Permanent Address
                                </strong>

                                <small>
                                    स्थायी ठेगाना
                                </small>
                            </div>
                        </div>


                        <div class="field-grid field-grid-3">

                            <div class="form-field">
                                <label>Province / प्रदेश</label>
                                <input v-model="form.permanent_province" />
                            </div>

                            <div class="form-field">
                                <label>District / जिल्ला</label>
                                <input v-model="form.permanent_district" />
                            </div>

                            <div class="form-field">
                                <label>Ward No. / वडा नं.</label>
                                <input v-model="form.permanent_ward" />
                            </div>

                            <div class="form-field span-2">
                                <label>
                                    Municipality / Rural Municipality
                                </label>

                                <input
                                    v-model="form.permanent_municipality"
                                    placeholder="नगरपालिका / गाउँपालिका"
                                />
                            </div>

                            <div class="form-field">
                                <label>Tole / Area / टोल</label>
                                <input v-model="form.permanent_tole" />
                            </div>

                        </div>

                    </div>


                    <!-- CURRENT ADDRESS -->
                    <div class="subsection">

                        <div class="subsection-title">
                            <span class="subsection-icon">B</span>

                            <div>
                                <strong>
                                    Current Address
                                </strong>

                                <small>
                                    हालको ठेगाना
                                </small>
                            </div>
                        </div>


                        <div class="field-grid field-grid-3">

                            <div class="form-field">
                                <label>Province / प्रदेश</label>
                                <input v-model="form.current_province" />
                            </div>

                            <div class="form-field">
                                <label>District / जिल्ला</label>
                                <input v-model="form.current_district" />
                            </div>

                            <div class="form-field">
                                <label>Ward No. / वडा नं.</label>
                                <input v-model="form.current_ward" />
                            </div>

                            <div class="form-field span-2">
                                <label>
                                    Municipality / Rural Municipality
                                </label>

                                <input
                                    v-model="form.current_municipality"
                                    placeholder="नगरपालिका / गाउँपालिका"
                                />
                            </div>

                            <div class="form-field">
                                <label>Tole / Area / टोल</label>
                                <input v-model="form.current_tole" />
                            </div>

                        </div>

                    </div>

                </section>


                <!-- SECTION 02 -->
                <section class="form-section">

                    <div class="section-heading">
                        <div class="section-number">
                            02
                        </div>

                        <div>
                            <h3>
                                Property Details
                            </h3>

                            <p>
                                सम्पत्तिको विवरण
                            </p>
                        </div>
                    </div>


                    <div class="form-field">

                        <label>
                            Type of Property / सम्पत्तिको प्रकार
                            <span>*</span>
                        </label>


                        <div class="choice-grid">

                            <label class="choice-card">
                                <input
                                    v-model="form.property_type"
                                    type="radio"
                                    value="land"
                                />

                                <span class="choice-content">
                                    <strong>Land Only</strong>
                                    <small>जग्गा मात्र</small>
                                </span>
                            </label>


                            <label class="choice-card">
                                <input
                                    v-model="form.property_type"
                                    type="radio"
                                    value="house"
                                />

                                <span class="choice-content">
                                    <strong>House & Land</strong>
                                    <small>घर तथा जग्गा</small>
                                </span>
                            </label>


                            <label class="choice-card">
                                <input
                                    v-model="form.property_type"
                                    type="radio"
                                    value="commercial_building"
                                />

                                <span class="choice-content">
                                    <strong>Commercial Property</strong>
                                    <small>व्यापारिक सम्पत्ति</small>
                                </span>
                            </label>


                            <label class="choice-card">
                                <input
                                    v-model="form.property_type"
                                    type="radio"
                                    value="industrial_property"
                                />

                                <span class="choice-content">
                                    <strong>Industrial Property</strong>
                                    <small>औद्योगिक सम्पत्ति</small>
                                </span>
                            </label>


                            <label class="choice-card">
                                <input
                                    v-model="form.property_type"
                                    type="radio"
                                    value="other"
                                />

                                <span class="choice-content">
                                    <strong>Other</strong>
                                    <small>अन्य</small>
                                </span>
                            </label>

                        </div>


                        <input
                            v-if="form.property_type === 'other'"
                            v-model="form.property_type_other"
                            class="other-input"
                            placeholder="अन्य सम्पत्तिको प्रकार"
                        />

                    </div>


                    <div class="field-grid field-grid-3">

                        <div class="form-field">
                            <label>Province / प्रदेश</label>
                            <input v-model="form.province" />
                        </div>

                        <div class="form-field">
                            <label>District / जिल्ला</label>
                            <input v-model="form.district" />
                        </div>

                        <div class="form-field">
                            <label>Ward No. / वडा नं.</label>
                            <input v-model="form.ward_no" />
                        </div>

                        <div class="form-field span-2">
                            <label>
                                Municipality / Rural Municipality
                            </label>

                            <input
                                v-model="form.municipality"
                                placeholder="नगरपालिका / गाउँपालिका"
                            />
                        </div>

                        <div class="form-field">
                            <label>Tole / Area / टोल</label>
                            <input v-model="form.tole_area" />
                        </div>

                        <div class="form-field">
                            <label>Kitta / Plot No. / कित्ता नं.</label>
                            <input v-model="form.kitta_no" />
                        </div>

                        <div class="form-field">
                            <label>
                                Land Area / जग्गाको क्षेत्रफल
                            </label>

                            <input
                                v-model="form.land_area"
                                placeholder="उदा. ४ आना"
                            />
                        </div>

                    </div>


                    <div class="form-field">

                        <label>
                            Ownership Type / स्वामित्वको प्रकार
                        </label>

                        <div class="choice-grid choice-grid-3">

                            <label class="choice-card compact">
                                <input
                                    v-model="form.ownership_type"
                                    type="radio"
                                    value="private"
                                />

                                <span class="choice-content">
                                    <strong>Private</strong>
                                    <small>निजी</small>
                                </span>
                            </label>


                            <label class="choice-card compact">
                                <input
                                    v-model="form.ownership_type"
                                    type="radio"
                                    value="joint"
                                />

                                <span class="choice-content">
                                    <strong>Joint</strong>
                                    <small>संयुक्त</small>
                                </span>
                            </label>


                            <label class="choice-card compact">
                                <input
                                    v-model="form.ownership_type"
                                    type="radio"
                                    value="other"
                                />

                                <span class="choice-content">
                                    <strong>Other</strong>
                                    <small>अन्य</small>
                                </span>
                            </label>

                        </div>

                    </div>

                </section>


                <!-- SECTION 03 -->
                <section class="form-section">

                    <div class="section-heading">
                        <div class="section-number">
                            03
                        </div>

                        <div>
                            <h3>
                                Building Details
                            </h3>

                            <p>
                                भवनको विवरण
                            </p>
                        </div>
                    </div>


                    <div class="field-grid field-grid-2">

                        <div class="form-field">
                            <label>
                                Building Type / भवनको प्रकार
                            </label>

                            <input
                                v-model="form.building_type"
                                placeholder="भवनको प्रकार"
                            />
                        </div>


                        <div class="form-field">
                            <label>
                                Year of Construction / निर्माण वर्ष
                            </label>

                            <input
                                v-model="form.year_of_construction"
                                type="number"
                                placeholder="२०८०"
                            />
                        </div>


                        <div class="form-field">
                            <label>
                                Total Floor Area / कुल क्षेत्रफल
                            </label>

                            <input
                                v-model="form.total_floor_area"
                                placeholder="वर्ग फिट / वर्ग मिटर"
                            />
                        </div>


                        <div class="form-field">
                            <label>
                                Number of Floors / तल्ला संख्या
                            </label>

                            <input
                                v-model="form.no_of_floors"
                                type="number"
                                placeholder="उदा. ३"
                            />
                        </div>

                    </div>


                    <div class="form-field">

                        <label>
                            Construction Material / निर्माण सामग्री
                        </label>

                        <div class="choice-grid">

                            <label class="choice-card">
                                <input
                                    v-model="form.construction_material"
                                    type="radio"
                                    value="RCC"
                                />

                                <span class="choice-content">
                                    <strong>RCC Frame</strong>
                                    <small>आर.सी.सी.</small>
                                </span>
                            </label>


                            <label class="choice-card">
                                <input
                                    v-model="form.construction_material"
                                    type="radio"
                                    value="Load Bearing"
                                />

                                <span class="choice-content">
                                    <strong>Load Bearing</strong>
                                    <small>लोड बेयरिङ</small>
                                </span>
                            </label>


                            <label class="choice-card">
                                <input
                                    v-model="form.construction_material"
                                    type="radio"
                                    value="Steel"
                                />

                                <span class="choice-content">
                                    <strong>Steel Structure</strong>
                                    <small>स्टिल संरचना</small>
                                </span>
                            </label>


                            <label class="choice-card">
                                <input
                                    v-model="form.construction_material"
                                    type="radio"
                                    value="Other"
                                />

                                <span class="choice-content">
                                    <strong>Other</strong>
                                    <small>अन्य</small>
                                </span>
                            </label>

                        </div>

                    </div>


                    <div class="form-field">

                        <label>
                            Current Building Condition / भवनको अवस्था
                        </label>

                        <div class="choice-grid choice-grid-4">

                            <label class="choice-card compact">
                                <input
                                    v-model="form.building_condition"
                                    type="radio"
                                    value="excellent"
                                />

                                <span class="choice-content">
                                    <strong>Excellent</strong>
                                    <small>उत्कृष्ट</small>
                                </span>
                            </label>


                            <label class="choice-card compact">
                                <input
                                    v-model="form.building_condition"
                                    type="radio"
                                    value="good"
                                />

                                <span class="choice-content">
                                    <strong>Good</strong>
                                    <small>राम्रो</small>
                                </span>
                            </label>


                            <label class="choice-card compact">
                                <input
                                    v-model="form.building_condition"
                                    type="radio"
                                    value="fair"
                                />

                                <span class="choice-content">
                                    <strong>Fair</strong>
                                    <small>सामान्य</small>
                                </span>
                            </label>


                            <label class="choice-card compact">
                                <input
                                    v-model="form.building_condition"
                                    type="radio"
                                    value="poor"
                                />

                                <span class="choice-content">
                                    <strong>Poor</strong>
                                    <small>कमजोर</small>
                                </span>
                            </label>

                        </div>

                    </div>

                </section>


                <!-- SECTION 04 -->
                <section class="form-section">

                    <div class="section-heading">
                        <div class="section-number">
                            04
                        </div>

                        <div>
                            <h3>
                                Purpose of Valuation
                            </h3>

                            <p>
                                मूल्याङ्कनको उद्देश्य
                            </p>
                        </div>
                    </div>


                    <div class="choice-grid">

                        <label class="choice-card">
                            <input
                                v-model="form.purpose_of_valuation"
                                type="radio"
                                value="bank_loan_mortgage"
                            />

                            <span class="choice-content">
                                <strong>Bank Loan / Mortgage</strong>
                                <small>बैंक ऋण / धितो</small>
                            </span>
                        </label>


                        <label class="choice-card">
                            <input
                                v-model="form.purpose_of_valuation"
                                type="radio"
                                value="buying_selling"
                            />

                            <span class="choice-content">
                                <strong>Buying & Selling</strong>
                                <small>किनबेच</small>
                            </span>
                        </label>


                        <label class="choice-card">
                            <input
                                v-model="form.purpose_of_valuation"
                                type="radio"
                                value="insurance"
                            />

                            <span class="choice-content">
                                <strong>Insurance</strong>
                                <small>बीमा</small>
                            </span>
                        </label>


                        <label class="choice-card">
                            <input
                                v-model="form.purpose_of_valuation"
                                type="radio"
                                value="legal"
                            />

                            <span class="choice-content">
                                <strong>Legal</strong>
                                <small>कानुनी</small>
                            </span>
                        </label>


                        <label class="choice-card">
                            <input
                                v-model="form.purpose_of_valuation"
                                type="radio"
                                value="investment_decision"
                            />

                            <span class="choice-content">
                                <strong>Investment Decision</strong>
                                <small>लगानी निर्णय</small>
                            </span>
                        </label>


                        <label class="choice-card">
                            <input
                                v-model="form.purpose_of_valuation"
                                type="radio"
                                value="other"
                            />

                            <span class="choice-content">
                                <strong>Other</strong>
                                <small>अन्य</small>
                            </span>
                        </label>

                    </div>


                    <input
                        v-if="form.purpose_of_valuation === 'other'"
                        v-model="form.purpose_other"
                        class="other-input"
                        placeholder="अन्य उद्देश्य लेख्नुहोस्"
                    />

                </section>


                <!-- SECTION 05 -->
                <section class="form-section">

                    <div class="section-heading">
                        <div class="section-number">
                            05
                        </div>

                        <div>
                            <h3>
                                Required Valuation Type
                            </h3>

                            <p>
                                आवश्यक मूल्याङ्कनको प्रकार
                            </p>
                        </div>
                    </div>


                    <div class="choice-grid choice-grid-2">

                        <label class="choice-card">
                            <input
                                v-model="form.requested_valuation_type"
                                type="radio"
                                value="market_value"
                            />

                            <span class="choice-content">
                                <strong>Market Value</strong>
                                <small>बजार मूल्य</small>
                            </span>
                        </label>


                        <label class="choice-card">
                            <input
                                v-model="form.requested_valuation_type"
                                type="radio"
                                value="forced_sale_value"
                            />

                            <span class="choice-content">
                                <strong>Forced Sale Value</strong>
                                <small>बाध्यकारी बिक्री मूल्य</small>
                            </span>
                        </label>


                        <label class="choice-card">
                            <input
                                v-model="form.requested_valuation_type"
                                type="radio"
                                value="government_value_reference"
                            />

                            <span class="choice-content">
                                <strong>Government Value Reference</strong>
                                <small>सरकारी मूल्य आधार</small>
                            </span>
                        </label>


                        <label class="choice-card">
                            <input
                                v-model="form.requested_valuation_type"
                                type="radio"
                                value="rental_value"
                            />

                            <span class="choice-content">
                                <strong>Rental Value</strong>
                                <small>भाडा मूल्य</small>
                            </span>
                        </label>

                    </div>

                </section>


                <!-- SECTION 06 -->
                <section class="form-section">

                    <div class="section-heading">
                        <div class="section-number">
                            06
                        </div>

                        <div>
                            <h3>
                                Available Documents
                            </h3>

                            <p>
                                उपलब्ध कागजातहरू
                            </p>
                        </div>
                    </div>


                    <div class="document-grid">

                        <label class="document-card">
                            <input
                                v-model="form.lalpurja"
                                type="checkbox"
                            />

                            <span>
                                <strong>Land Ownership Certificate</strong>
                                <small>लालपूर्जा</small>
                            </span>
                        </label>


                        <label class="document-card">
                            <input
                                v-model="form.citizenship_document"
                                type="checkbox"
                            />

                            <span>
                                <strong>Citizenship Certificate</strong>
                                <small>नागरिकता प्रमाणपत्र</small>
                            </span>
                        </label>


                        <label class="document-card">
                            <input
                                v-model="form.land_revenue_receipt"
                                type="checkbox"
                            />

                            <span>
                                <strong>Land Revenue Receipt</strong>
                                <small>मालपोत रसिद</small>
                            </span>
                        </label>


                        <label class="document-card">
                            <input
                                v-model="form.land_map"
                                type="checkbox"
                            />

                            <span>
                                <strong>Land / Trace Map</strong>
                                <small>नक्सा / ट्रेस नक्सा</small>
                            </span>
                        </label>


                        <label class="document-card">
                            <input
                                v-model="form.building_approval"
                                type="checkbox"
                            />

                            <span>
                                <strong>Building Approval</strong>
                                <small>भवन नक्सा स्वीकृति</small>
                            </span>
                        </label>


                        <label class="document-card">
                            <input
                                v-model="form.tax_clearance"
                                type="checkbox"
                            />

                            <span>
                                <strong>Tax Clearance</strong>
                                <small>कर चुक्ता प्रमाणपत्र</small>
                            </span>
                        </label>


                        <label class="document-card">
                            <input
                                v-model="form.other_document"
                                type="checkbox"
                            />

                            <span>
                                <strong>Other Documents</strong>
                                <small>अन्य कागजात</small>
                            </span>
                        </label>

                    </div>

                </section>


                <!-- SECTION 07 -->
                <section class="form-section">

                    <div class="section-heading">
                        <div class="section-number">
                            07
                        </div>

                        <div>
                            <h3>
                                Site Visit Information
                            </h3>

                            <p>
                                स्थलगत निरीक्षण विवरण
                            </p>
                        </div>
                    </div>


                    <div class="field-grid field-grid-2">

                        <div class="form-field">
                            <label>
                                Preferred Visit Date
                            </label>

                            <input
                                v-model="form.preferred_visit_date"
                                type="date"
                            />
                        </div>


                        <div class="form-field">
                            <label>
                                Preferred Visit Time
                            </label>

                            <input
                                v-model="form.preferred_visit_time"
                                type="time"
                            />
                        </div>


                        <div class="form-field">
                            <label>
                                Contact Person at Site
                            </label>

                            <input
                                v-model="form.site_contact_person_name"
                                placeholder="स्थलमा सम्पर्क गर्ने व्यक्तिको नाम"
                            />
                        </div>


                        <div class="form-field">
                            <label>
                                Mobile Number
                            </label>

                            <input
                                v-model="form.site_contact_mobile"
                                placeholder="९८XXXXXXXX"
                            />
                        </div>

                    </div>

                </section>


                <!-- SECTION 08 -->
                <section class="form-section declaration-section">

                    <div class="section-heading">
                        <div class="section-number">
                            08
                        </div>

                        <div>
                            <h3>
                                Applicant Declaration
                            </h3>

                            <p>
                                आवेदकको घोषणा
                            </p>
                        </div>
                    </div>


                    <div class="declaration-box">

                        <p>
                            I/We hereby request Api Ghar Jagga to conduct
                            property valuation of the above-mentioned property.
                        </p>

                        <p>
                            म/हामी माथि उल्लेखित सम्पत्तिको मूल्याङ्कन गर्न
                            Api Ghar Jagga लाई अनुरोध गर्दछु/गर्दछौं।
                        </p>


                        <label class="declaration-check">

                            <input
                                v-model="form.declaration"
                                type="checkbox"
                            />

                            <span>
                                I confirm that the information provided above
                                is true and correct.
                                <br />

                                <small>
                                    माथि प्रदान गरिएको विवरण सही र सत्य भएको
                                    पुष्टि गर्दछु।
                                </small>
                            </span>

                        </label>

                    </div>

                </section>


                <!-- FOOTER -->
                <div class="form-footer">

                    <div class="required-note">
                        <span>*</span>
                        Required fields
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="submit-button"
                    >
                        <span v-if="!form.processing">
                            Submit Valuation Request
                        </span>

                        <span v-else>
                            Submitting...
                        </span>
                    </button>

                </div>

            </form>

        </div>
    </div>
</template>


<style scoped>
/* =========================================================
   PAGE
========================================================= */

.annex-page {
    min-height: 100vh;
    padding: 42px 24px;
    background: #f4f6f8;
    color: #172033;
    font-family:
        Inter,
        ui-sans-serif,
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;
}


/* =========================================================
   MAIN CONTAINER
========================================================= */

.form-wrapper {
    width: 100%;
    max-width: 1120px;
    margin: 0 auto;
    overflow: hidden;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    box-shadow:
        0 8px 30px rgba(15, 23, 42, 0.06);
}


/* =========================================================
   HEADER
========================================================= */

.form-header {
    padding: 28px 38px 32px;
    border-bottom: 1px solid #e5e7eb;
    background: #ffffff;
}


.header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
}


.brand-block {
    display: flex;
    align-items: center;
    gap: 13px;
}


.brand-logo {
    width: 48px;
    height: 48px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: #166534;
    color: white;

    font-size: 14px;
    font-weight: 800;
    letter-spacing: 0.5px;
}


.brand-name {
    color: #172033;
    font-size: 16px;
    font-weight: 800;
    letter-spacing: 0.4px;
}


.brand-subtitle {
    margin-top: 3px;
    color: #7b8494;
    font-size: 12px;
}


.document-info {
    min-width: 110px;
    padding: 10px 14px;

    border: 1px solid #dfe4ea;
    border-radius: 8px;

    text-align: center;
}


.document-label {
    color: #7b8494;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1px;
}


.document-number {
    margin-top: 3px;
    color: #166534;
    font-size: 15px;
    font-weight: 800;
}


.header-title {
    margin-top: 30px;
    text-align: center;
}


.header-title h1 {
    margin: 0;

    color: #172033;

    font-size: 24px;
    font-weight: 800;
    letter-spacing: 0.3px;
}


.header-title h2 {
    margin: 7px 0 0;

    color: #4b5563;

    font-size: 17px;
    font-weight: 600;
}


.header-title p {
    margin: 10px 0 0;

    color: #7b8494;
    font-size: 13px;
}


/* =========================================================
   SECTION
========================================================= */

.form-section {
    padding: 34px 38px;

    border-bottom: 1px solid #e9edf1;
}


.section-heading {
    display: flex;
    align-items: center;
    gap: 14px;

    margin-bottom: 27px;
}


.section-number {
    width: 42px;
    height: 42px;

    flex: 0 0 42px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 9px;

    background: #166534;
    color: #ffffff;

    font-size: 13px;
    font-weight: 800;
}


.section-heading h3 {
    margin: 0;

    color: #172033;

    font-size: 18px;
    font-weight: 750;
}


.section-heading p {
    margin: 4px 0 0;

    color: #7b8494;

    font-size: 13px;
}


/* =========================================================
   FIELD GRID
========================================================= */

.field-grid {
    display: grid;
    gap: 20px;
}


.field-grid-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}


.field-grid-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}


.full-width {
    grid-column: 1 / -1;
}


.span-2 {
    grid-column: span 2;
}


/* =========================================================
   FORM FIELD
========================================================= */

.form-field {
    min-width: 0;
}


.form-field label {
    display: block;

    margin-bottom: 8px;

    color: #374151;

    font-size: 13px;
    font-weight: 650;
}


.form-field label span {
    margin-left: 2px;
    color: #dc2626;
}


.form-field input[type="text"],
.form-field input[type="email"],
.form-field input[type="number"],
.form-field input[type="date"],
.form-field input[type="time"] {
    display: block;

    width: 100%;
    height: 44px;

    padding: 0 13px;

    border: 1px solid #d7dce2;
    border-radius: 7px;

    outline: none;

    background: #ffffff;
    color: #172033;

    font-size: 14px;

    transition:
        border-color 0.15s ease,
        box-shadow 0.15s ease;
}


.form-field input::placeholder {
    color: #a0a8b5;
}


.form-field input:focus {
    border-color: #166534;

    box-shadow:
        0 0 0 3px rgba(22, 101, 52, 0.08);
}


/* =========================================================
   SUBSECTIONS
========================================================= */

.subsection {
    margin-top: 28px;
    padding: 22px;

    border: 1px solid #e4e8ed;
    border-radius: 10px;

    background: #fafbfc;
}


.subsection-title {
    display: flex;
    align-items: center;
    gap: 10px;

    margin-bottom: 20px;
}


.subsection-icon {
    width: 28px;
    height: 28px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 6px;

    background: #e8f3ec;
    color: #166534;

    font-size: 12px;
    font-weight: 800;
}


.subsection-title strong {
    display: block;

    color: #374151;

    font-size: 14px;
}


.subsection-title small {
    display: block;

    margin-top: 2px;

    color: #8a93a1;

    font-size: 12px;
}


/* =========================================================
   CHOICES
========================================================= */

.choice-grid {
    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 12px;
}


.choice-grid-2 {
    grid-template-columns:
        repeat(2, minmax(0, 1fr));
}


.choice-grid-3 {
    grid-template-columns:
        repeat(3, minmax(0, 1fr));
}


.choice-grid-4 {
    grid-template-columns:
        repeat(4, minmax(0, 1fr));
}


.choice-card {
    min-width: 0;

    display: flex;
    align-items: center;

    gap: 11px;

    min-height: 64px;

    padding: 11px 13px;

    border: 1px solid #dfe4e9;
    border-radius: 8px;

    background: #ffffff;

    cursor: pointer;

    transition:
        border-color 0.15s ease,
        background 0.15s ease;
}


.choice-card:hover {
    border-color: #a9c8b3;
    background: #f8fcf9;
}


.choice-card:has(input:checked) {
    border-color: #166534;
    background: #f3faf5;
}


.choice-card input[type="radio"] {
    width: 16px;
    height: 16px;

    flex: 0 0 16px;

    margin: 0;

    accent-color: #166534;
}


.choice-content {
    min-width: 0;
}


.choice-content strong {
    display: block;

    color: #374151;

    font-size: 13px;
    font-weight: 650;
}


.choice-content small {
    display: block;

    margin-top: 3px;

    color: #8a93a1;

    font-size: 11px;
}


.compact {
    min-height: 58px;
}


.other-input {
    width: 100%;
    height: 44px;

    margin-top: 12px;

    padding: 0 13px;

    border: 1px solid #d7dce2;
    border-radius: 7px;

    outline: none;

    font-size: 14px;
}


.other-input:focus {
    border-color: #166534;
}


/* =========================================================
   DOCUMENTS
========================================================= */

.document-grid {
    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 12px;
}


.document-card {
    min-height: 66px;

    display: flex;
    align-items: center;

    gap: 12px;

    padding: 12px 14px;

    border: 1px solid #e1e5ea;
    border-radius: 8px;

    background: #ffffff;

    cursor: pointer;
}


.document-card:hover {
    background: #f8faf9;
}


.document-card:has(input:checked) {
    border-color: #166534;
    background: #f3faf5;
}


.document-card input {
    width: 17px;
    height: 17px;

    flex: 0 0 17px;

    accent-color: #166534;
}


.document-card strong {
    display: block;

    color: #374151;

    font-size: 13px;
}


.document-card small {
    display: block;

    margin-top: 3px;

    color: #8a93a1;

    font-size: 11px;
}


/* =========================================================
   DECLARATION
========================================================= */

.declaration-section {
    border-bottom: none;
}


.declaration-box {
    padding: 20px;

    border: 1px solid #dfe7e2;
    border-radius: 9px;

    background: #f7faf8;
}


.declaration-box p {
    margin: 0 0 10px;

    color: #4b5563;

    font-size: 13px;
    line-height: 1.7;
}


.declaration-check {
    display: flex;
    align-items: flex-start;

    gap: 11px;

    margin-top: 18px;
    padding-top: 16px;

    border-top: 1px solid #e0e8e3;

    cursor: pointer;
}


.declaration-check input {
    width: 17px;
    height: 17px;

    flex: 0 0 17px;

    margin-top: 2px;

    accent-color: #166534;
}


.declaration-check span {
    color: #374151;

    font-size: 13px;
    font-weight: 600;
    line-height: 1.6;
}


.declaration-check small {
    color: #7b8494;
    font-size: 12px;
    font-weight: 400;
}


/* =========================================================
   FOOTER
========================================================= */

.form-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 22px 38px;

    border-top: 1px solid #e5e7eb;

    background: #fafbfc;
}


.required-note {
    color: #7b8494;
    font-size: 12px;
}


.required-note span {
    color: #dc2626;
}


.submit-button {
    min-width: 220px;
    height: 46px;

    padding: 0 22px;

    border: none;
    border-radius: 7px;

    background: #166534;
    color: #ffffff;

    font-size: 13px;
    font-weight: 700;

    cursor: pointer;

    transition:
        background 0.15s ease,
        transform 0.15s ease;
}


.submit-button:hover {
    background: #14532d;
    transform: translateY(-1px);
}


.submit-button:disabled {
    opacity: 0.65;
    cursor: not-allowed;
    transform: none;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 900px) {

    .field-grid-3 {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }


    .choice-grid-4 {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }


    .choice-grid {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

}


@media (max-width: 650px) {

    .annex-page {
        padding: 12px;
    }


    .form-header {
        padding: 22px 18px 25px;
    }


    .form-section {
        padding: 25px 18px;
    }


    .header-top {
        align-items: flex-start;
    }


    .document-info {
        min-width: 85px;
    }


    .header-title {
        margin-top: 24px;
    }


    .header-title h1 {
        font-size: 19px;
    }


    .header-title h2 {
        font-size: 15px;
    }


    .field-grid-2,
    .field-grid-3,
    .choice-grid,
    .choice-grid-2,
    .choice-grid-3,
    .choice-grid-4,
    .document-grid {
        grid-template-columns: 1fr;
    }


    .span-2,
    .full-width {
        grid-column: auto;
    }


    .subsection {
        padding: 16px;
    }


    .form-footer {
        align-items: stretch;
        flex-direction: column;
        padding: 20px 18px;
    }


    .submit-button {
        width: 100%;
    }

}
</style>