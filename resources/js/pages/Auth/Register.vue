<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from '@/route';
import { ref, computed } from 'vue';

const showPassword = ref(false);
const showConfirm = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

// Clean password strength calculation
const strength = computed(() => {
    const p = form.password;
    if (!p) return 0;
    let s = 0;
    if (p.length >= 8) s++;
    if (/[A-Z]/.test(p)) s++;
    if (/[0-9]/.test(p)) s++;
    if (/[^A-Za-z0-9]/.test(p)) s++;
    return s;
});

const strengthLabel = computed(() => ['', 'Weak', 'Fair', 'Good', 'Strong'][strength.value] || '');
const strengthColor = computed(() => ['', 'bg-rose-500', 'bg-amber-500', 'bg-blue-500', 'bg-emerald-500'][strength.value] || 'bg-slate-200');

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Create Account — API GharJagga MIS" />

    <div class="min-h-screen flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50/70 relative overflow-hidden selection:bg-blue-600 selection:text-white">
        <!-- Vibrant ambient blue backdrop glows -->
        <div class="pointer-events-none absolute inset-0 flex items-center justify-center overflow-hidden">
            <div class="absolute -top-32 -right-32 w-[420px] h-[420px] bg-blue-500/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-[420px] h-[420px] bg-blue-600/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] bg-blue-300/20 rounded-full blur-3xl"></div>
            <!-- Subtle micro dot pattern -->
            <div class="absolute inset-0 bg-[radial-gradient(#94a3b8_1px,transparent_1px)] [background-size:24px_24px] opacity-25"></div>
        </div>

        <div class="relative z-10 sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Brand header with actual client logo -->
            <div class="text-center mb-8">
                <Link href="/" class="inline-flex flex-col items-center gap-3 group">
                    <div class="h-16 w-16 rounded-2xl bg-white p-2 shadow-lg shadow-blue-500/15 ring-1 ring-blue-100 group-hover:scale-105 group-hover:shadow-blue-500/25 transition-all duration-300 flex items-center justify-center">
                        <img src="/images/logo.png" alt="Api Ghar Jagga" class="h-full w-full object-contain" />
                    </div>
                    <div>
                        <span class="block text-xl font-extrabold text-slate-900 tracking-tight leading-tight">API GharJagga</span>
                        <span class="inline-block mt-0.5 text-xs font-semibold text-blue-600 uppercase tracking-wider bg-blue-50 border border-blue-200/70 px-2.5 py-0.5 rounded-full">
                            Real Estate MIS
                        </span>
                    </div>
                </Link>
                <h1 class="mt-5 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Create your account
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Join Nepal's premier verified property ecosystem
                </p>
            </div>

            <!-- Form card -->
            <div class="bg-white/90 backdrop-blur-xl border border-blue-100/80 shadow-2xl shadow-blue-900/10 rounded-3xl p-6 sm:p-8">
                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Full Name -->
                    <div>
                        <label for="reg-name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Full Name
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-blue-500/70">
                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input
                                id="reg-name"
                                v-model="form.name"
                                type="text"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Ram Sharma"
                                class="w-full rounded-xl bg-slate-50/80 border border-slate-200 pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/15 transition-all duration-200"
                            />
                        </div>
                        <p v-if="form.errors.name" class="mt-1.5 text-xs text-rose-500 font-medium">{{ form.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="reg-email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Email address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-blue-500/70">
                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input
                                id="reg-email"
                                v-model="form.email"
                                type="email"
                                required
                                autocomplete="username"
                                placeholder="name@company.com"
                                class="w-full rounded-xl bg-slate-50/80 border border-slate-200 pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/15 transition-all duration-200"
                            />
                        </div>
                        <p v-if="form.errors.email" class="mt-1.5 text-xs text-rose-500 font-medium">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="reg-password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-blue-500/70">
                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input
                                id="reg-password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                autocomplete="new-password"
                                placeholder="Min. 8 characters"
                                class="w-full rounded-xl bg-slate-50/80 border border-slate-200 pl-10 pr-11 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/15 transition-all duration-200"
                            />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-blue-600 transition"
                                @click="showPassword = !showPassword"
                                aria-label="Toggle password visibility"
                            >
                                <svg v-if="!showPassword" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>

                        <!-- Strength meter -->
                        <div v-if="form.password" class="mt-2.5 space-y-1.5">
                            <div class="flex gap-1.5">
                                <div
                                    v-for="i in 4"
                                    :key="i"
                                    :class="[
                                        'h-1.5 flex-1 rounded-full transition-all duration-300',
                                        i <= strength ? strengthColor : 'bg-slate-200'
                                    ]"
                                ></div>
                            </div>
                            <div class="flex justify-between items-center text-[11px]">
                                <span class="text-slate-400">Password strength</span>
                                <span :class="[
                                    'font-bold',
                                    strength >= 3 ? 'text-emerald-600' : strength === 2 ? 'text-amber-600' : 'text-rose-500'
                                ]">
                                    {{ strengthLabel }}
                                </span>
                            </div>
                        </div>
                        <p v-if="form.errors.password" class="mt-1.5 text-xs text-rose-500 font-medium">{{ form.errors.password }}</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="reg-confirm" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-blue-500/70">
                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <input
                                id="reg-confirm"
                                v-model="form.password_confirmation"
                                :type="showConfirm ? 'text' : 'password'"
                                required
                                autocomplete="new-password"
                                placeholder="Re-enter password"
                                class="w-full rounded-xl bg-slate-50/80 border border-slate-200 pl-10 pr-11 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/15 transition-all duration-200"
                            />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-blue-600 transition"
                                @click="showConfirm = !showConfirm"
                                aria-label="Toggle confirm password visibility"
                            >
                                <svg v-if="!showConfirm" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <p v-if="form.errors.password_confirmation" class="mt-1.5 text-xs text-rose-500 font-medium">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button
                            id="register-submit"
                            type="submit"
                            :disabled="form.processing"
                            class="w-full rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed py-2.5 px-4 text-sm font-bold text-white shadow-lg shadow-blue-600/25 hover:shadow-xl hover:shadow-blue-600/35 transition-all duration-200 flex items-center justify-center gap-2"
                        >
                            <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            <span>{{ form.processing ? 'Creating account…' : 'Create free account' }}</span>
                        </button>
                    </div>
                </form>

                <!-- Footer divider & links -->
                <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-600">
                        Already have an account?
                        <Link :href="route('login')" class="text-blue-600 font-bold hover:text-blue-700 transition ml-1">
                            Sign in here
                        </Link>
                    </p>
                </div>
            </div>

            <!-- Trust / Subtext -->
            <p class="mt-6 text-center text-xs text-slate-400 font-medium">
                By creating an account, you agree to our Terms &amp; Privacy Policy.
            </p>
        </div>
    </div>
</template>
