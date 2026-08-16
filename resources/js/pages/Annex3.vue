<template>
    <div class="min-h-screen bg-slate-50 text-slate-800">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">

            <!-- Header -->
            <header class="mb-10 text-center">
                <div
                    class="mb-4 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-1.5 text-xs font-bold tracking-wider text-slate-500 shadow-sm"
                >
                    AGJ-FRM-03
                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                    Annex 3
                </div>

                <h1 class="text-4xl font-black tracking-tight text-slate-900">
                    Property Listing & Brokerage Agreement
                </h1>

                <p class="mt-2 text-lg font-medium text-slate-500">
                    सम्पत्ति सूचीकरण तथा दलाली सेवा सम्झौता
                </p>
            </header>

            <!-- Progress -->
            <div class="relative mx-auto mb-10 max-w-4xl">
                <div
                    class="absolute left-0 top-1/2 h-1 w-full -translate-y-1/2 rounded-full bg-slate-200"
                ></div>

                <div
                    class="absolute left-0 top-1/2 h-1 -translate-y-1/2 rounded-full bg-emerald-500 transition-all"
                    :style="{ width: progress + '%' }"
                ></div>

                <div class="relative flex justify-between">
                    <button
                        v-for="(s, i) in steps"
                        :key="s"
                        type="button"
                        @click="go(i)"
                        :disabled="i > maxStep"
                        class="flex h-10 w-10 items-center justify-center rounded-full border-4 text-sm font-bold"
                        :class="
                            i <= step
                                ? 'border-emerald-100 bg-emerald-500 text-white'
                                : 'border-slate-200 bg-white text-slate-400'
                        "
                    >
                        {{ i + 1 }}
                    </button>
                </div>
            </div>

            <!-- Server Error -->
            <div
                v-if="serverError"
                class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700"
            >
                {{ serverError }}
            </div>

            <form @submit.prevent="nextOrSubmit">

                <!-- STEP 1 -->
                <section v-if="step === 0" class="card">
                    <SectionHead
                        n="01"
                        title="Agreement & Party Details"
                        sub="सम्झौता तथा पक्षहरूको विवरण"
                    />

                    <div class="grid gap-6 md:grid-cols-2">

                        <Field
                            label="Agreement Type / सम्झौताको प्रकार"
                            required
                        >
                            <select
                                v-model="form.agreement_type"
                                class="input"
                            >
                                <option value="">छान्नुहोस्</option>
                                <option value="listing_brokerage">
                                    Listing / Brokerage
                                </option>
                                <option value="sale_purchase">
                                    Sale / Purchase
                                </option>
                            </select>
                        </Field>

                        <Field
                            label="Agreement Date / सम्झौताको मिति"
                            required
                        >
                            <input
                                v-model="form.agreement_date"
                                type="date"
                                class="input"
                            />
                        </Field>

                        <Field label="Place / सम्झौता भएको स्थान">
                            <input
                                v-model="form.place"
                                class="input"
                                placeholder="काठमाडौं"
                            />
                        </Field>

                        <Field
                            label="Property Owner Name / सम्पत्ति धनीको नाम"
                            required
                        >
                            <input
                                v-model="form.full_name"
                                class="input"
                                placeholder="पूरा नाम"
                            />
                        </Field>

                        <Field
                            label="Father / Mother Name / बाबु / आमाको नाम"
                        >
                            <input
                                v-model="form.father_mother_name"
                                class="input"
                            />
                        </Field>

                        <Field
                            label="Citizenship No. / नागरिकता नं."
                            required
                        >
                            <input
                                v-model="form.citizenship_no"
                                class="input"
                            />
                        </Field>

                        <Field
                            label="Mobile No. / मोबाइल नं."
                            required
                        >
                            <input
                                v-model="form.mobile_no"
                                class="input"
                                placeholder="98XXXXXXXX"
                            />
                        </Field>

                        <Field label="Email / इमेल">
                            <input
                                v-model="form.email"
                                type="email"
                                class="input"
                            />
                        </Field>

                        <Field
                            label="Address / ठेगाना"
                            class="md:col-span-2"
                        >
                            <textarea
                                v-model="form.full_address_text"
                                rows="3"
                                class="input"
                            ></textarea>
                        </Field>

                    </div>
                </section>

                <!-- STEP 2 -->
                <section v-else-if="step === 1" class="card">

                    <SectionHead
                        n="02"
                        title="Property Details"
                        sub="सम्पत्तिको विवरण"
                    />

                    <div class="grid gap-6 md:grid-cols-2">

                        <Field
                            label="Property Type / सम्पत्तिको प्रकार"
                            required
                        >
                            <select
                                v-model="form.property_type"
                                class="input"
                            >
                                <option value="">छान्नुहोस्</option>

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
                                    कार्यालय
                                </option>

                                <option value="industrial_property">
                                    औद्योगिक
                                </option>

                                <option value="agricultural_land">
                                    कृषि जग्गा
                                </option>

                                <option value="other">
                                    अन्य
                                </option>
                            </select>
                        </Field>

                        <Field label="Ownership Role / स्वामित्व भूमिका">
                            <select
                                v-model="form.ownership_role"
                                class="input"
                            >
                                <option value="">छान्नुहोस्</option>

                                <option value="self">
                                    आफैं
                                </option>

                                <option value="family_member">
                                    परिवार सदस्य
                                </option>

                                <option value="authorized_representative">
                                    अधिकृत प्रतिनिधि
                                </option>

                                <option value="company">
                                    कम्पनी
                                </option>
                            </select>
                        </Field>

                        <Field label="Province / प्रदेश" required>
                            <input
                                v-model="form.province"
                                class="input"
                            />
                        </Field>

                        <Field label="District / जिल्ला" required>
                            <input
                                v-model="form.district"
                                class="input"
                            />
                        </Field>

                        <Field label="Municipality / नगरपालिका" required>
                            <input
                                v-model="form.municipality"
                                class="input"
                            />
                        </Field>

                        <Field label="Ward No. / वडा नं." required>
                            <input
                                v-model="form.ward_no"
                                class="input"
                            />
                        </Field>

                        <Field label="Tole / टोल">
                            <input
                                v-model="form.tole_locality"
                                class="input"
                            />
                        </Field>

                        <Field label="Kitta No. / कित्ता नं.">
                            <input
                                v-model="form.kitta_no"
                                class="input"
                            />
                        </Field>

                        <Field label="Area / क्षेत्रफल">
                            <input
                                v-model="form.area"
                                class="input"
                                placeholder="जस्तै 4-2-1 / sqft"
                            />
                        </Field>

                        <Field label="Ownership Certificate / लालपुर्जा नं.">
                            <input
                                v-model="form.ownership_certificate_no"
                                class="input"
                            />
                        </Field>

                        <Field label="Expected Price / अपेक्षित मूल्य">
                            <input
                                v-model.number="form.total_price"
                                type="number"
                                min="0"
                                class="input"
                                @input="balance"
                            />
                        </Field>

                        <Field label="Map Sheet No. / नक्सा सिट नं.">
                            <input
                                v-model="form.map_sheet_no"
                                class="input"
                            />
                        </Field>

                    </div>
                </section>

                <!-- STEP 3 -->
                <section v-else-if="step === 2" class="card">

                    <SectionHead
                        n="03"
                        title="Financial & Agreement Terms"
                        sub="आर्थिक तथा सम्झौता सर्तहरू"
                    />

                    <div class="grid gap-6 md:grid-cols-2">

                        <Field label="Total Price / कुल मूल्य">
                            <input
                                v-model.number="form.total_price"
                                type="number"
                                min="0"
                                class="input"
                                @input="balance"
                            />
                        </Field>

                        <Field label="Advance Payment / अग्रिम रकम">
                            <input
                                v-model.number="form.advance_payment"
                                type="number"
                                min="0"
                                class="input"
                                @input="balance"
                            />
                        </Field>

                        <Field label="Balance Payment / बाँकी रकम">
                            <input
                                v-model="form.balance_payment"
                                readonly
                                class="input bg-slate-50"
                            />
                        </Field>

                        <Field
                            label="Final Payment Date / अन्तिम भुक्तानी मिति"
                        >
                            <input
                                v-model="form.final_payment_date"
                                type="date"
                                class="input"
                            />
                        </Field>

                        <Field label="Commission Rate / कमिशन दर (%)">
                            <input
                                v-model.number="form.commission_rate_percent"
                                type="number"
                                min="0"
                                max="100"
                                step="0.01"
                                class="input"
                            />
                        </Field>

                        <Field label="Fixed Commission / निश्चित कमिशन">
                            <input
                                v-model.number="form.commission_fixed_amount"
                                type="number"
                                min="0"
                                class="input"
                            />
                        </Field>

                        <Field
                            label="Agreement Period / सम्झौता अवधि (महिना)"
                        >
                            <input
                                v-model.number="form.agreement_period_months"
                                type="number"
                                min="1"
                                class="input"
                            />
                        </Field>

                        <Field
                            label="Termination Notice / सूचना अवधि (दिन)"
                        >
                            <input
                                v-model.number="form.termination_notice_days"
                                type="number"
                                min="0"
                                class="input"
                            />
                        </Field>

                        <Field label="Status / अवस्था">
                            <select
                                v-model="form.status"
                                class="input"
                            >
                                <option value="draft">Draft</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="terminated">Terminated</option>
                                <option value="breached">Breached</option>
                            </select>
                        </Field>

                    </div>

                    <!-- Expenses -->
                    <div
                        class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-5"
                    >
                        <h3 class="mb-4 font-bold text-slate-900">
                            Other Expenses / अन्य खर्च
                        </h3>

                        <div class="grid gap-3 sm:grid-cols-2">

                            <label
                                v-for="expense in expenses"
                                :key="expense.value"
                                class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-white p-3"
                            >
                                <input
                                    v-model="form.expenses"
                                    :value="expense.value"
                                    type="checkbox"
                                    class="h-4 w-4 accent-emerald-600"
                                />

                                <span class="text-sm font-medium">
                                    {{ expense.label }}
                                </span>
                            </label>

                        </div>
                    </div>
                </section>

                <!-- STEP 4 -->
                <section v-else class="card">

                    <SectionHead
                        n="04"
                        title="Witnesses & Declaration"
                        sub="साक्षी तथा घोषणा"
                    />

                    <div class="grid gap-6 md:grid-cols-2">

                        <!-- Witness 1 -->
                        <div
                            class="rounded-2xl border border-slate-200 bg-slate-50 p-5"
                        >
                            <h3 class="mb-4 font-bold">
                                Witness 1 / साक्षी १
                            </h3>

                            <input
                                v-model="form.witness1_full_name"
                                class="input mb-3"
                                placeholder="नाम"
                            />

                            <input
                                v-model="form.witness1_citizenship_no"
                                class="input mb-3"
                                placeholder="नागरिकता नं."
                            />

                            <textarea
                                v-model="form.witness1_address"
                                rows="3"
                                class="input"
                                placeholder="ठेगाना"
                            ></textarea>
                        </div>

                        <!-- Witness 2 -->
                        <div
                            class="rounded-2xl border border-slate-200 bg-slate-50 p-5"
                        >
                            <h3 class="mb-4 font-bold">
                                Witness 2 / साक्षी २
                            </h3>

                            <input
                                v-model="form.witness2_full_name"
                                class="input mb-3"
                                placeholder="नाम"
                            />

                            <input
                                v-model="form.witness2_citizenship_no"
                                class="input mb-3"
                                placeholder="नागरिकता नं."
                            />

                            <textarea
                                v-model="form.witness2_address"
                                rows="3"
                                class="input"
                                placeholder="ठेगाना"
                            ></textarea>
                        </div>

                    </div>

                    <!-- Declaration -->
                    <div
                        class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5"
                    >
                        <p class="text-sm leading-7 text-slate-600">
                            म/हामी माथि उल्लेखित विवरणहरू सत्य र सही रहेको
                            घोषणा गर्दछु/गर्दछौं। Api Ghar Jagga लाई
                            सम्झौतामा उल्लेखित सेवाहरू सञ्चालन गर्न
                            अधिकृत गर्दछु/गर्दछौं।
                        </p>

                        <label
                            class="mt-4 flex cursor-pointer items-center gap-3 font-semibold"
                        >
                            <input
                                v-model="form.declaration_agreed"
                                type="checkbox"
                                class="h-5 w-5 accent-emerald-600"
                            />

                            घोषणामा सहमत छु
                        </label>
                    </div>
                </section>

                <!-- Buttons -->
                <div class="mt-6 flex items-center justify-between">

                    <button
                        v-if="step > 0"
                        type="button"
                        @click="step--"
                        class="btn-secondary"
                    >
                        ← Back
                    </button>

                    <span v-else></span>

                    <button
                        type="submit"
                        :disabled="saving"
                        class="btn-primary"
                    >
                        {{
                            saving
                                ? 'Saving...'
                                : step === steps.length - 1
                                    ? 'Save Agreement'
                                    : 'Continue →'
                        }}
                    </button>

                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, reactive, ref, defineComponent, h } from 'vue'
import axios from 'axios'

/* -----------------------------
   Section Header
----------------------------- */

const SectionHead = defineComponent({
    props: {
        n: String,
        title: String,
        sub: String,
    },

    setup(props) {
        return () =>
            h(
                'div',
                {
                    class: 'mb-8 flex items-center gap-4',
                },
                [
                    h(
                        'div',
                        {
                            class: 'flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-sm font-black text-white',
                        },
                        props.n,
                    ),

                    h('div', {}, [
                        h(
                            'h2',
                            {
                                class: 'text-2xl font-bold text-slate-900',
                            },
                            props.title,
                        ),

                        h(
                            'p',
                            {
                                class: 'mt-1 text-sm text-slate-500',
                            },
                            props.sub,
                        ),
                    ]),
                ],
            )
    },
})

/* -----------------------------
   Form Field
----------------------------- */

const Field = defineComponent({
    props: {
        label: String,
        required: Boolean,
    },

    setup(props, context) {
        return () =>
            h(
                'label',
                {
                    class: 'block',
                },
                [
                    h(
                        'span',
                        {
                            class: 'mb-2 block text-sm font-bold text-slate-700',
                        },
                        [
                            props.label,

                            props.required
                                ? h(
                                      'b',
                                      {
                                          class: 'ml-1 text-red-500',
                                      },
                                      '*',
                                  )
                                : null,
                        ],
                    ),

                    ...(context.slots.default?.() || []),
                ],
            )
    },
})

/* -----------------------------
   Steps
----------------------------- */

const steps = [
    'Party',
    'Property',
    'Terms',
    'Review',
]

const step = ref(0)
const maxStep = ref(0)
const saving = ref(false)
const serverError = ref('')

/* -----------------------------
   Form
----------------------------- */

const form = reactive<any>({
    agreement_type: '',
    agreement_date: '',
    place: '',

    full_name: '',
    father_mother_name: '',
    citizenship_no: '',
    mobile_no: '',
    email: '',
    full_address_text: '',

    ownership_role: '',
    property_type: '',
    kitta_no: '',
    area: '',
    map_sheet_no: '',
    ownership_type: 'private',
    ownership_certificate_no: '',

    province: '',
    district: '',
    municipality: '',
    ward_no: '',
    tole_locality: '',

    total_price: null,
    advance_payment: null,
    balance_payment: '',
    final_payment_date: '',

    commission_rate_percent: null,
    commission_fixed_amount: null,

    agreement_period_months: null,
    termination_notice_days: null,

    status: 'active',

    expenses: [],

    witness1_full_name: '',
    witness1_citizenship_no: '',
    witness1_address: '',

    witness2_full_name: '',
    witness2_citizenship_no: '',
    witness2_address: '',

    declaration_agreed: false,
})

/* -----------------------------
   Expenses
----------------------------- */

const expenses = [
    {
        value: 'property_tax',
        label: 'Property Tax / सम्पत्ति कर',
    },
    {
        value: 'land_revenue',
        label: 'Land Revenue / मालपोत',
    },
    {
        value: 'capital_gains_tax',
        label: 'Capital Gains Tax / पूँजीगत लाभकर',
    },
    {
        value: 'document_prep_notarization',
        label: 'Document & Notary / कागजात तथा नोटरी',
    },
    {
        value: 'utility_bill_clearance',
        label: 'Utility Clearance / बाँकी बिल',
    },
    {
        value: 'registration_charge',
        label: 'Registration Charge / दर्ता शुल्क',
    },
    {
        value: 'preliminary_consultation_charge',
        label: 'Consultation / परामर्श शुल्क',
    },
    {
        value: 'field_visit_charge',
        label: 'Field Visit / स्थलगत शुल्क',
    },
    {
        value: 'valuation_charge',
        label: 'Valuation / मूल्याङ्कन शुल्क',
    },
    {
        value: 'digital_marketing_charge',
        label: 'Digital Marketing / डिजिटल मार्केटिङ',
    },
]

/* -----------------------------
   Progress
----------------------------- */

const progress = computed(() => {
    return (step.value / (steps.length - 1)) * 100
})

function go(index: number) {
    if (index <= maxStep.value) {
        step.value = index
    }
}

/* -----------------------------
   Balance Calculation
----------------------------- */

function balance() {
    const total = Number(form.total_price || 0)
    const advance = Number(form.advance_payment || 0)

    form.balance_payment = Math.max(
        0,
        total - advance,
    ).toFixed(2)
}

/* -----------------------------
   Submit
----------------------------- */

async function nextOrSubmit() {
    serverError.value = ''

    if (step.value < steps.length - 1) {
        step.value++
        maxStep.value = Math.max(
            maxStep.value,
            step.value,
        )

        return
    }

    if (!form.declaration_agreed) {
        serverError.value =
            'Please accept the declaration / घोषणामा सहमत हुनुहोस्'

        return
    }

    saving.value = true

    try {
        const response = await axios.post(
            '/annex3',
            form,
            {
                headers: {
                    Accept: 'application/json',
                },
            },
        )

        alert(
            `Agreement saved successfully. ID: ${response.data.agreement_id}`,
        )

        window.location.reload()
    } catch (error: any) {
        serverError.value =
            error.response?.data?.message ||
            Object.values(
                error.response?.data?.errors || {},
            )
                .flat()
                .join(' ') ||
            'Unable to save the agreement.'
    } finally {
        saving.value = false
    }
}
</script>

<style scoped>
.card {
    overflow: hidden;
    border: 1px solid #e2e8f0;
    border-radius: 24px;
    background: #ffffff;
    padding: 32px;
    box-shadow: 0 8px 30px rgba(15, 23, 42, 0.05);
}

.input {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    background: #ffffff;
    padding: 12px 14px;
    font-size: 14px;
    outline: none;
    transition: 0.2s;
}

.input:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
}

.btn-primary {
    border: 0;
    border-radius: 16px;
    background: #10b981;
    color: #ffffff;
    padding: 13px 24px;
    font-weight: 800;
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
}

.btn-primary:hover {
    background: #059669;
}

.btn-primary:disabled {
    opacity: 0.6;
}

.btn-secondary {
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    background: #ffffff;
    padding: 12px 20px;
    font-weight: 700;
    color: #475569;
}

.btn-secondary:hover {
    background: #f8fafc;
}

@media (max-width: 640px) {
    .card {
        padding: 20px;
        border-radius: 18px;
    }
}
</style>