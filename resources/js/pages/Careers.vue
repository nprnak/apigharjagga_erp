<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppFooter from '../components/home/AppFooter.vue';
import AppHeader from '../components/home/AppHeader.vue';

type Job = {
    job_id: number;
    title: string;
    department: string | null;
    location: string | null;
    employment_type: string;
    description: string | null;
    requirements: string | null;
    posted_date: string | null;
    closing_date: string | null;
};

const props = defineProps<{ jobs: Job[] }>();

const applyingTo = ref<Job | null>(null);

const form = useForm({
    applicant_name: '',
    email: '',
    phone: '',
    cover_letter: '',
    resume: null as File | null,
});

function openApply(job: Job) {
    applyingTo.value = job;
    form.reset();
}

function onResumeChange(event: Event) {
    const target = event.target as HTMLInputElement;
    form.resume = target.files?.[0] ?? null;
}

function submitApplication() {
    if (!applyingTo.value) {
        return;
    }

    form.post(`/careers/${applyingTo.value.job_id}/apply`, {
        forceFormData: true,
        onSuccess: () => {
            applyingTo.value = null;
        },
    });
}

function employmentTypeLabel(type: string): string {
    return (
        {
            full_time: 'Full Time',
            part_time: 'Part Time',
            contract: 'Contract',
            internship: 'Internship',
        }[type] ?? type
    );
}
</script>

<template>
    <Head title="Careers — Api Ghar Jagga">
        <meta
            head-key="description"
            name="description"
            content="Join Api Ghar Jagga — current job openings in engineering, valuation, sales, and support."
        />
    </Head>

    <div class="min-h-screen bg-white text-slate-900">
        <AppHeader solid />

        <section class="bg-[#1a365d] pt-28 pb-16 text-white">
            <div class="mx-auto max-w-5xl px-4 text-center sm:px-6">
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    Careers at Api Ghar Jagga
                </h1>
                <p
                    class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-white/80"
                >
                    Build your career with Nepal's growing real estate services
                    company.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-4xl px-4 py-16 sm:px-6">
            <div
                v-if="props.jobs.length === 0"
                class="rounded-2xl border border-slate-200 p-10 text-center text-sm text-slate-500"
            >
                No open positions right now. Check back soon.
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="job in props.jobs"
                    :key="job.job_id"
                    class="rounded-2xl border border-slate-200 p-6 transition hover:border-brand-300 hover:shadow-sm"
                >
                    <div
                        class="flex flex-wrap items-start justify-between gap-4"
                    >
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">
                                {{ job.title }}
                            </h3>
                            <div
                                class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500"
                            >
                                <span v-if="job.department">{{
                                    job.department
                                }}</span>
                                <span v-if="job.location"
                                    >📍 {{ job.location }}</span
                                >
                                <span
                                    class="rounded-full bg-slate-100 px-2 py-0.5 font-semibold text-slate-600"
                                >
                                    {{
                                        employmentTypeLabel(job.employment_type)
                                    }}
                                </span>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="shrink-0 rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700"
                            @click="openApply(job)"
                        >
                            Apply Now
                        </button>
                    </div>
                    <p
                        v-if="job.description"
                        class="mt-4 text-sm leading-relaxed text-slate-600"
                    >
                        {{ job.description }}
                    </p>
                    <div
                        v-if="job.requirements"
                        class="mt-3 text-sm leading-relaxed text-slate-600"
                    >
                        <span class="font-semibold text-slate-700"
                            >Requirements:</span
                        >
                        {{ job.requirements }}
                    </div>
                </div>
            </div>
        </section>

        <AppFooter />

        <!-- Apply modal -->
        <div
            v-if="applyingTo"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 p-4"
            @click.self="applyingTo = null"
        >
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">
                        Apply — {{ applyingTo.title }}
                    </h3>
                    <button
                        type="button"
                        class="text-slate-400 hover:text-slate-600"
                        @click="applyingTo = null"
                    >
                        ✕
                    </button>
                </div>

                <form
                    class="mt-5 space-y-4"
                    @submit.prevent="submitApplication"
                >
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-slate-700"
                            >Full name</label
                        >
                        <input
                            v-model="form.applicant_name"
                            type="text"
                            required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                        />
                        <p
                            v-if="form.errors.applicant_name"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.applicant_name }}
                        </p>
                    </div>
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
                            >Phone</label
                        >
                        <input
                            v-model="form.phone"
                            type="tel"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                        />
                    </div>
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-slate-700"
                            >Resume (PDF/DOC, max 5MB)</label
                        >
                        <input
                            type="file"
                            accept=".pdf,.doc,.docx"
                            required
                            class="w-full text-sm"
                            @change="onResumeChange"
                        />
                        <p
                            v-if="form.errors.resume"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.resume }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-slate-700"
                            >Cover letter (optional)</label
                        >
                        <textarea
                            v-model="form.cover_letter"
                            rows="3"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                        ></textarea>
                    </div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-lg bg-brand-600 py-2.5 text-sm font-bold text-white hover:bg-brand-700 disabled:opacity-60"
                    >
                        {{
                            form.processing
                                ? 'Submitting…'
                                : 'Submit Application'
                        }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
