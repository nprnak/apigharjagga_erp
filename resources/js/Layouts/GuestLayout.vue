<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50/70 relative overflow-hidden selection:bg-blue-600 selection:text-white">
        <!-- Vibrant ambient blue backdrop glows -->
        <div class="pointer-events-none absolute inset-0 flex items-center justify-center overflow-hidden">
            <div class="absolute -top-32 -left-32 w-[420px] h-[420px] bg-blue-400/25 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -right-32 w-[420px] h-[420px] bg-blue-500/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] bg-blue-300/20 rounded-full blur-3xl"></div>
            <!-- Subtle dot pattern -->
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
            </div>

            <!-- Card container -->
            <div class="bg-white/90 backdrop-blur-xl border border-blue-100/80 shadow-2xl shadow-blue-900/10 rounded-3xl p-6 sm:p-8">
                <slot />
            </div>

            <p class="mt-6 text-center text-xs text-slate-400 font-medium">
                API GharJagga MIS &bull; Secure Real Estate Platform
            </p>
        </div>
    </div>
</template>
