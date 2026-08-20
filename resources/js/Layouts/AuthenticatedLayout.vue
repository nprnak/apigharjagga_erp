<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from '@/route';

const showingNavigationDropdown = ref(false);
const showUserMenu = ref(false);

const page = usePage();
const user = computed(() => (page.props as any).auth?.user);
const isAdmin = computed(() => user.value?.role === 'admin');

// Close dropdown when clicking outside
function closeDropdown() {
    showUserMenu.value = false;
}

// Avatar initials
const initials = computed(() => {
    const n = user.value?.name ?? '';
    return n.split(' ').map((p: string) => p[0]).join('').toUpperCase().slice(0, 2);
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <!-- Top Nav -->
        <nav class="sticky top-0 z-50 border-b border-white/10 bg-slate-900/80 backdrop-blur-xl">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">

                    <!-- Left: Logo + Nav links -->
                    <div class="flex items-center gap-8">
                        <Link :href="route('dashboard')" class="flex items-center gap-2.5 group">
                            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center group-hover:bg-blue-500 transition-colors shadow-lg shadow-blue-600/30">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                    <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            </div>
                            <span class="font-bold text-white text-sm tracking-tight hidden sm:block">API GharJagga</span>
                        </Link>

                        <div class="hidden sm:flex items-center gap-1">
                            <Link
                                :href="route('dashboard')"
                                :class="[
                                    'rounded-lg px-3 py-2 text-sm font-medium transition-all',
                                    route().current('dashboard')
                                        ? 'bg-blue-600/20 text-blue-300'
                                        : 'text-slate-400 hover:text-white hover:bg-white/5',
                                ]"
                            >
                                Dashboard
                            </Link>
                            <Link
                                :href="route('properties.index')"
                                :class="[
                                    'rounded-lg px-3 py-2 text-sm font-medium transition-all',
                                    route().current('properties.index')
                                        ? 'bg-blue-600/20 text-blue-300'
                                        : 'text-slate-400 hover:text-white hover:bg-white/5',
                                ]"
                            >
                                Browse Properties
                            </Link>
                            <a
                                v-if="isAdmin"
                                href="/admin"
                                class="rounded-lg px-3 py-2 text-sm font-medium text-amber-400 hover:text-amber-300 hover:bg-amber-400/10 transition-all flex items-center gap-1.5"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Admin Panel
                            </a>
                        </div>
                    </div>

                    <!-- Right: User menu -->
                    <div class="flex items-center gap-3">
                        <!-- User avatar dropdown -->
                        <div class="relative" v-click-outside="closeDropdown">
                            <button
                                type="button"
                                class="flex items-center gap-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 px-3 py-1.5 text-sm font-medium text-white transition-all"
                                @click="showUserMenu = !showUserMenu"
                            >
                                <!-- Avatar -->
                                <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-xs font-bold text-white">
                                    {{ initials }}
                                </div>
                                <span class="hidden sm:block max-w-32 truncate text-sm text-slate-200">{{ user?.name }}</span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform" :class="showUserMenu ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <Transition
                                enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95"
                            >
                                <div
                                    v-if="showUserMenu"
                                    class="absolute right-0 mt-2 w-56 rounded-2xl border border-white/10 bg-slate-800 shadow-2xl shadow-black/50 overflow-hidden z-50"
                                >
                                    <!-- User info -->
                                    <div class="px-4 py-3 border-b border-white/10">
                                        <p class="text-sm font-semibold text-white truncate">{{ user?.name }}</p>
                                        <p class="text-xs text-slate-400 truncate mt-0.5">{{ user?.email }}</p>
                                        <span v-if="isAdmin" class="mt-2 inline-flex items-center rounded-full bg-amber-400/10 px-2 py-0.5 text-xs font-bold text-amber-400 ring-1 ring-amber-400/20">
                                            Admin
                                        </span>
                                    </div>
                                    <div class="py-1">
                                        <Link
                                            :href="route('profile.edit')"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition-all"
                                            @click="showUserMenu = false"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            Profile Settings
                                        </Link>
                                        <a
                                            v-if="isAdmin"
                                            href="/admin"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-amber-400 hover:bg-amber-400/5 transition-all"
                                            @click="showUserMenu = false"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            Admin Panel
                                        </a>
                                    </div>
                                    <div class="border-t border-white/10 py-1">
                                        <Link
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                            class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-400 hover:bg-red-400/5 transition-all"
                                            @click="showUserMenu = false"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Sign Out
                                        </Link>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <!-- Mobile hamburger -->
                        <button
                            type="button"
                            class="sm:hidden rounded-lg p-2 text-slate-400 hover:text-white hover:bg-white/5 transition"
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path v-if="!showingNavigationDropdown" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile nav dropdown -->
            <Transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showingNavigationDropdown" class="sm:hidden border-t border-white/10 bg-slate-900 px-4 py-3 space-y-1">
                    <Link :href="route('dashboard')" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-300 hover:bg-white/5 hover:text-white transition">Dashboard</Link>
                    <Link :href="route('properties.index')" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-300 hover:bg-white/5 hover:text-white transition">Browse Properties</Link>
                    <Link :href="route('profile.edit')" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-300 hover:bg-white/5 hover:text-white transition">Profile</Link>
                    <a v-if="isAdmin" href="/admin" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-amber-400 hover:bg-amber-400/5 transition">Admin Panel</a>
                    <Link :href="route('logout')" method="post" as="button" class="block w-full text-left rounded-lg px-3 py-2.5 text-sm font-medium text-red-400 hover:bg-red-400/5 transition">Sign Out</Link>
                </div>
            </Transition>
        </nav>

        <!-- Page Heading -->
        <header v-if="$slots.header" class="border-b border-white/10 bg-white/5">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
