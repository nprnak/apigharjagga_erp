<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type Errors = Record<string, string>;

const page = usePage<{ errors?: Errors }>();

const form = useForm({
    email: '',
    password: '',
});

const emailError = computed(() => page.props.errors?.email ?? '');
const generalError = computed(() => page.props.errors?.email ?? '');

function submit() {
    form.post('/signin', {
        onFinish: () => {
            // no-op
        },
    });
}
</script>

<template>
    <Head title="Sign in" />

    <div class="min-h-screen bg-slate-50 font-sans text-slate-800 selection:bg-emerald-500 selection:text-white">
        <div class="mx-auto flex min-h-screen max-w-4xl items-center justify-center px-4 py-12">
            <div class="w-full overflow-hidden rounded-3xl bg-white shadow-[0_20px_50px_-12px_rgba(0,0,0,0.1)]">
                <div class="relative overflow-hidden bg-linear-to-br from-emerald-500 to-emerald-700 px-8 py-10 text-center">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                    <div class="relative z-10 mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white/20 shadow-inner backdrop-blur-md">
                        <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M8.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M23 21v-2a4 4 0 0 0-3-3.87"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M16 3.13a4 4 0 0 1 0 7.75"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </div>

                    <h1 class="relative z-10 mt-4 text-3xl font-extrabold tracking-tight text-white">
                        Sign In
                    </h1>
                    <p class="relative z-10 mt-2 text-base font-medium text-emerald-100">
                        Access your marketplace account
                    </p>
                </div>

                <div class="relative z-10 px-8 py-10">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label class="mb-2 block text-xs font-bold tracking-wide text-slate-500 uppercase">Email</label>
                            <input
                                v-model="form.email"
                                type="email"
                                autocomplete="email"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                                placeholder="you@example.com"
                            />
                            <p v-if="emailError" class="mt-2 text-sm font-semibold text-red-600">
                                {{ emailError }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-bold tracking-wide text-slate-500 uppercase">Password</label>
                            <input
                                v-model="form.password"
                                type="password"
                                autocomplete="current-password"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                                placeholder="••••••••"
                            />
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-slate-900/20 transition hover:-translate-y-0.5 hover:bg-slate-800 disabled:opacity-60"
                        >
                            {{ form.processing ? 'Signing in...' : 'Sign in' }}
                        </button>

                        <p v-if="generalError" class="text-center text-sm font-semibold text-red-600">
                            {{ generalError }}
                        </p>

                        <div class="pt-2 text-center text-sm text-slate-500">
                            No agent section here. If you don’t have an account, ask your administrator to create a user in the system.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

