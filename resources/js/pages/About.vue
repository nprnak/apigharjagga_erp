<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppFooter from '../components/home/AppFooter.vue';
import AppHeader from '../components/home/AppHeader.vue';

type TeamMember = {
    member_id: number;
    name: string;
    designation: string | null;
    department: string | null;
    photo_url: string | null;
    bio: string | null;
};

type Testimonial = {
    testimonial_id: number;
    client_name: string;
    client_role: string | null;
    client_photo_url: string | null;
    rating: number;
    message: string;
    is_featured: boolean;
};

defineProps<{
    team: TeamMember[];
    testimonials: Testimonial[];
}>();
</script>

<template>
    <Head title="About Us — Api Ghar Jagga">
        <meta
            head-key="description"
            name="description"
            content="Api Ghar Jagga is Nepal's trusted real estate services company offering property valuation, engineering consultancy, land survey, and brokerage."
        />
    </Head>

    <div class="min-h-screen bg-white text-slate-900">
        <AppHeader solid />

        <!-- Page banner -->
        <section class="bg-[#1a365d] pt-28 pb-16 text-white">
            <div class="mx-auto max-w-5xl px-4 text-center sm:px-6">
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    About Api Ghar Jagga
                </h1>
                <p
                    class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-white/80"
                >
                    A trusted partner for property valuation, engineering
                    consultancy, land survey, construction supervision, and real
                    estate services across Nepal.
                </p>
            </div>
        </section>

        <!-- Mission / Vision -->
        <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 p-6">
                    <h3
                        class="text-sm font-bold tracking-wide text-brand-600 uppercase"
                    >
                        Our Vision
                    </h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        To be Nepal's most trusted name in real estate services,
                        known for transparency, professionalism, and technical
                        excellence.
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-6">
                    <h3
                        class="text-sm font-bold tracking-wide text-brand-600 uppercase"
                    >
                        Our Mission
                    </h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        To deliver accurate valuations, reliable engineering
                        consultancy, and honest brokerage services that protect
                        our clients' interests at every step.
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-6">
                    <h3
                        class="text-sm font-bold tracking-wide text-brand-600 uppercase"
                    >
                        Our Values
                    </h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        Transparency, professionalism, and quick response — the
                        same standards our client service policy holds every
                        team member to.
                    </p>
                </div>
            </div>
        </section>

        <!-- Team -->
        <section v-if="team.length" class="bg-slate-50 py-16">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <h2 class="text-center text-2xl font-bold text-slate-900">
                    Our Team
                </h2>
                <div
                    class="mt-10 grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4"
                >
                    <div
                        v-for="member in team"
                        :key="member.member_id"
                        class="text-center"
                    >
                        <div
                            class="mx-auto h-24 w-24 overflow-hidden rounded-full bg-slate-200"
                        >
                            <img
                                v-if="member.photo_url"
                                :src="member.photo_url"
                                :alt="member.name"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <p class="mt-3 text-sm font-bold text-slate-900">
                            {{ member.name }}
                        </p>
                        <p
                            v-if="member.designation"
                            class="text-xs text-slate-500"
                        >
                            {{ member.designation }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section
            v-if="testimonials.length"
            class="mx-auto max-w-6xl px-4 py-16 sm:px-6"
        >
            <h2 class="text-center text-2xl font-bold text-slate-900">
                What Our Clients Say
            </h2>
            <div
                class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="testimonial in testimonials"
                    :key="testimonial.testimonial_id"
                    class="rounded-2xl border border-slate-200 p-6"
                >
                    <div class="flex items-center gap-1 text-amber-400">
                        <svg
                            v-for="i in testimonial.rating"
                            :key="i"
                            class="h-4 w-4"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                d="M10 15.27 16.18 19l-1.64-7.03L20 7.24l-7.19-.61L10 0 7.19 6.63 0 7.24l5.46 4.73L3.82 19z"
                            />
                        </svg>
                    </div>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        &ldquo;{{ testimonial.message }}&rdquo;
                    </p>
                    <p class="mt-4 text-sm font-bold text-slate-900">
                        {{ testimonial.client_name }}
                    </p>
                    <p
                        v-if="testimonial.client_role"
                        class="text-xs text-slate-500"
                    >
                        {{ testimonial.client_role }}
                    </p>
                </div>
            </div>
        </section>

        <AppFooter />
    </div>
</template>
