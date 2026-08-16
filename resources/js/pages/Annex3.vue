<script setup lang="ts">
import { computed, reactive } from 'vue'

const form = reactive({
    // =========================
    // agreements
    // =========================
    agreement_type: '',
    property_id: '',
    agreement_date: '',
    place: '',

    total_price: '',
    advance_payment: '',
    balance_payment: '',
    final_payment_date: '',

    commission_rate_percent: '',
    commission_fixed_amount: '',

    agreement_period_months: '',
    termination_notice_days: '',

    status: 'active',
    governing_law: 'नेपालमा प्रचलित कानून बमोजिम',

    // =========================
    // property
    // =========================
    property_code: '',
    ownership_role: '',
    property_type: '',
    kitta_no: '',
    area: '',
    map_sheet_no: '',
    ownership_type: '',
    ownership_certificate_no: '',
    road_access: '',
    road_width: '',
    facing_direction: '',

    year_of_construction: '',
    no_of_floors: '',
    covered_area: '',
    structure_type: '',
    roof_type: '',
    parking: '',
    water_supply: '',
    electricity: '',
    internet: '',
    drainage: '',
    building_permit_no: '',
    current_building_condition: '',

    // =========================
    // property address
    // addresses
    // =========================
    province: '',
    district: '',
    municipality: '',
    ward_no: '',
    tole_locality: '',
    full_address_text: '',

    // =========================
    // property owner / client
    // =========================
    client_code: '',
    client_type: 'owner',
    full_name: '',
    father_mother_name: '',
    spouse_name: '',
    grandfather_name: '',
    citizenship_no: '',
    nationality: 'Nepali',
    mobile_no: '',
    alt_contact_no: '',
    telephone_no: '',
    email: '',

    // =========================
    // agreement party
    // =========================
    party_role: 'property_owner',
    representative_name: '',
    designation: '',

    // =========================
    // witnesses
    // =========================
    witness1_full_name: '',
    witness1_citizenship_no: '',
    witness1_address: '',

    witness2_full_name: '',
    witness2_citizenship_no: '',
    witness2_address: '',
})

const isSalePurchase = computed(
    () => form.agreement_type === 'sale_purchase',
)

const isListingBrokerage = computed(
    () => form.agreement_type === 'listing_brokerage',
)

function calculateBalance() {
    const total = Number(form.total_price || 0)
    const advance = Number(form.advance_payment || 0)

    if (total >= advance) {
        form.balance_payment = String(total - advance)
    }
}

function resetForm() {
    window.location.reload()
}

function submitForm() {
    console.log('ANNEX 3 FRONTEND DATA')
    console.log(JSON.stringify(form, null, 2))

    alert(
        'फारम तयार भयो ।\n\nअहिले कुनै Database वा Backend मा data पठाइएको छैन ।',
    )
}
</script>

<template>
    <div class="annex-page">

        <!-- =====================================
             HEADER
        ====================================== -->
        <header class="annex-header">

            <div class="header-left">

                <div class="breadcrumb">
                    सम्पत्ति व्यवस्थापन
                    <span>/</span>
                    सम्झौता
                    <span>/</span>
                    Annex 3
                </div>

                <div class="title-row">
                    <div class="document-icon">
                        📄
                    </div>

                    <div>
                        <h1>ANNEX 3</h1>
                        <p>
                            सम्पत्ति सम्झौता तथा ब्रोकरेज फारम
                        </p>
                    </div>
                </div>

            </div>

            <div class="header-status">
                <span class="status-dot"></span>
                Frontend Form
            </div>

        </header>


        <form
            class="annex-form"
            @submit.prevent="submitForm"
        >

            <!-- =====================================
                 01 AGREEMENT INFORMATION
            ====================================== -->
            <section class="form-section">

                <div class="section-header">

                    <div class="section-number">
                        ०१
                    </div>

                    <div>
                        <h2>सम्झौताको विवरण</h2>
                        <p>
                            Agreement सम्बन्धी आधारभूत जानकारी
                        </p>
                    </div>

                </div>


                <div class="form-grid">

                    <div class="field">
                        <label>
                            सम्झौताको प्रकार
                            <b>*</b>
                        </label>

                        <select v-model="form.agreement_type">
                            <option value="">
                                सम्झौताको प्रकार छान्नुहोस्
                            </option>

                            <option value="sale_purchase">
                                बिक्री / खरिद सम्झौता
                            </option>

                            <option value="listing_brokerage">
                                Listing / Brokerage सम्झौता
                            </option>
                        </select>

                        <small>
                            Database: agreement_type
                        </small>
                    </div>


                    <div class="field">
                        <label>
                            सम्झौताको मिति
                            <b>*</b>
                        </label>

                        <input
                            v-model="form.agreement_date"
                            type="date"
                        />

                        <small>
                            Database: agreement_date
                        </small>
                    </div>


                    <div class="field">
                        <label>
                            सम्झौता भएको स्थान
                        </label>

                        <input
                            v-model="form.place"
                            type="text"
                            placeholder="उदाहरण: काठमाडौं"
                        />

                        <small>
                            Database: place
                        </small>
                    </div>


                    <div class="field">
                        <label>
                            सम्झौताको अवस्था
                        </label>

                        <select v-model="form.status">

                            <option value="draft">
                                Draft
                            </option>

                            <option value="active">
                                Active
                            </option>

                            <option value="completed">
                                Completed
                            </option>

                            <option value="terminated">
                                Terminated
                            </option>

                            <option value="breached">
                                Breached
                            </option>

                        </select>

                        <small>
                            Database: status
                        </small>
                    </div>

                </div>

            </section>


            <!-- =====================================
                 02 PROPERTY
            ====================================== -->
            <section class="form-section">

                <div class="section-header">

                    <div class="section-number">
                        ०२
                    </div>

                    <div>
                        <h2>सम्पत्तिको विवरण</h2>
                        <p>
                            सम्झौतासँग सम्बन्धित सम्पत्तिको विवरण
                        </p>
                    </div>

                </div>


                <div class="info-banner">
                    <span>ⓘ</span>

                    <div>
                        <strong>Property ID सम्बन्धी जानकारी</strong>

                        <p>
                            अहिले frontend मात्र बनाइएकोले Property ID
                            database बाट load गरिएको छैन।
                        </p>
                    </div>
                </div>


                <div class="form-grid">

                    <div class="field">
                        <label>
                            Property ID
                        </label>

                        <input
                            v-model="form.property_id"
                            type="text"
                            placeholder="Property ID"
                        />

                        <small>
                            Database: property_id
                        </small>
                    </div>


                    <div class="field">
                        <label>
                            Property Code
                        </label>

                        <input
                            v-model="form.property_code"
                            type="text"
                            placeholder="उदाहरण: PROP-0001"
                        />

                        <small>
                            Database: property_code
                        </small>
                    </div>


                    <div class="field">
                        <label>
                            सम्पत्तिको प्रकार
                            <b>*</b>
                        </label>

                        <select v-model="form.property_type">

                            <option value="">
                                सम्पत्तिको प्रकार छान्नुहोस्
                            </option>

                            <option value="land">
                                जग्गा
                            </option>

                            <option value="house">
                                घर
                            </option>

                            <option value="apartment">
                                अपार्टमेन्ट
                            </option>

                            <option value="commercial_building">
                                व्यावसायिक भवन
                            </option>

                            <option value="office_space">
                                कार्यालय स्थान
                            </option>

                            <option value="industrial_property">
                                औद्योगिक सम्पत्ति
                            </option>

                            <option value="agricultural_land">
                                कृषि जग्गा
                            </option>

                            <option value="other">
                                अन्य
                            </option>

                        </select>

                        <small>
                            Database: property_type
                        </small>
                    </div>


                    <div class="field">
                        <label>
                            Ownership Role
                        </label>

                        <select v-model="form.ownership_role">

                            <option value="">
                                छान्नुहोस्
                            </option>

                            <option value="self">
                                आफैं
                            </option>

                            <option value="family_member">
                                परिवारको सदस्य
                            </option>

                            <option value="authorized_representative">
                                अधिकृत प्रतिनिधि
                            </option>

                            <option value="company">
                                कम्पनी
                            </option>

                        </select>

                        <small>
                            Database: ownership_role
                        </small>
                    </div>


                    <div class="field">
                        <label>
                            कित्ता नं.
                        </label>

                        <input
                            v-model="form.kitta_no"
                            type="text"
                            placeholder="कित्ता नम्बर"
                        />

                        <small>
                            Database: kitta_no
                        </small>
                    </div>


                    <div class="field">
                        <label>
                            क्षेत्रफल
                        </label>

                        <input
                            v-model="form.area"
                            type="text"
                            placeholder="उदाहरण: 4-2-1"
                        />

                        <small>
                            Database: area
                        </small>
                    </div>


                    <div class="field">
                        <label>
                            नक्सा सिट नं.
                        </label>

                        <input
                            v-model="form.map_sheet_no"
                            type="text"
                            placeholder="Map Sheet Number"
                        />

                        <small>
                            Database: map_sheet_no
                        </small>
                    </div>


                    <div class="field">
                        <label>
                            स्वामित्वको प्रकार
                        </label>

                        <select v-model="form.ownership_type">

                            <option value="">
                                छान्नुहोस्
                            </option>

                            <option value="private">
                                निजी
                            </option>

                            <option value="joint">
                                संयुक्त
                            </option>

                            <option value="other">
                                अन्य
                            </option>

                        </select>

                        <small>
                            Database: ownership_type
                        </small>
                    </div>


                    <div class="field">
                        <label>
                            लालपुर्जा नं.
                        </label>

                        <input
                            v-model="form.ownership_certificate_no"
                            type="text"
                            placeholder="Ownership Certificate Number"
                        />

                        <small>
                            Database: ownership_certificate_no
                        </small>
                    </div>


                    <div class="field">
                        <label>
                            सडक पहुँच
                        </label>

                        <input
                            v-model="form.road_access"
                            type="text"
                            placeholder="उदाहरण: छ / छैन"
                        />

                        <small>
                            Database: road_access
                        </small>
                    </div>


                    <div class="field">
                        <label>
                            सडकको चौडाइ
                        </label>

                        <input
                            v-model="form.road_width"
                            type="text"
                            placeholder="उदाहरण: 20 ft"
                        />

                        <small>
                            Database: road_width
                        </small>
                    </div>


                    <div class="field">
                        <label>
                            मोहडा / Facing Direction
                        </label>

                        <select v-model="form.facing_direction">

                            <option value="">
                                छान्नुहोस्
                            </option>

                            <option value="east">
                                पूर्व
                            </option>

                            <option value="west">
                                पश्चिम
                            </option>

                            <option value="north">
                                उत्तर
                            </option>

                            <option value="south">
                                दक्षिण
                            </option>

                            <option value="northeast">
                                उत्तर-पूर्व
                            </option>

                            <option value="northwest">
                                उत्तर-पश्चिम
                            </option>

                            <option value="southeast">
                                दक्षिण-पूर्व
                            </option>

                            <option value="southwest">
                                दक्षिण-पश्चिम
                            </option>

                        </select>

                        <small>
                            Database: facing_direction
                        </small>
                    </div>

                </div>

            </section>


            <!-- =====================================
                 03 PROPERTY ADDRESS
            ====================================== -->
            <section class="form-section">

                <div class="section-header">

                    <div class="section-number">
                        ०३
                    </div>

                    <div>
                        <h2>सम्पत्तिको ठेगाना</h2>
                        <p>
                            Property को address information
                        </p>
                    </div>

                </div>


                <div class="form-grid">

                    <div class="field">
                        <label>
                            प्रदेश
                        </label>

                        <input
                            v-model="form.province"
                            type="text"
                            placeholder="प्रदेश"
                        />
                    </div>


                    <div class="field">
                        <label>
                            जिल्ला
                        </label>

                        <input
                            v-model="form.district"
                            type="text"
                            placeholder="जिल्ला"
                        />
                    </div>


                    <div class="field">
                        <label>
                            नगरपालिका / गाउँपालिका
                        </label>

                        <input
                            v-model="form.municipality"
                            type="text"
                            placeholder="नगरपालिका / गाउँपालिका"
                        />
                    </div>


                    <div class="field">
                        <label>
                            वडा नं.
                        </label>

                        <input
                            v-model="form.ward_no"
                            type="text"
                            placeholder="वडा नम्बर"
                        />
                    </div>


                    <div class="field">
                        <label>
                            टोल / स्थानीय क्षेत्र
                        </label>

                        <input
                            v-model="form.tole_locality"
                            type="text"
                            placeholder="टोल / स्थानीय क्षेत्र"
                        />
                    </div>


                    <div class="field full">
                        <label>
                            पूरा ठेगाना
                        </label>

                        <textarea
                            v-model="form.full_address_text"
                            rows="3"
                            placeholder="सम्पत्तिको पूरा ठेगाना लेख्नुहोस्"
                        ></textarea>
                    </div>

                </div>

            </section>


            <!-- =====================================
                 04 OWNER / CLIENT
            ====================================== -->
            <section class="form-section">

                <div class="section-header">

                    <div class="section-number">
                        ०४
                    </div>

                    <div>
                        <h2>सम्पत्ति धनीको विवरण</h2>
                        <p>
                            Property owner / client information
                        </p>
                    </div>

                </div>


                <div class="form-grid">

                    <div class="field">
                        <label>
                            Client Code
                        </label>

                        <input
                            v-model="form.client_code"
                            type="text"
                            placeholder="Client Code"
                        />
                    </div>


                    <div class="field">
                        <label>
                            Client Type
                        </label>

                        <select v-model="form.client_type">

                            <option value="owner">
                                सम्पत्ति धनी
                            </option>

                            <option value="buyer">
                                खरिदकर्ता
                            </option>

                            <option value="investor">
                                लगानीकर्ता
                            </option>

                            <option value="tenant">
                                भाडामा लिने
                            </option>

                            <option value="agent">
                                एजेन्ट
                            </option>

                            <option value="other">
                                अन्य
                            </option>

                        </select>
                    </div>


                    <div class="field">
                        <label>
                            पूरा नाम
                            <b>*</b>
                        </label>

                        <input
                            v-model="form.full_name"
                            type="text"
                            placeholder="सम्पत्ति धनीको पूरा नाम"
                        />
                    </div>


                    <div class="field">
                        <label>
                            बाबु / आमाको नाम
                        </label>

                        <input
                            v-model="form.father_mother_name"
                            type="text"
                            placeholder="बाबु / आमाको नाम"
                        />
                    </div>


                    <div class="field">
                        <label>
                            पति / पत्नीको नाम
                        </label>

                        <input
                            v-model="form.spouse_name"
                            type="text"
                            placeholder="पति / पत्नीको नाम"
                        />
                    </div>


                    <div class="field">
                        <label>
                            बाजेको नाम
                        </label>

                        <input
                            v-model="form.grandfather_name"
                            type="text"
                            placeholder="बाजेको नाम"
                        />
                    </div>


                    <div class="field">
                        <label>
                            नागरिकता नं.
                        </label>

                        <input
                            v-model="form.citizenship_no"
                            type="text"
                            placeholder="नागरिकता नम्बर"
                        />
                    </div>


                    <div class="field">
                        <label>
                            राष्ट्रियता
                        </label>

                        <input
                            v-model="form.nationality"
                            type="text"
                            placeholder="Nationality"
                        />
                    </div>


                    <div class="field">
                        <label>
                            मोबाइल नं.
                        </label>

                        <input
                            v-model="form.mobile_no"
                            type="tel"
                            placeholder="98XXXXXXXX"
                        />
                    </div>


                    <div class="field">
                        <label>
                            वैकल्पिक सम्पर्क नं.
                        </label>

                        <input
                            v-model="form.alt_contact_no"
                            type="tel"
                            placeholder="Alternative Contact"
                        />
                    </div>


                    <div class="field">
                        <label>
                            टेलिफोन नं.
                        </label>

                        <input
                            v-model="form.telephone_no"
                            type="tel"
                            placeholder="Telephone"
                        />
                    </div>


                    <div class="field">
                        <label>
                            इमेल
                        </label>

                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="example@email.com"
                        />
                    </div>

                </div>

            </section>


            <!-- =====================================
                 05 BUILDING DETAILS
            ====================================== -->
            <section
                v-if="
                    form.property_type === 'house' ||
                    form.property_type === 'apartment' ||
                    form.property_type === 'commercial_building' ||
                    form.property_type === 'office_space' ||
                    form.property_type === 'industrial_property'
                "
                class="form-section"
            >

                <div class="section-header">

                    <div class="section-number">
                        ०५
                    </div>

                    <div>
                        <h2>भवन सम्बन्धी विवरण</h2>
                        <p>
                            Property database मा रहेका building fields
                        </p>
                    </div>

                </div>


                <div class="form-grid">

                    <div class="field">
                        <label>
                            निर्माण वर्ष
                        </label>

                        <input
                            v-model="form.year_of_construction"
                            type="number"
                            placeholder="YYYY"
                        />
                    </div>


                    <div class="field">
                        <label>
                            तल्ला संख्या
                        </label>

                        <input
                            v-model="form.no_of_floors"
                            type="number"
                            min="0"
                            placeholder="तल्ला संख्या"
                        />
                    </div>


                    <div class="field">
                        <label>
                            Covered Area
                        </label>

                        <input
                            v-model="form.covered_area"
                            type="text"
                            placeholder="Covered Area"
                        />
                    </div>


                    <div class="field">
                        <label>
                            Structure Type
                        </label>

                        <select v-model="form.structure_type">

                            <option value="">
                                छान्नुहोस्
                            </option>

                            <option value="RCC">
                                RCC
                            </option>

                            <option value="Load Bearing">
                                Load Bearing
                            </option>

                            <option value="Steel">
                                Steel
                            </option>

                            <option value="Other">
                                Other
                            </option>

                        </select>
                    </div>


                    <div class="field">
                        <label>
                            छानाको प्रकार
                        </label>

                        <input
                            v-model="form.roof_type"
                            type="text"
                            placeholder="Roof Type"
                        />
                    </div>


                    <div class="field">
                        <label>
                            पार्किङ
                        </label>

                        <input
                            v-model="form.parking"
                            type="text"
                            placeholder="Parking"
                        />
                    </div>


                    <div class="field">
                        <label>
                            खानेपानी
                        </label>

                        <input
                            v-model="form.water_supply"
                            type="text"
                            placeholder="Water Supply"
                        />
                    </div>


                    <div class="field">
                        <label>
                            बिजुली
                        </label>

                        <input
                            v-model="form.electricity"
                            type="text"
                            placeholder="Electricity"
                        />
                    </div>


                    <div class="field">
                        <label>
                            इन्टरनेट
                        </label>

                        <input
                            v-model="form.internet"
                            type="text"
                            placeholder="Internet"
                        />
                    </div>


                    <div class="field">
                        <label>
                            ढल निकास
                        </label>

                        <input
                            v-model="form.drainage"
                            type="text"
                            placeholder="Drainage"
                        />
                    </div>


                    <div class="field">
                        <label>
                            भवन निर्माण अनुमति नं.
                        </label>

                        <input
                            v-model="form.building_permit_no"
                            type="text"
                            placeholder="Building Permit Number"
                        />
                    </div>


                    <div class="field">
                        <label>
                            भवनको वर्तमान अवस्था
                        </label>

                        <select
                            v-model="form.current_building_condition"
                        >

                            <option value="">
                                अवस्था छान्नुहोस्
                            </option>

                            <option value="excellent">
                                उत्कृष्ट
                            </option>

                            <option value="good">
                                राम्रो
                            </option>

                            <option value="fair">
                                सामान्य
                            </option>

                            <option value="poor">
                                कमजोर
                            </option>

                        </select>
                    </div>

                </div>

            </section>


            <!-- =====================================
                 06 FINANCIAL
            ====================================== -->
            <section class="form-section">

                <div class="section-header">

                    <div class="section-number">
                        ०६
                    </div>

                    <div>
                        <h2>आर्थिक विवरण</h2>
                        <p>
                            Price तथा payment सम्बन्धी विवरण
                        </p>
                    </div>

                </div>


                <div class="money-highlight">

                    <div>
                        <span>कुल मूल्य</span>

                        <strong>
                            NPR
                            {{ form.total_price || '0' }}
                        </strong>
                    </div>

                    <div>
                        <span>अग्रिम भुक्तानी</span>

                        <strong>
                            NPR
                            {{ form.advance_payment || '0' }}
                        </strong>
                    </div>

                    <div>
                        <span>बाँकी रकम</span>

                        <strong>
                            NPR
                            {{ form.balance_payment || '0' }}
                        </strong>
                    </div>

                </div>


                <div class="form-grid">

                    <div class="field">

                        <label>
                            कुल मूल्य
                        </label>

                        <div class="input-prefix">
                            <span>NPR</span>

                            <input
                                v-model="form.total_price"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="कुल मूल्य"
                                @input="calculateBalance"
                            />
                        </div>

                        <small>
                            Database: total_price
                        </small>

                    </div>


                    <div class="field">

                        <label>
                            अग्रिम भुक्तानी
                        </label>

                        <div class="input-prefix">
                            <span>NPR</span>

                            <input
                                v-model="form.advance_payment"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="अग्रिम रकम"
                                @input="calculateBalance"
                            />
                        </div>

                        <small>
                            Database: advance_payment
                        </small>

                    </div>


                    <div class="field">

                        <label>
                            बाँकी भुक्तानी
                        </label>

                        <div class="input-prefix">
                            <span>NPR</span>

                            <input
                                v-model="form.balance_payment"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="बाँकी रकम"
                            />
                        </div>

                        <small>
                            Database: balance_payment
                        </small>

                    </div>


                    <div class="field">

                        <label>
                            अन्तिम भुक्तानी मिति
                        </label>

                        <input
                            v-model="form.final_payment_date"
                            type="date"
                        />

                        <small>
                            Database: final_payment_date
                        </small>

                    </div>

                </div>

            </section>


            <!-- =====================================
                 07 COMMISSION
            ====================================== -->
            <section class="form-section">

                <div class="section-header">

                    <div class="section-number">
                        ०७
                    </div>

                    <div>
                        <h2>कमिसन विवरण</h2>
                        <p>
                            Brokerage commission सम्बन्धी विवरण
                        </p>
                    </div>

                </div>


                <div class="form-grid">

                    <div class="field">

                        <label>
                            कमिसन प्रतिशत
                        </label>

                        <div class="input-suffix">

                            <input
                                v-model="form.commission_rate_percent"
                                type="number"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="उदाहरण: 2.5"
                            />

                            <span>%</span>

                        </div>

                        <small>
                            Database: commission_rate_percent
                        </small>

                    </div>


                    <div class="field">

                        <label>
                            निश्चित कमिसन रकम
                        </label>

                        <div class="input-prefix">

                            <span>NPR</span>

                            <input
                                v-model="form.commission_fixed_amount"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="Fixed Commission"
                            />

                        </div>

                        <small>
                            Database: commission_fixed_amount
                        </small>

                    </div>

                </div>

            </section>


            <!-- =====================================
                 08 PERIOD
            ====================================== -->
            <section class="form-section">

                <div class="section-header">

                    <div class="section-number">
                        ०८
                    </div>

                    <div>
                        <h2>सम्झौताको अवधि</h2>
                        <p>
                            Agreement duration तथा termination
                        </p>
                    </div>

                </div>


                <div class="form-grid">

                    <div class="field">

                        <label>
                            सम्झौताको अवधि
                        </label>

                        <div class="input-suffix">

                            <input
                                v-model="form.agreement_period_months"
                                type="number"
                                min="1"
                                placeholder="महिना"
                            />

                            <span>महिना</span>

                        </div>

                        <small>
                            Database: agreement_period_months
                        </small>

                    </div>


                    <div class="field">

                        <label>
                            समाप्ति सूचना अवधि
                        </label>

                        <div class="input-suffix">

                            <input
                                v-model="form.termination_notice_days"
                                type="number"
                                min="0"
                                placeholder="दिन"
                            />

                            <span>दिन</span>

                        </div>

                        <small>
                            Database: termination_notice_days
                        </small>

                    </div>

                </div>

            </section>


            <!-- =====================================
                 09 PARTY / COMPANY
            ====================================== -->
            <section class="form-section">

                <div class="section-header">

                    <div class="section-number">
                        ०९
                    </div>

                    <div>
                        <h2>सम्झौता पक्ष / कम्पनी प्रतिनिधि</h2>
                        <p>
                            Agreement party तथा representative information
                        </p>
                    </div>

                </div>


                <div class="form-grid">

                    <div class="field">

                        <label>
                            Party Role
                        </label>

                        <select v-model="form.party_role">

                            <option value="seller">
                                Seller / विक्रेता
                            </option>

                            <option value="buyer">
                                Buyer / खरिदकर्ता
                            </option>

                            <option value="property_owner">
                                Property Owner / सम्पत्ति धनी
                            </option>

                            <option value="company">
                                Company / कम्पनी
                            </option>

                        </select>

                        <small>
                            Database: party_role
                        </small>

                    </div>


                    <div class="field">

                        <label>
                            Company ID
                        </label>

                        <input
                            type="text"
                            placeholder="Company ID"
                        />

                        <small>
                            Database: company_id
                        </small>

                    </div>


                    <div class="field">

                        <label>
                            प्रतिनिधिको नाम
                        </label>

                        <input
                            v-model="form.representative_name"
                            type="text"
                            placeholder="प्रतिनिधिको नाम"
                        />

                        <small>
                            Database: representative_name
                        </small>

                    </div>


                    <div class="field">

                        <label>
                            पद
                        </label>

                        <input
                            v-model="form.designation"
                            type="text"
                            placeholder="उदाहरण: Manager"
                        />

                        <small>
                            Database: designation
                        </small>

                    </div>

                </div>

            </section>


            <!-- =====================================
                 10 WITNESSES
            ====================================== -->
            <section class="form-section">

                <div class="section-header">

                    <div class="section-number">
                        १०
                    </div>

                    <div>
                        <h2>साक्षीहरूको विवरण</h2>
                        <p>
                            Agreement witnesses information
                        </p>
                    </div>

                </div>


                <div class="witness-card">

                    <div class="witness-heading">
                        <span>१</span>

                        <div>
                            <h3>पहिलो साक्षी</h3>
                            <p>Witness 1</p>
                        </div>
                    </div>


                    <div class="form-grid">

                        <div class="field">

                            <label>
                                पूरा नाम
                            </label>

                            <input
                                v-model="form.witness1_full_name"
                                type="text"
                                placeholder="साक्षीको पूरा नाम"
                            />

                        </div>


                        <div class="field">

                            <label>
                                नागरिकता नं.
                            </label>

                            <input
                                v-model="form.witness1_citizenship_no"
                                type="text"
                                placeholder="नागरिकता नम्बर"
                            />

                        </div>


                        <div class="field full">

                            <label>
                                ठेगाना
                            </label>

                            <textarea
                                v-model="form.witness1_address"
                                rows="2"
                                placeholder="साक्षीको ठेगाना"
                            ></textarea>

                        </div>

                    </div>

                </div>


                <div class="witness-card">

                    <div class="witness-heading">
                        <span>२</span>

                        <div>
                            <h3>दोस्रो साक्षी</h3>
                            <p>Witness 2</p>
                        </div>
                    </div>


                    <div class="form-grid">

                        <div class="field">

                            <label>
                                पूरा नाम
                            </label>

                            <input
                                v-model="form.witness2_full_name"
                                type="text"
                                placeholder="साक्षीको पूरा नाम"
                            />

                        </div>


                        <div class="field">

                            <label>
                                नागरिकता नं.
                            </label>

                            <input
                                v-model="form.witness2_citizenship_no"
                                type="text"
                                placeholder="नागरिकता नम्बर"
                            />

                        </div>


                        <div class="field full">

                            <label>
                                ठेगाना
                            </label>

                            <textarea
                                v-model="form.witness2_address"
                                rows="2"
                                placeholder="साक्षीको ठेगाना"
                            ></textarea>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =====================================
                 11 GOVERNING LAW
            ====================================== -->
            <section class="form-section">

                <div class="section-header">

                    <div class="section-number">
                        ११
                    </div>

                    <div>
                        <h2>लागू हुने कानून</h2>
                        <p>
                            Agreement governing law
                        </p>
                    </div>

                </div>


                <div class="law-box">

                    <div class="law-icon">
                        ⚖
                    </div>

                    <div class="field">

                        <label>
                            लागू हुने कानून
                        </label>

                        <textarea
                            v-model="form.governing_law"
                            rows="3"
                        ></textarea>

                        <small>
                            Database: governing_law
                        </small>

                    </div>

                </div>

            </section>


            <!-- =====================================
                 FOOTER
            ====================================== -->
            <div class="form-footer">

                <div class="footer-message">

                    <div class="footer-check">
                        ✓
                    </div>

                    <div>
                        <strong>
                            फारम जाँच गर्नुहोस्
                        </strong>

                        <p>
                            सबै विवरण सही भएपछि मात्र Save गर्नुहोस्।
                            अहिले यो फारम Database मा save हुँदैन।
                        </p>
                    </div>

                </div>


                <div class="footer-actions">

                    <button
                        type="button"
                        class="btn-secondary"
                        @click="resetForm"
                    >
                        रिसेट गर्नुहोस्
                    </button>

                    <button
                        type="submit"
                        class="btn-primary"
                    >
                        फारम सुरक्षित गर्नुहोस्
                    </button>

                </div>

            </div>

        </form>

    </div>
</template>


<style scoped>
* {
    box-sizing: border-box;
}

.annex-page {
    min-height: 100vh;
    background: #f4f6f8;
    padding: 28px 20px 60px;
    color: #172033;
    font-family:
        "Noto Sans Devanagari",
        "Nirmala UI",
        "Segoe UI",
        sans-serif;
}

.annex-header,
.annex-form {
    width: min(1150px, 100%);
    margin: auto;
}

.annex-header {
    background: #ffffff;
    border: 1px solid #e4e8ee;
    border-radius: 16px;
    padding: 26px 30px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.05);
}

.breadcrumb {
    color: #8993a4;
    font-size: 12px;
    margin-bottom: 13px;
}

.breadcrumb span {
    margin: 0 7px;
    color: #c8ced7;
}

.title-row {
    display: flex;
    align-items: center;
    gap: 14px;
}

.document-icon {
    width: 48px;
    height: 48px;
    display: grid;
    place-items: center;
    background: #111827;
    color: #ffffff;
    border-radius: 12px;
    font-size: 21px;
}

.annex-header h1 {
    margin: 0;
    font-size: 25px;
    letter-spacing: 0.5px;
}

.annex-header p {
    margin: 4px 0 0;
    color: #707a8b;
    font-size: 13px;
}

.header-status {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    border: 1px solid #e5e9ef;
    border-radius: 999px;
    padding: 8px 13px;
    color: #64748b;
    font-size: 12px;
}

.status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #16a34a;
}

.form-section {
    background: #ffffff;
    border: 1px solid #e4e8ee;
    border-radius: 16px;
    margin-bottom: 18px;
    padding: 28px 30px;
    box-shadow: 0 3px 15px rgba(15, 23, 42, 0.035);
}

.section-header {
    display: flex;
    align-items: flex-start;
    gap: 13px;
    padding-bottom: 19px;
    margin-bottom: 22px;
    border-bottom: 1px solid #edf0f3;
}

.section-number {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    display: grid;
    place-items: center;
    border-radius: 10px;
    background: #111827;
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
}

.section-header h2 {
    margin: 0;
    font-size: 17px;
    font-weight: 700;
}

.section-header p {
    margin: 3px 0 0;
    color: #7b8494;
    font-size: 12px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 19px;
}

.field {
    min-width: 0;
}

.field.full {
    grid-column: 1 / -1;
}

.field label {
    display: block;
    color: #344054;
    font-size: 13px;
    font-weight: 650;
    margin-bottom: 7px;
}

.field label b {
    color: #dc2626;
}

.field input,
.field select,
.field textarea {
    display: block;
    width: 100%;
    border: 1px solid #d6dbe3;
    border-radius: 9px;
    background: #ffffff;
    color: #172033;
    padding: 11px 13px;
    outline: none;
    font-family: inherit;
    font-size: 13px;
    transition: 0.15s ease;
}

.field input,
.field select {
    height: 43px;
}

.field textarea {
    resize: vertical;
    min-height: 90px;
}

.field input::placeholder,
.field textarea::placeholder {
    color: #a3abb8;
}

.field input:focus,
.field select:focus,
.field textarea:focus {
    border-color: #475569;
    box-shadow: 0 0 0 3px rgba(71, 85, 105, 0.08);
}

.field small {
    display: block;
    margin-top: 5px;
    color: #a0a8b5;
    font-size: 10px;
}

.info-banner {
    display: flex;
    gap: 11px;
    padding: 13px 15px;
    margin-bottom: 20px;
    border: 1px solid #dbe5f0;
    background: #f7fafc;
    border-radius: 10px;
}

.info-banner > span {
    font-size: 17px;
}

.info-banner strong {
    font-size: 12px;
}

.info-banner p {
    margin: 3px 0 0;
    color: #718096;
    font-size: 11px;
}

.money-highlight {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 22px;
}

.money-highlight > div {
    padding: 17px;
    border: 1px solid #e5e9ef;
    border-radius: 11px;
    background: #fafbfc;
}

.money-highlight span {
    display: block;
    color: #7b8494;
    font-size: 11px;
    margin-bottom: 6px;
}

.money-highlight strong {
    font-size: 17px;
    color: #172033;
}

.input-prefix,
.input-suffix {
    position: relative;
}

.input-prefix input {
    padding-left: 58px;
}

.input-prefix > span {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #667085;
    font-size: 11px;
    font-weight: 700;
    pointer-events: none;
}

.input-suffix input {
    padding-right: 60px;
}

.input-suffix > span {
    position: absolute;
    right: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #667085;
    font-size: 11px;
    font-weight: 700;
    pointer-events: none;
}

.witness-card {
    border: 1px solid #e4e8ee;
    border-radius: 12px;
    background: #fafbfc;
    padding: 20px;
    margin-bottom: 14px;
}

.witness-card:last-child {
    margin-bottom: 0;
}

.witness-heading {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
}

.witness-heading > span {
    width: 30px;
    height: 30px;
    display: grid;
    place-items: center;
    border-radius: 8px;
    background: #e8edf3;
    font-size: 12px;
    font-weight: 700;
}

.witness-heading h3 {
    margin: 0;
    font-size: 14px;
}

.witness-heading p {
    margin: 2px 0 0;
    color: #8a93a2;
    font-size: 10px;
}

.law-box {
    display: flex;
    gap: 15px;
    padding: 18px;
    border: 1px solid #e6e9ee;
    border-radius: 11px;
    background: #fafbfc;
}

.law-icon {
    width: 40px;
    height: 40px;
    flex: 0 0 40px;
    display: grid;
    place-items: center;
    border-radius: 9px;
    background: #eef1f5;
    font-size: 19px;
}

.law-box .field {
    flex: 1;
}

.form-footer {
    background: #ffffff;
    border: 1px solid #e1e6ec;
    border-radius: 16px;
    padding: 20px 23px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    box-shadow: 0 5px 20px rgba(15, 23, 42, 0.06);
}

.footer-message {
    display: flex;
    align-items: center;
    gap: 11px;
}

.footer-check {
    width: 35px;
    height: 35px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: #ecfdf3;
    color: #16a34a;
    font-weight: 700;
}

.footer-message strong {
    display: block;
    font-size: 13px;
}

.footer-message p {
    margin: 3px 0 0;
    color: #7b8494;
    font-size: 11px;
}

.footer-actions {
    display: flex;
    gap: 9px;
}

.btn-secondary,
.btn-primary {
    border-radius: 9px;
    padding: 11px 17px;
    font-family: inherit;
    font-size: 12px;
    font-weight: 650;
    cursor: pointer;
    transition: 0.15s ease;
}

.btn-secondary {
    border: 1px solid #d6dbe3;
    background: #ffffff;
    color: #344054;
}

.btn-secondary:hover {
    background: #f7f8fa;
}

.btn-primary {
    border: 1px solid #111827;
    background: #111827;
    color: #ffffff;
}

.btn-primary:hover {
    background: #293241;
}

@media (max-width: 800px) {

    .annex-page {
        padding: 15px 10px 40px;
    }

    .annex-header {
        padding: 20px;
        align-items: flex-start;
        flex-direction: column;
    }

    .form-section {
        padding: 21px 17px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .field.full {
        grid-column: auto;
    }

    .money-highlight {
        grid-template-columns: 1fr;
    }

    .form-footer {
        align-items: stretch;
        flex-direction: column;
    }

    .footer-actions {
        width: 100%;
    }

    .btn-secondary,
    .btn-primary {
        flex: 1;
    }
}

@media (max-width: 480px) {

    .annex-header h1 {
        font-size: 21px;
    }

    .title-row {
        align-items: flex-start;
    }

    .document-icon {
        width: 42px;
        height: 42px;
    }

    .section-header {
        gap: 9px;
    }

    .section-number {
        width: 34px;
        height: 34px;
        flex-basis: 34px;
    }

    .law-box {
        flex-direction: column;
    }
}
</style>