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

// Simple password strength meter
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

const strengthLabel = computed(() => ['', 'Weak', 'Fair', 'Good', 'Strong'][strength.value]);
const strengthColor = computed(() => ['', 'bg-red-500', 'bg-amber-500', 'bg-emerald-400', 'bg-emerald-500'][strength.value]);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Create Account — API GharJagga MIS" />

    <div class="min-h-screen flex bg-slate-950">

        <!-- ── Left panel: Form ── -->
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
                    <h2 class="text-3xl font-extrabold text-white">Create your account</h2>
                    <p class="text-slate-400 mt-2">Join thousands of property owners and buyers in Nepal</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">

                    <!-- Full Name -->
                    <div>
                        <label for="reg-name" class="block text-sm font-semibold text-slate-300 mb-1.5">Full Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4.5 h-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input
                                id="reg-name"
                                v-model="form.name"
                                type="text"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Your full name"
                                class="w-full rounded-xl bg-slate-800 border border-slate-700 pl-11 pr-4 py-3 text-sm text-white placeholder-slate-500 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                            />
                        </div>
                        <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-400">{{ form.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="reg-email" class="block text-sm font-semibold text-slate-300 mb-1.5">Email address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4.5 h-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input
                                id="reg-email"
                                v-model="form.email"
                                type="email"
                                required
                                autocomplete="username"
                                placeholder="you@example.com"
                                class="w-full rounded-xl bg-slate-800 border border-slate-700 pl-11 pr-4 py-3 text-sm text-white placeholder-slate-500 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                            />
                        </div>
                        <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-400">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="reg-password" class="block text-sm font-semibold text-slate-300 mb-1.5">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4.5 h-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input
                                id="reg-password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                autocomplete="new-password"
                                placeholder="Min. 8 characters"
                                class="w-full rounded-xl bg-slate-800 border border-slate-700 pl-11 pr-12 py-3 text-sm text-white placeholder-slate-500 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                            />
                            <button type="button" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-300 transition" @click="showPassword = !showPassword">
                                <svg v-if="!showPassword" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg v-else class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        <!-- Strength meter -->
                        <div v-if="form.password" class="mt-2">
                            <div class="flex gap-1">
                                <div v-for="i in 4" :key="i" :class="['h-1 flex-1 rounded-full transition-all', i <= strength ? strengthColor : 'bg-slate-700']"></div>
                            </div>
                            <p class="text-xs mt-1" :class="strength >= 3 ? 'text-emerald-400' : strength >= 2 ? 'text-amber-400' : 'text-red-400'">
                                Password strength: {{ strengthLabel }}
                            </p>
                        </div>
                        <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-400">{{ form.errors.password }}</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="reg-confirm" class="block text-sm font-semibold text-slate-300 mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4.5 h-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <input
                                id="reg-confirm"
                                v-model="form.password_confirmation"
                                :type="showConfirm ? 'text' : 'password'"
                                required
                                autocomplete="new-password"
                                placeholder="Re-enter your password"
                                class="w-full rounded-xl bg-slate-800 border border-slate-700 pl-11 pr-12 py-3 text-sm text-white placeholder-slate-500 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                            />
                            <button type="button" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-300 transition" @click="showConfirm = !showConfirm">
                                <svg v-if="!showConfirm" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg v-else class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        <p v-if="form.errors.password_confirmation" class="mt-1.5 text-xs text-red-400">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <!-- Submit -->
                    <button
                        id="register-submit"
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-xl bg-blue-600 hover:bg-blue-500 disabled:opacity-60 disabled:cursor-not-allowed py-3 text-sm font-bold text-white transition-all shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2"
                    >
                        <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ form.processing ? 'Creating account…' : 'Create account' }}
                    </button>
                </form>

                <div class="mt-8 border-t border-slate-800 pt-6 text-center">
                    <p class="text-slate-400 text-sm">
                        Already have an account?
                        <Link :href="route('login')" class="text-blue-400 font-semibold hover:text-blue-300 transition ml-1">
                            Sign in
                        </Link>
                    </p>
                </div>
            </div>
        </div>

        <!-- ── Right panel: Branding ── -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden flex-col justify-between p-12"
             style="background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 40%, #4f46e5 100%)">

            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full opacity-20"
                 style="background: radial-gradient(circle, #818cf8 0%, transparent 70%)"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #3b82f6 0%, transparent 70%)"></div>

            <!-- Logo -->
            <div class="relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold text-xl tracking-tight">API GharJagga</span>
                </div>
            </div>

            <!-- Content -->
            <div class="relative z-10">
                <h1 class="text-5xl font-extrabold text-white leading-tight">
                    Start your<br />
                    <span class="text-blue-200">property</span><br />
                    journey today
                </h1>
                <p class="mt-6 text-blue-100 text-lg leading-relaxed max-w-sm">
                    Create your free account and get verified in minutes. List or discover properties across all 7 provinces.
                </p>

                <!-- Steps -->
                <div class="mt-10 space-y-5">
                    <div v-for="(step, i) in ['Create account', 'Complete KYC verification', 'List or browse properties']"
                         :key="step" class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0 text-white text-sm font-bold">
                            {{ i + 1 }}
                        </div>
                        <span class="text-blue-100 text-sm font-medium">{{ step }}</span>
                    </div>
                </div>
            </div>

            <div class="relative z-10 border-t border-white/10 pt-6">
                <p class="text-blue-200 text-sm">Free forever for individual property owners.</p>
            </div>
        </div>
    </div>
</template>
