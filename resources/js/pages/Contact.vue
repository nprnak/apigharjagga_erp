<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppFooter from '../components/home/AppFooter.vue';
import AppHeader from '../components/home/AppHeader.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
});

function submit() {
    form.post('/contact', {
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <Head title="Contact Us — Api Ghar Jagga">
        <meta
            head-key="description"
            name="description"
            content="Get in touch with Api Ghar Jagga for property valuation, engineering consultancy, or real estate services."
        />
    </Head>

    <div class="min-h-screen bg-white text-slate-900">
        <AppHeader solid />

        <section class="bg-[#1a365d] pt-28 pb-16 text-white">
            <div class="mx-auto max-w-5xl px-4 text-center sm:px-6">
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    Contact Us
                </h1>
                <p
                    class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-white/80"
                >
                    Have a question about a property, valuation, or our
                    services? Send us a message.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
                <!-- Form -->
                <div>
                    <div
                        v-if="form.recentlySuccessful"
                        class="mb-5 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-700"
                    >
                        Thank you for reaching out. We will get back to you
                        shortly.
                    </div>

                    <form class="space-y-4" @submit.prevent="submit">
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-slate-700"
                                >Full name</label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-slate-700"
                                    >Email</label
                                >
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                />
                                <p
                                    v-if="form.errors.email"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-sm font-medium text-slate-700"
                                    >Phone (optional)</label
                                >
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                />
                            </div>
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-slate-700"
                                >Subject</label
                            >
                            <input
                                v-model="form.subject"
                                type="text"
                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-slate-700"
                                >Message</label
                            >
                            <textarea
                                v-model="form.message"
                                rows="5"
                                required
                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                            ></textarea>
                            <p
                                v-if="form.errors.message"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ form.errors.message }}
                            </p>
                        </div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full rounded-lg bg-brand-600 py-2.5 text-sm font-bold text-white hover:bg-brand-700 disabled:opacity-60 sm:w-auto sm:px-8"
                        >
                            {{ form.processing ? 'Sending…' : 'Send Message' }}
                        </button>
                    </form>
                </div>

                <!-- Map + info -->
                <div>
                    <div
                        class="overflow-hidden rounded-2xl border border-slate-200"
                    >
                        <iframe
                            title="Api Ghar Jagga location"
                            class="h-72 w-full sm:h-full"
                            style="border: 0"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            src="https://www.google.com/maps?q=Api+Ghar+Jagga,+Kathmandu,+Nepal&output=embed"
                        />
                    </div>
                    <p class="mt-3 text-xs text-slate-400">
                        Map shows an approximate search result — update with the
                        exact office address.
                    </p>
                </div>
            </div>
        </section>

        <AppFooter />
    </div>
</template>
