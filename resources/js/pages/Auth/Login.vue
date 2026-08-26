<script setup lang="ts">
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from '@/route';
import { ref } from 'vue';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const inputClass = (hasError: boolean) =>
    [
        'block w-full rounded-lg border bg-white px-3.5 py-3 text-sm text-slate-800 placeholder:text-slate-400',
        'focus:outline-none focus:ring-1',
        hasError
            ? 'border-red-400 focus:border-red-500 focus:ring-red-400'
            : 'border-slate-200 focus:border-slate-400 focus:ring-slate-300',
    ].join(' ');
</script>

<template>
    <Head title="Sign In — API GharJagga MIS" />

    <div class="min-h-screen bg-slate-200 p-3 sm:p-4 lg:p-5">
        <div
            class="relative min-h-[calc(100vh-1.5rem)] overflow-hidden rounded-[1.75rem] bg-cover bg-center shadow-sm sm:min-h-[calc(100vh-2rem)] lg:min-h-[calc(100vh-2.5rem)]"
            style="background-image: url('/images/home.jpg')"
        >
            <div class="absolute inset-0 bg-slate-900/10"></div>

            <!-- Logo -->
            <div class="absolute left-5 top-5 z-20 sm:left-8 sm:top-8">
                <Link href="/" class="inline-flex items-center gap-3">
                    <img
                        src="/images/logo.png"
                        alt="Api Ghar Jagga"
                        class="h-11 w-11 rounded-lg bg-white/90 object-contain p-1 shadow-sm"
                    />
                    <div class="leading-tight">
                        <span class="block text-base font-bold tracking-tight text-slate-900">API GharJagga</span>
                        <span class="block text-[11px] font-medium uppercase tracking-wide text-slate-600">
                            Real Estate MIS
                        </span>
                    </div>
                </Link>
            </div>

            <!-- Form card -->
            <div class="relative z-10 flex min-h-[inherit] items-center justify-center px-4 py-24 sm:px-8">
                <div class="w-full max-w-[420px]">
                    <div class="rounded-[1.75rem] bg-white px-8 py-10 shadow-xl shadow-slate-900/10 sm:px-10 sm:py-12">
                        <div class="mb-8 text-center">
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Sign in</h1>
                            <p class="mt-1.5 text-sm text-slate-500">Please enter your details</p>
                        </div>

                        <div
                            v-if="status"
                            class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-3 text-sm text-emerald-800"
                            role="status"
                        >
                            {{ status }}
                        </div>

                        <form class="space-y-5" @submit.prevent="submit">
                            <div>
                                <InputLabel for="signin-email" value="Email" class="mb-1.5 text-sm font-medium text-slate-500" />
                                <input
                                    id="signin-email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="name@company.com"
                                    :aria-invalid="Boolean(form.errors.email)"
                                    :class="inputClass(Boolean(form.errors.email))"
                                />
                                <InputError class="mt-1.5" :message="form.errors.email" />
                            </div>

                            <div>
                                <InputLabel for="signin-password" value="Password" class="mb-1.5 text-sm font-medium text-slate-500" />
                                <div class="relative">
                                    <input
                                        id="signin-password"
                                        v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        required
                                        autocomplete="current-password"
                                        placeholder="••••••••"
                                        :aria-invalid="Boolean(form.errors.password)"
                                        :class="[inputClass(Boolean(form.errors.password)), 'pr-11']"
                                    />
                                    <button
                                        type="button"
                                        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600"
                                        aria-label="Toggle password visibility"
                                        @click="showPassword = !showPassword"
                                    >
                                        <svg v-if="!showPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                                <InputError class="mt-1.5" :message="form.errors.password" />

                                <div class="mt-2.5 flex items-center justify-between gap-3">
                                    <label class="inline-flex items-center gap-2">
                                        <Checkbox
                                            id="remember"
                                            v-model:checked="form.remember"
                                            name="remember"
                                            class="border-slate-300 text-slate-700 focus:ring-slate-500"
                                        />
                                        <span class="text-sm text-slate-500">Remember me</span>
                                    </label>
                                    <Link
                                        v-if="canResetPassword"
                                        :href="route('password.request')"
                                        class="text-sm text-slate-600 underline underline-offset-2 hover:text-slate-900"
                                    >
                                        Forgot password?
                                    </Link>
                                </div>
                            </div>

                            <button
                                id="signin-submit"
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <svg
                                    v-if="form.processing"
                                    class="h-4 w-4 animate-spin"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                                <span>{{ form.processing ? 'Signing in…' : 'Sign in' }}</span>
                            </button>
                        </form>
                    </div>

                    <p class="mt-6 rounded-full bg-white/90 px-4 py-2.5 text-center text-sm font-medium text-slate-800 shadow-sm backdrop-blur-sm">
                        Are you new?
                        <Link
                            :href="route('register')"
                            class="font-semibold text-brand-700 underline underline-offset-2 hover:text-brand-800"
                        >
                            Create an Account
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
