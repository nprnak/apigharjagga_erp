<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const nav = [
    { label: 'Buy', href: '/properties' },
    { label: 'Sell', href: '/property-listing' },
];

const scrolled = ref(false);

function onScroll() {
    scrolled.value = window.scrollY > 40;
}

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', onScroll);
});

const headerClass = computed(() =>
    scrolled.value
        ? 'bg-white/95 backdrop-blur border-b border-slate-200 shadow-sm'
        : 'bg-transparent border-b border-transparent',
);

const textClass = computed(() => (scrolled.value ? 'text-slate-800' : 'text-white'));
</script>

<template>
    <header :class="['fixed top-0 left-0 z-50 w-full transition-colors duration-300', headerClass]">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
            <div class="flex items-center gap-6 sm:gap-8">
                <Link href="/" class="flex items-center gap-2.5">
                    <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-white p-1 shadow-sm ring-1 ring-black/5">
                        <img src="/images/logo.png" alt="Api Ghar Jagga" class="h-full w-full object-contain" />
                    </span>
                    <span :class="['text-base font-bold tracking-wide transition-colors', textClass]">
                        Api Ghar Jagga
                    </span>
                </Link>

                <nav class="hidden items-center gap-6 sm:flex">
                    <a
                        v-for="item in nav"
                        :key="item.label"
                        :href="item.href"
                        :class="['text-sm font-semibold transition-colors hover:opacity-80', textClass]"
                    >
                        {{ item.label }}
                    </a>
                </nav>
            </div>

            <div class="flex items-center gap-4">
                <a
                    href="/signin"
                    :class="['text-sm font-semibold transition-colors hover:opacity-80', textClass]"
                >
                    Sign In
                </a>
                <a
                    href="/signup"
                    :class="[
                        'rounded-lg px-4 py-2 text-sm font-bold transition-colors',
                        scrolled
                            ? 'bg-brand-600 text-white hover:bg-brand-700'
                            : 'bg-white text-slate-900 hover:bg-slate-100',
                    ]"
                >
                    Sign Up
                </a>
            </div>
        </div>
    </header>
</template>
