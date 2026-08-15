<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const services = [
    { title: 'Property Valuation', desc: 'Market, mortgage, insurance, forced sale, investment, and government valuation by certified valuators.', icon: '📊' },
    { title: 'Engineering Consultancy', desc: 'Structural design, feasibility studies, and expert engineering advice for your project.', icon: '🏗️' },
    { title: 'Land Survey', desc: 'GPS-based land measurement, boundary demarcation, and topographic surveys.', icon: '📐' },
    { title: 'Construction Supervision', desc: 'On-site supervision ensuring quality, safety, and timeline compliance.', icon: '🔧' },
    { title: 'Real Estate Services', desc: 'Buy, sell, rent, or lease — we connect you to the right property at the right price.', icon: '🏘️' },
    { title: 'GIS Mapping', desc: 'Geographic Information System mapping for land analysis and spatial planning.', icon: '🗺️' },
];

const form = useForm({
    name: '',
    email: '',
    phone: '',
    service: '',
    message: '',
});

const inquirySubmitted = ref(false);

function submitInquiry() {
    form.post('/inquiry', {
        onSuccess: () => {
            inquirySubmitted.value = true;
            form.reset();
        },
    });
}
</script>

<template>
    <Head title="Apighar Jagga — Property & Engineering Consultancy">
        <meta name="description" content="Apighar Jagga provides professional property valuation, engineering consultancy, land survey, construction supervision, real estate services and GIS mapping in Nepal." />
    </Head>

    <!-- Nav -->
    <nav class="sticky top-0 z-50 border-b bg-white shadow-sm">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-700 text-sm font-bold text-white">AJ</div>
                <span class="font-bold text-gray-800">Apighar Jagga</span>
            </div>
            <div class="hidden items-center gap-6 text-sm text-gray-600 md:flex">
                <a href="#services" class="hover:text-slate-700">Services</a>
                <Link href="/about" class="hover:text-slate-700">About</Link>
                <a href="#contact" class="hover:text-slate-700">Contact</a>
                <Link href="/login" class="rounded-md bg-slate-700 px-4 py-1.5 text-white hover:bg-slate-800">Login</Link>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="bg-gradient-to-br from-slate-700 to-slate-900 py-24 text-white">
        <div class="mx-auto max-w-4xl px-4 text-center">
            <h1 class="mb-4 text-4xl font-extrabold leading-tight md:text-5xl">
                Professional Property &amp;<br />Engineering Services in Nepal
            </h1>
            <p class="mb-8 text-lg text-slate-300">
                Trusted valuation, survey, and consultancy services since our founding. Certified professionals. Transparent processes.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#contact" class="rounded-lg bg-white px-6 py-3 font-semibold text-slate-700 hover:bg-gray-100">
                    Request a Service
                </a>
                <a href="#services" class="rounded-lg border border-white px-6 py-3 font-semibold text-white hover:bg-white/10">
                    Our Services
                </a>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section id="services" class="py-20">
        <div class="mx-auto max-w-6xl px-4">
            <h2 class="mb-2 text-center text-3xl font-bold text-gray-800">Our Services</h2>
            <p class="mb-12 text-center text-gray-500">Comprehensive property and engineering solutions</p>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="s in services"
                    :key="s.title"
                    class="rounded-xl border bg-white p-6 shadow-sm transition hover:shadow-md"
                >
                    <div class="mb-3 text-3xl">{{ s.icon }}</div>
                    <h3 class="mb-2 font-semibold text-gray-800">{{ s.title }}</h3>
                    <p class="text-sm text-gray-500">{{ s.desc }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why us -->
    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-5xl px-4">
            <h2 class="mb-12 text-center text-3xl font-bold text-gray-800">Why Choose Us?</h2>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div class="text-center">
                    <div class="mb-3 text-4xl">🏆</div>
                    <h3 class="mb-2 font-semibold">Licensed &amp; Certified</h3>
                    <p class="text-sm text-gray-500">Government-registered broker and land survey licence holders.</p>
                </div>
                <div class="text-center">
                    <div class="mb-3 text-4xl">⚡</div>
                    <h3 class="mb-2 font-semibold">Fast Turnaround</h3>
                    <p class="text-sm text-gray-500">Quick response times with professional service delivery.</p>
                </div>
                <div class="text-center">
                    <div class="mb-3 text-4xl">🔒</div>
                    <h3 class="mb-2 font-semibold">Transparent Process</h3>
                    <p class="text-sm text-gray-500">Clear reports, documented processes and fair pricing.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact / Inquiry -->
    <section id="contact" class="py-20">
        <div class="mx-auto max-w-2xl px-4">
            <h2 class="mb-2 text-center text-3xl font-bold text-gray-800">Get in Touch</h2>
            <p class="mb-8 text-center text-gray-500">Fill in the form below and we'll contact you shortly</p>

            <div v-if="inquirySubmitted" class="rounded-xl bg-green-50 p-6 text-center text-green-700">
                <p class="text-lg font-semibold">✅ Inquiry submitted!</p>
                <p class="mt-1 text-sm">We will contact you within 24 hours.</p>
                <button class="mt-4 rounded-md bg-green-600 px-4 py-2 text-sm text-white hover:bg-green-700" @click="inquirySubmitted = false">Send another</button>
            </div>

            <form v-else @submit.prevent="submitInquiry" class="space-y-4 rounded-xl bg-white p-8 shadow">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Full Name *</label>
                        <input v-model="form.name" type="text" class="w-full rounded-md border px-3 py-2 text-sm" :class="{ 'border-red-400': form.errors.name }" />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Phone *</label>
                        <input v-model="form.phone" type="text" class="w-full rounded-md border px-3 py-2 text-sm" :class="{ 'border-red-400': form.errors.phone }" />
                        <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                    <input v-model="form.email" type="email" class="w-full rounded-md border px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Service Needed</label>
                    <select v-model="form.service" class="w-full rounded-md border px-3 py-2 text-sm">
                        <option value="">Select service…</option>
                        <option value="Property Valuation">Property Valuation</option>
                        <option value="Engineering Consultancy">Engineering Consultancy</option>
                        <option value="Land Survey">Land Survey</option>
                        <option value="Construction Supervision">Construction Supervision</option>
                        <option value="Real Estate Services">Real Estate Services</option>
                        <option value="GIS Mapping">GIS Mapping</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Message *</label>
                    <textarea v-model="form.message" rows="4" class="w-full rounded-md border px-3 py-2 text-sm" :class="{ 'border-red-400': form.errors.message }" />
                    <p v-if="form.errors.message" class="mt-1 text-xs text-red-600">{{ form.errors.message }}</p>
                </div>
                <button type="submit" :disabled="form.processing" class="w-full rounded-md bg-slate-700 py-3 font-semibold text-white hover:bg-slate-800 disabled:opacity-60">
                    Send Inquiry
                </button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-800 py-10 text-slate-300">
        <div class="mx-auto max-w-6xl px-4">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div>
                    <div class="mb-3 flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-xs font-bold text-slate-800">AJ</div>
                        <span class="font-bold text-white">Apighar Jagga</span>
                    </div>
                    <p class="text-sm text-slate-400">Professional property and engineering consultancy services in Nepal.</p>
                </div>
                <div>
                    <h4 class="mb-3 font-semibold text-white">Services</h4>
                    <ul class="space-y-1 text-sm">
                        <li v-for="s in services" :key="s.title"><a href="#services" class="hover:text-white">{{ s.title }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-3 font-semibold text-white">Contact</h4>
                    <p class="text-sm">📞 +977-XXX-XXXXXXX</p>
                    <p class="text-sm">✉️ info@apigharjagga.com.np</p>
                    <p class="text-sm">📍 Kathmandu, Nepal</p>
                </div>
            </div>
            <div class="mt-8 border-t border-slate-700 pt-6 text-center text-xs text-slate-500">
                &copy; {{ new Date().getFullYear() }} Apighar Jagga. All rights reserved.
            </div>
        </div>
    </footer>
</template>
