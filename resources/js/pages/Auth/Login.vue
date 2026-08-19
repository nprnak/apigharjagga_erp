<script setup lang="ts">
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
</script>

<template>
    <Head title="Sign In — API GharJagga MIS" />

    <div class="min-h-screen flex bg-slate-950">

        <!-- ── Left panel: Branding / Hero ── -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden flex-col justify-between p-12"
             style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%)">

            <!-- Decorative circles -->
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full opacity-20"
                 style="background: radial-gradient(circle, #3b82f6 0%, transparent 70%)"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #818cf8 0%, transparent 70%)"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full opacity-5 border border-blue-400"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full opacity-10 border border-blue-300"></div>

            <!-- Logo / brand -->
            <div class="relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center shadow-lg shadow-blue-500/50">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold text-xl tracking-tight">API GharJagga</span>
                </div>
            </div>

            <!-- Hero text -->
            <div class="relative z-10">
                <h1 class="text-5xl font-extrabold text-white leading-tight">
                    Nepal's Premier<br />
                    <span class="text-blue-300">Real Estate</span><br />
                    Platform
                </h1>
                <p class="mt-6 text-blue-200 text-lg leading-relaxed max-w-sm">
                    Buy, sell, and list properties with full KYC verification and secure transactions across Nepal.
                </p>

                <!-- Feature bullets -->
                <div class="mt-10 space-y-4">
                    <div v-for="feat in ['KYC-verified listings', 'Secure document management', 'Real-time approval tracking']"
                         :key="feat" class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-400/20 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-blue-100 text-sm font-medium">{{ feat }}</span>
                    </div>
                </div>
            </div>

            <!-- Bottom quote -->
            <div class="relative z-10 border-t border-white/10 pt-6">
                <p class="text-blue-300 text-sm italic">"Your trusted partner for property transactions in Nepal."</p>
            </div>
        </div>

        <!-- ── Right panel: Login form ── -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-16 xl:px-24 bg-slate-900">

            <!-- Mobile logo -->
            <div class="flex items-center gap-3 mb-10 lg:hidden">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="text-white font-bold text-lg">API GharJagga MIS</span>
            </div>

            <div class="max-w-md w-full mx-auto">
                <div class="mb-8">
                    <h2 class="text-3xl font-extrabold text-white">Welcome back</h2>
                    <p class="text-slate-400 mt-2">Sign in to your account to continue</p>
                </div>

                <!-- Status message -->
                <div v-if="status" class="mb-6 rounded-xl bg-emerald-400/10 border border-emerald-400/20 px-4 py-3 text-sm text-emerald-300">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">

                    <!-- Email -->
                    <div>
                        <label for="signin-email" class="block text-sm font-semibold text-slate-300 mb-1.5">Email address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4.5 h-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input
                                id="signin-email"
                                v-model="form.email"
                                type="email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="you@example.com"
                                class="w-full rounded-xl bg-slate-800 border border-slate-700 pl-11 pr-4 py-3 text-sm text-white placeholder-slate-500 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                            />
                        </div>
                        <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-400">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="signin-password" class="block text-sm font-semibold text-slate-300">Password</label>
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-xs text-blue-400 hover:text-blue-300 transition"
                            >
                                Forgot password?
                            </Link>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4.5 h-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input
                                id="signin-password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full rounded-xl bg-slate-800 border border-slate-700 pl-11 pr-12 py-3 text-sm text-white placeholder-slate-500 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                            />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-300 transition"
                                @click="showPassword = !showPassword"
                            >
                                <svg v-if="!showPassword" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg v-else class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-400">{{ form.errors.password }}</p>
                    </div>

                    <!-- Remember me -->
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            :class="[
                                'w-9 h-5 rounded-full transition-colors duration-200 relative shrink-0',
                                form.remember ? 'bg-blue-600' : 'bg-slate-700',
                            ]"
                            @click="form.remember = !form.remember"
                        >
                            <span :class="[
                                'absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform duration-200',
                                form.remember ? 'translate-x-4' : 'translate-x-0.5',
                            ]"></span>
                        </button>
                        <span class="text-sm text-slate-400">Remember me for 30 days</span>
                    </div>

                    <!-- Submit -->
                    <button
                        id="signin-submit"
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-xl bg-blue-600 hover:bg-blue-500 disabled:opacity-60 disabled:cursor-not-allowed py-3 text-sm font-bold text-white transition-all shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2"
                    >
                        <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ form.processing ? 'Signing in…' : 'Sign in' }}
                    </button>
                </form>

                <!-- Divider -->
                <div class="mt-8 border-t border-slate-800 pt-6 text-center">
                    <p class="text-slate-400 text-sm">
                        Don't have an account?
                        <Link :href="route('register')" class="text-blue-400 font-semibold hover:text-blue-300 transition ml-1">
                            Create one free
                        </Link>
                    </p>
                    <p class="mt-3 text-slate-600 text-xs">
                        Admin?
                        <a href="/admin" class="text-slate-500 hover:text-slate-400 transition ml-1">Go to admin panel →</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
