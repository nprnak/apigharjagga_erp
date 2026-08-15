<template>
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto bg-white shadow border border-gray-200">
            <!-- ============================================================ -->
            <!-- FORM HEADER -->
            <!-- ============================================================ -->
            <div class="border-b-2 border-gray-800 px-8 py-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-mono">Document Code: AGJ-FRM-001</p>
                        <p class="text-xs text-gray-500 font-mono">Version: 1.0</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-600">ANNEX – A</p>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <h1 class="text-xl font-bold text-gray-900 uppercase tracking-wide">
                        Property Listing Application Form
                    </h1>
                    <p class="text-base text-gray-700 mt-1">सम्पत्ति सूचीकरण आवेदन फाराम</p>
                </div>
                <div class="flex justify-between mt-4 text-sm text-gray-600">
                    <div>
                        <span class="font-medium">Effective Date:</span>
                        <span class="border-b border-gray-400 inline-block min-w-[160px] ml-1">{{ form?.data?.effective_date || '' }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Application No.:</span>
                        <span class="border-b border-gray-400 inline-block min-w-[160px] ml-1">{{ form?.data?.application_no || '' }}</span>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- VUEFORM -->
            <!-- ============================================================ -->
            <Vueform
                ref="form$"
                :endpoint="false"
                @submit="handleSubmit"
                size="sm"
                :columns="{ container: 12, label: 12, wrapper: 12 }"
                class="px-8 py-6"
            >
                <!-- ====================================================== -->
                <!-- SECTION 1: APPLICANT DETAILS -->
                <!-- ====================================================== -->
                <StaticElement name="section1_header">
                    <template #default>
                        <div class="border-b-2 border-gray-700 pb-2 mb-4 mt-2">
                            <h2 class="text-base font-bold text-gray-800">
                                1. APPLICANT DETAILS
                                <span class="font-normal text-gray-600 ml-2">१. आवेदकको विवरण</span>
                            </h2>
                        </div>
                    </template>
                </StaticElement>

                <GroupElement name="applicant">
                    <TextElement
                        name="full_name_en"
                        label="Full Name / पूरा नाम"
                        placeholder="Enter full name in English"
                        :columns="6"
                        rules="required"
                    />
                    <TextElement
                        name="full_name_np"
                        label="पूरा नाम (नेपालीमा)"
                        placeholder="नेपालीमा पूरा नाम लेख्नुहोस्"
                        :columns="6"
                    />
                    <TextElement
                        name="citizenship_no"
                        label="Citizenship No. / नागरिकता नं."
                        placeholder="e.g. 12-01-75-12345"
                        :columns="6"
                        rules="required"
                    />
                    <DateElement
                        name="date_of_birth"
                        label="Date of Birth / जन्म मिति"
                        :columns="6"
                    />
                    <TextElement
                        name="father_name"
                        label="Father's Name / बाबुको नाम"
                        placeholder="Enter father's name"
                        :columns="6"
                    />
                    <TextElement
                        name="grandfather_name"
                        label="Grandfather's Name / बाजेको नाम"
                        placeholder="Enter grandfather's name"
                        :columns="6"
                    />
                    <TextareaElement
                        name="permanent_address"
                        label="Permanent Address / स्थायी ठेगाना"
                        placeholder="Enter permanent address"
                        :columns="6"
                        :rows="2"
                    />
                    <TextareaElement
                        name="current_address"
                        label="Current Address / हालको ठेगाना"
                        placeholder="Enter current address"
                        :columns="6"
                        :rows="2"
                    />
                    <TextElement
                        name="mobile_no"
                        label="Mobile No. / मोबाइल नं."
                        placeholder="98XXXXXXXX"
                        :columns="4"
                        rules="required"
                        input-type="tel"
                    />
                    <TextElement
                        name="telephone_no"
                        label="Telephone No. / टेलिफोन नं."
                        placeholder="01-XXXXXXX"
                        :columns="4"
                        input-type="tel"
                    />
                    <TextElement
                        name="email"
                        label="E-mail / इमेल"
                        placeholder="example@email.com"
                        :columns="4"
                        input-type="email"
                    />
                    <TextElement
                        name="occupation"
                        label="Occupation / पेशा"
                        placeholder="Enter occupation"
                        :columns="6"
                    />
                </GroupElement>

                <!-- ====================================================== -->
                <!-- SECTION 2: PROPERTY OWNER DETAILS -->
                <!-- ====================================================== -->
                <StaticElement name="section2_header">
                    <template #default>
                        <div class="border-b-2 border-gray-700 pb-2 mb-4 mt-8">
                            <h2 class="text-base font-bold text-gray-800">
                                2. PROPERTY OWNER DETAILS
                                <span class="font-normal text-gray-600 ml-2">२. सम्पत्ति धनीको विवरण</span>
                            </h2>
                        </div>
                    </template>
                </StaticElement>

                <GroupElement name="owner">
                    <RadiogroupElement
                        name="ownership_role"
                        label="Ownership Role / स्वामित्व भूमिका"
                        :items="[
                            { value: 'self', label: 'Self (स्वयं)' },
                            { value: 'family_member', label: 'Family Member (परिवार सदस्य)' },
                            { value: 'authorized_representative', label: 'Authorized Representative (अधिकृत प्रतिनिधि)' },
                            { value: 'company', label: 'Company/Organization (कम्पनी/संस्था)' },
                        ]"
                        rules="required"
                        :columns="12"
                    />

                    <StaticElement name="poa_note">
                        <template #default>
                            <p class="text-xs text-gray-500 italic mt-1 mb-4">
                                If representative, attach Power of Attorney. /
                                प्रतिनिधि भएमा अधिकारपत्र (Power of Attorney) संलग्न गर्नुहोस्।
                            </p>
                        </template>
                    </StaticElement>
                </GroupElement>

                <!-- ====================================================== -->
                <!-- SECTION 3: PROPERTY DETAILS -->
                <!-- ====================================================== -->
                <StaticElement name="section3_header">
                    <template #default>
                        <div class="border-b-2 border-gray-700 pb-2 mb-4 mt-8">
                            <h2 class="text-base font-bold text-gray-800">
                                3. PROPERTY DETAILS
                                <span class="font-normal text-gray-600 ml-2">३. सम्पत्तिको विवरण</span>
                            </h2>
                        </div>
                    </template>
                </StaticElement>

                <GroupElement name="property">
                    <!-- Property Type -->
                    <RadiogroupElement
                        name="property_type"
                        label="Property Type / सम्पत्तिको प्रकार"
                        :items="[
                            { value: 'land', label: 'Land (जग्गा)' },
                            { value: 'house', label: 'House (घर)' },
                            { value: 'apartment', label: 'Apartment (अपार्टमेन्ट)' },
                            { value: 'commercial_building', label: 'Commercial Building (व्यावसायिक भवन)' },
                            { value: 'office_space', label: 'Office Space (कार्यालय)' },
                            { value: 'industrial_property', label: 'Industrial Property (औद्योगिक)' },
                            { value: 'agricultural_land', label: 'Agricultural Land (कृषि)' },
                            { value: 'other', label: 'Other (अन्य)' },
                        ]"
                        rules="required"
                        :columns="12"
                    />
                    <TextElement
                        name="property_type_other"
                        label="If Other, specify / अन्य भए खुलाउनुहोस्"
                        :columns="6"
                        :conditions="[['property.property_type', 'other']]"
                    />

                    <!-- Address of Property -->
                    <StaticElement name="address_subheader">
                        <template #default>
                            <p class="text-sm font-semibold text-gray-700 mt-4 mb-2 border-b border-gray-300 pb-1">
                                Address of Property / सम्पत्तिको ठेगाना
                            </p>
                        </template>
                    </StaticElement>

                    <TextElement
                        name="province"
                        label="Province / प्रदेश"
                        :columns="4"
                        rules="required"
                    />
                    <TextElement
                        name="district"
                        label="District / जिल्ला"
                        :columns="4"
                        rules="required"
                    />
                    <TextElement
                        name="municipality"
                        label="Municipality/Rural Municipality / पालिका"
                        :columns="4"
                        rules="required"
                    />
                    <TextElement
                        name="ward_no"
                        label="Ward No. / वडा नं."
                        :columns="3"
                        rules="required"
                    />
                    <TextElement
                        name="tole"
                        label="Tole/Locality / टोल"
                        :columns="5"
                    />
                    <TextElement
                        name="gps_location"
                        label="GPS Location (if available)"
                        placeholder="e.g. 27.7172, 85.3240"
                        :columns="4"
                    />

                    <!-- Land Information -->
                    <StaticElement name="land_subheader">
                        <template #default>
                            <p class="text-sm font-semibold text-gray-700 mt-4 mb-2 border-b border-gray-300 pb-1">
                                Land Information / जग्गाको विवरण
                            </p>
                        </template>
                    </StaticElement>

                    <TextElement
                        name="kitta_no"
                        label="Kitta No. / कित्ता नं."
                        :columns="4"
                    />
                    <TextElement
                        name="area"
                        label="Area / क्षेत्रफल"
                        placeholder="e.g. 5 Aana / 200 sqft"
                        :columns="4"
                    />
                    <TextElement
                        name="map_sheet_no"
                        label="Map Sheet No. / नक्सा सिट नं."
                        :columns="4"
                    />
                    <TextElement
                        name="ownership_type"
                        label="Ownership Type / स्वामित्वको प्रकार"
                        placeholder="e.g. Private / Joint"
                        :columns="4"
                    />
                    <TextElement
                        name="road_access"
                        label="Road Access / सडक पहुँच"
                        placeholder="e.g. Yes / No"
                        :columns="4"
                    />
                    <TextElement
                        name="road_width"
                        label="Road Width / सडक चौडाइ"
                        placeholder="e.g. 20 ft"
                        :columns="4"
                    />
                    <TextElement
                        name="facing_direction"
                        label="Facing Direction / मुख दिशा"
                        placeholder="e.g. East / South"
                        :columns="4"
                    />

                    <!-- Building Details -->
                    <StaticElement name="building_subheader">
                        <template #default>
                            <p class="text-sm font-semibold text-gray-700 mt-4 mb-2 border-b border-gray-300 pb-1">
                                Building Details (If Applicable) / भवन सम्बन्धी विवरण
                            </p>
                        </template>
                    </StaticElement>

                    <TextElement
                        name="year_of_construction"
                        label="Year of Construction / निर्माण वर्ष"
                        placeholder="e.g. 2075 BS / 2018 AD"
                        :columns="4"
                    />
                    <TextElement
                        name="no_of_floors"
                        label="No. of Floors / तल्ला संख्या"
                        :columns="4"
                        input-type="number"
                    />
                    <TextElement
                        name="covered_area"
                        label="Covered Area / ढाकिएको क्षेत्रफल"
                        placeholder="e.g. 1200 sqft"
                        :columns="4"
                    />
                    <SelectElement
                        name="structure_type"
                        label="Structure Type / संरचना प्रकार"
                        :native="true"
                        :items="[
                            { value: '', label: '-- Select / छान्नुहोस् --' },
                            { value: 'RCC', label: 'RCC' },
                            { value: 'Load Bearing', label: 'Load Bearing' },
                            { value: 'Steel', label: 'Steel' },
                            { value: 'Other', label: 'Other' },
                        ]"
                        :columns="4"
                    />
                    <TextElement
                        name="roof_type"
                        label="Roof Type / छानाको प्रकार"
                        placeholder="e.g. Concrete / Tile"
                        :columns="4"
                    />
                    <TextElement
                        name="parking"
                        label="Parking / पार्किङ"
                        placeholder="e.g. Available / Not Available"
                        :columns="4"
                    />
                    <TextElement
                        name="water_supply"
                        label="Water Supply / पानी आपूर्ति"
                        placeholder="e.g. Municipal / Boring"
                        :columns="4"
                    />
                    <TextElement
                        name="electricity"
                        label="Electricity / विद्युत"
                        placeholder="e.g. NEA"
                        :columns="4"
                    />
                    <TextElement
                        name="internet"
                        label="Internet / इन्टरनेट"
                        placeholder="e.g. Available / Not Available"
                        :columns="4"
                    />
                    <TextElement
                        name="drainage"
                        label="Drainage / ढल"
                        placeholder="e.g. Yes / No"
                        :columns="4"
                    />
                </GroupElement>

                <!-- ====================================================== -->
                <!-- SECTION 4: PURPOSE OF LISTING -->
                <!-- ====================================================== -->
                <StaticElement name="section4_header">
                    <template #default>
                        <div class="border-b-2 border-gray-700 pb-2 mb-4 mt-8">
                            <h2 class="text-base font-bold text-gray-800">
                                4. PURPOSE OF LISTING
                                <span class="font-normal text-gray-600 ml-2">४. सूचीकरणको उद्देश्य</span>
                            </h2>
                        </div>
                    </template>
                </StaticElement>

                <GroupElement name="listing">
                    <RadiogroupElement
                        name="purpose_of_listing"
                        label="Purpose / उद्देश्य"
                        :items="[
                            { value: 'sale', label: 'Sale (बिक्री)' },
                            { value: 'rent', label: 'Rent (भाडा)' },
                            { value: 'lease', label: 'Lease (लिज)' },
                            { value: 'exchange', label: 'Exchange (साटासाट)' },
                            { value: 'investment', label: 'Investment' },
                            { value: 'other', label: 'Other (अन्य)' },
                        ]"
                        rules="required"
                        :columns="12"
                    />
                    <TextElement
                        name="purpose_other"
                        label="If Other, specify / अन्य भए खुलाउनुहोस्"
                        :columns="6"
                        :conditions="[['listing.purpose_of_listing', 'other']]"
                    />
                </GroupElement>

                <!-- ====================================================== -->
                <!-- SECTION 5: EXPECTED PRICE -->
                <!-- ====================================================== -->
                <StaticElement name="section5_header">
                    <template #default>
                        <div class="border-b-2 border-gray-700 pb-2 mb-4 mt-8">
                            <h2 class="text-base font-bold text-gray-800">
                                5. EXPECTED PRICE
                                <span class="font-normal text-gray-600 ml-2">५. अपेक्षित मूल्य</span>
                            </h2>
                        </div>
                    </template>
                </StaticElement>

                <GroupElement name="pricing">
                    <TextElement
                        name="expected_selling_price"
                        label="Expected Selling Price / अपेक्षित बिक्री मूल्य"
                        placeholder="e.g. Rs. 50,00,000"
                        :columns="6"
                        input-type="text"
                    />
                    <RadiogroupElement
                        name="negotiable"
                        label="Negotiable / मोलमोलाइ"
                        :items="[
                            { value: 'yes', label: 'Yes (छ)' },
                            { value: 'no', label: 'No (छैन)' },
                        ]"
                        :columns="6"
                    />
                    <TextElement
                        name="minimum_acceptable_price"
                        label="Minimum Acceptable Price / न्यूनतम स्वीकार्य मूल्य"
                        placeholder="e.g. Rs. 45,00,000"
                        :columns="6"
                        input-type="text"
                    />
                    <TextElement
                        name="rental_amount"
                        label="Rental Amount (If applicable) / भाडा रकम"
                        placeholder="e.g. Rs. 25,000 / month"
                        :columns="6"
                        input-type="text"
                    />
                </GroupElement>

                <!-- ====================================================== -->
                <!-- SECTION 6: PROPERTY DOCUMENTS SUBMITTED -->
                <!-- ====================================================== -->
                <StaticElement name="section6_header">
                    <template #default>
                        <div class="border-b-2 border-gray-700 pb-2 mb-4 mt-8">
                            <h2 class="text-base font-bold text-gray-800">
                                6. PROPERTY DOCUMENTS SUBMITTED
                                <span class="font-normal text-gray-600 ml-2">६. पेश गरिएका कागजातहरू</span>
                            </h2>
                        </div>
                    </template>
                </StaticElement>

                <GroupElement name="documents">
                    <CheckboxgroupElement
                        name="submitted_documents"
                        label="Select submitted documents / पेश गरिएका कागजातहरू छान्नुहोस्"
                        :items="[
                            { value: 'citizenship_copy', label: 'Citizenship Copy (नागरिकताको प्रतिलिपि)' },
                            { value: 'land_ownership_certificate', label: 'Land Ownership Certificate / लालपुर्जा' },
                            { value: 'tax_clearance', label: 'Tax Clearance (कर चुक्ता)' },
                            { value: 'blueprint', label: 'Blueprint (नक्सा)' },
                            { value: 'building_completion_certificate', label: 'Building Completion Certificate' },
                            { value: 'valuation_report', label: 'Valuation Report (मूल्यांकन प्रतिवेदन)' },
                            { value: 'power_of_attorney', label: 'Power of Attorney (अधिकारपत्र)' },
                            { value: 'utility_bills', label: 'Utility Bills' },
                            { value: 'photographs', label: 'Photographs (फोटोहरू)' },
                        ]"
                        :columns="12"
                    />
                    <TextElement
                        name="other_documents"
                        label="Other Documents / अन्य कागजातहरू"
                        placeholder="Specify other documents"
                        :columns="12"
                    />
                </GroupElement>

                <!-- ====================================================== -->
                <!-- SECTION 7: PROPERTY FEATURES -->
                <!-- ====================================================== -->
                <StaticElement name="section7_header">
                    <template #default>
                        <div class="border-b-2 border-gray-700 pb-2 mb-4 mt-8">
                            <h2 class="text-base font-bold text-gray-800">
                                7. PROPERTY FEATURES
                                <span class="font-normal text-gray-600 ml-2">७. सम्पत्तिका विशेषताहरू</span>
                            </h2>
                        </div>
                    </template>
                </StaticElement>

                <GroupElement name="features">
                    <CheckboxgroupElement
                        name="property_features"
                        label="Select applicable features / लागू हुने विशेषताहरू छान्नुहोस्"
                        :items="[
                            { value: 'corner_plot', label: 'Corner Plot (कुना प्लट)' },
                            { value: 'blacktopped_road', label: 'Blacktopped Road (कालोपत्रे सडक)' },
                            { value: 'drinking_water', label: 'Drinking Water (खानेपानी)' },
                            { value: 'electricity', label: 'Electricity (विद्युत)' },
                            { value: 'sewer', label: 'Sewer (ढल)' },
                            { value: 'internet', label: 'Internet (इन्टरनेट)' },
                            { value: 'school_nearby', label: 'School Nearby (विद्यालय नजिक)' },
                            { value: 'hospital_nearby', label: 'Hospital Nearby (अस्पताल नजिक)' },
                            { value: 'market_nearby', label: 'Market Nearby (बजार नजिक)' },
                            { value: 'public_transport', label: 'Public Transport (सार्वजनिक यातायात)' },
                            { value: 'bank_nearby', label: 'Bank Nearby (बैंक नजिक)' },
                            { value: 'temple', label: 'Temple (मन्दिर)' },
                            { value: 'park', label: 'Park (पार्क)' },
                        ]"
                        :columns="12"
                    />
                    <TextElement
                        name="other_features"
                        label="Other Features / अन्य विशेषताहरू"
                        placeholder="Specify other features"
                        :columns="12"
                    />
                </GroupElement>

                <!-- ====================================================== -->
                <!-- SECTION 8: OWNER'S DECLARATION -->
                <!-- ====================================================== -->
                <StaticElement name="section8_header">
                    <template #default>
                        <div class="border-b-2 border-gray-700 pb-2 mb-4 mt-8">
                            <h2 class="text-base font-bold text-gray-800">
                                8. OWNER'S DECLARATION
                                <span class="font-normal text-gray-600 ml-2">८. सम्पत्ति धनीको घोषणा</span>
                            </h2>
                        </div>
                    </template>
                </StaticElement>

                <StaticElement name="declaration_text">
                    <template #default>
                        <div class="bg-gray-50 border border-gray-200 p-4 mb-4 text-sm leading-relaxed">
                            <p class="text-gray-800 mb-3">
                                I hereby declare that the information provided in this application is true and correct to the
                                best of my knowledge. I confirm that I am the lawful owner or authorized representative of
                                the property and authorize Api Ghar Jagga to inspect, market, advertise, and facilitate the
                                sale, rental, lease, or transfer of the property in accordance with the agreed terms and
                                applicable laws.
                            </p>
                            <p class="text-gray-700">
                                म यस आवेदनमा उल्लेख गरिएका सम्पूर्ण विवरणहरू मेरो जानकारीअनुसार सत्य तथा सही रहेको
                                घोषणा गर्दछु। म उक्त सम्पत्तिको वैधानिक स्वामित्व भएको वा अधिकृत प्रतिनिधि भएको पुष्टि
                                गर्दछु र सहमति भएका सर्तहरू तथा प्रचलित कानून बमोजिम Api Ghar Jagga लाई उक्त
                                सम्पत्तिको निरीक्षण, सूचीकरण, प्रचार–प्रसार तथा बिक्री, भाडा, लिज वा हस्तान्तरण
                                प्रक्रियामा सहजीकरण गर्न अधिकार प्रदान गर्दछु।
                            </p>
                        </div>
                    </template>
                </StaticElement>

                <CheckboxElement
                    name="declaration_agreed"
                    rules="accepted"
                    :columns="12"
                >
                    <template #default>
                        I agree to the above declaration / माथिको घोषणामा सहमत छु
                    </template>
                </CheckboxElement>

                <!-- ====================================================== -->
                <!-- SECTION 9: SIGNATURES -->
                <!-- ====================================================== -->
                <StaticElement name="section9_header">
                    <template #default>
                        <div class="border-b-2 border-gray-700 pb-2 mb-4 mt-8">
                            <h2 class="text-base font-bold text-gray-800">
                                9. SIGNATURES
                                <span class="font-normal text-gray-600 ml-2">९. हस्ताक्षर</span>
                            </h2>
                        </div>
                    </template>
                </StaticElement>

                <GroupElement name="signatures">
                    <!-- Applicant Signature Block -->
                    <StaticElement name="applicant_sig_label">
                        <template #default>
                            <p class="text-sm font-semibold text-gray-700 mb-2">
                                Applicant / Property Owner
                            </p>
                        </template>
                    </StaticElement>

                    <TextElement
                        name="applicant_name"
                        label="Name / नाम"
                        :columns="6"
                        rules="required"
                    />
                    <DateElement
                        name="applicant_date"
                        label="Date / मिति"
                        :columns="6"
                    />

                    <!-- Received By Block -->
                    <StaticElement name="received_sig_label">
                        <template #default>
                            <p class="text-sm font-semibold text-gray-700 mt-4 mb-2">
                                Received By (Api Ghar Jagga)
                            </p>
                        </template>
                    </StaticElement>

                    <TextElement
                        name="received_by_name"
                        label="Name / नाम"
                        :columns="4"
                    />
                    <TextElement
                        name="received_by_designation"
                        label="Designation / पद"
                        :columns="4"
                    />
                    <DateElement
                        name="received_date"
                        label="Date / मिति"
                        :columns="4"
                    />
                </GroupElement>

                <!-- ====================================================== -->
                <!-- SECTION 10: OFFICE USE ONLY -->
                <!-- ====================================================== -->
                <StaticElement name="section10_header">
                    <template #default>
                        <div class="border-t-2 border-gray-800 mt-8 pt-4">
                            <div class="border-b-2 border-gray-700 pb-2 mb-4">
                                <h2 class="text-base font-bold text-gray-800">
                                    10. OFFICE USE ONLY
                                    <span class="font-normal text-gray-600 ml-2">१०. कार्यालय प्रयोजनका लागि मात्र</span>
                                </h2>
                            </div>
                        </div>
                    </template>
                </StaticElement>

                <GroupElement name="office_use">
                    <TextElement
                        name="application_no"
                        label="Application No."
                        :columns="4"
                    />
                    <TextElement
                        name="listing_id"
                        label="Listing ID"
                        :columns="4"
                    />
                    <DateElement
                        name="date_received"
                        label="Date Received"
                        :columns="4"
                    />
                    <TextElement
                        name="assigned_officer"
                        label="Assigned Officer"
                        :columns="6"
                    />
                    <DateElement
                        name="effective_date"
                        label="Effective Date"
                        :columns="6"
                    />

                    <!-- Yes/No toggles -->
                    <RadiogroupElement
                        name="inspection_required"
                        label="Inspection Required"
                        :items="[
                            { value: 'yes', label: 'Yes' },
                            { value: 'no', label: 'No' },
                        ]"
                        :columns="4"
                    />
                    <DateElement
                        name="inspection_date"
                        label="Inspection Date"
                        :columns="4"
                        :conditions="[['office_use.inspection_required', 'yes']]"
                    />
                    <RadiogroupElement
                        name="valuation_required"
                        label="Valuation Required"
                        :items="[
                            { value: 'yes', label: 'Yes' },
                            { value: 'no', label: 'No' },
                        ]"
                        :columns="4"
                    />

                    <RadiogroupElement
                        name="photographs_received"
                        label="Photographs Received"
                        :items="[
                            { value: 'yes', label: 'Yes' },
                            { value: 'no', label: 'No' },
                        ]"
                        :columns="4"
                    />
                    <RadiogroupElement
                        name="gis_location_verified"
                        label="GIS Location Verified"
                        :items="[
                            { value: 'yes', label: 'Yes' },
                            { value: 'no', label: 'No' },
                        ]"
                        :columns="4"
                    />
                    <SelectElement
                        name="legal_verification_status"
                        label="Legal Verification"
                        :native="true"
                        :items="[
                            { value: 'pending', label: 'Pending' },
                            { value: 'completed', label: 'Completed' },
                        ]"
                        :columns="4"
                        default="pending"
                    />

                    <SelectElement
                        name="listing_status"
                        label="Listing Status"
                        :native="true"
                        :items="[
                            { value: 'pending', label: 'Pending' },
                            { value: 'approved', label: 'Approved' },
                            { value: 'rejected', label: 'Rejected' },
                        ]"
                        :columns="4"
                        default="pending"
                    />

                    <TextareaElement
                        name="remarks"
                        label="Remarks / कैफियत"
                        placeholder="Enter remarks"
                        :columns="12"
                        :rows="3"
                    />

                    <!-- Authorized Officer -->
                    <StaticElement name="auth_officer_label">
                        <template #default>
                            <p class="text-sm font-semibold text-gray-700 mt-4 mb-2 border-b border-gray-300 pb-1">
                                Authorized Officer
                            </p>
                        </template>
                    </StaticElement>

                    <TextElement
                        name="auth_officer_name"
                        label="Name"
                        :columns="6"
                    />
                </GroupElement>

                <!-- ====================================================== -->
                <!-- FORM ACTIONS -->
                <!-- ====================================================== -->
                <StaticElement name="form_actions">
                    <template #default>
                        <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-300">
                            <button
                                type="button"
                                class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                @click="handleReset"
                            >
                                Reset / रिसेट
                            </button>
                            <button
                                type="button"
                                class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                @click="handleSaveDraft"
                            >
                                Save Draft / ड्राफ्ट सेव
                            </button>
                            <button
                                type="submit"
                                class="px-6 py-2 text-sm font-medium text-white bg-gray-800 border border-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800"
                            >
                                Submit / पेश गर्नुहोस्
                            </button>
                        </div>
                    </template>
                </StaticElement>
            </Vueform>

            <!-- ============================================================ -->
            <!-- FORM FOOTER -->
            <!-- ============================================================ -->
            <div class="border-t border-gray-300 px-8 py-4 bg-gray-50 text-center">
                <p class="text-xs text-gray-500">
                    © Api Ghar Jagga Pvt. Ltd. | Document Code: AGJ-FRM-001 | Version 1.0
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const form$ = ref<InstanceType<typeof import('@vueform/vueform').Vueform> | null>(null);

/**
 * Handle form submission.
 * Sends the form data to the backend API.
 */
const handleSubmit = async (form: any) => {
    const data = form.data;
    console.log('Submitting Property Listing Form:', data);

    // TODO: Integrate with backend API endpoint
    // Example:
    // try {
    //     const response = await axios.post('/api/property-listings', data);
    //     alert('Application submitted successfully!');
    // } catch (error) {
    //     console.error('Submission error:', error);
    //     alert('Submission failed. Please try again.');
    // }

    alert('Form submitted successfully! / फाराम सफलतापूर्वक पेश गरियो!');
};

/**
 * Reset all form fields to their default values.
 */
const handleReset = () => {
    if (confirm('Are you sure you want to reset the form? / के तपाईं फाराम रिसेट गर्न चाहनुहुन्छ?')) {
        form$.value?.reset();
    }
};

/**
 * Save the current form state as a draft.
 */
const handleSaveDraft = () => {
    const data = form$.value?.data;
    console.log('Saving draft:', data);

    // TODO: Integrate with backend API for draft saving
    // Example:
    // await axios.post('/api/property-listings/draft', data);

    alert('Draft saved! / ड्राफ्ट सेव भयो!');
};
</script>
