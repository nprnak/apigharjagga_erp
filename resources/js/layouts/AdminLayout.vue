<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const company = computed(() => (page.props as any).company);

const sidebarOpen = ref(true);

const nav = [
    {
        label: 'Dashboard',
        href: '/admin/dashboard',
        icon: '🏠',
        name: 'admin.dashboard',
    },
    {
        label: 'Clients',
        href: '/admin/clients',
        icon: '👥',
        name: 'admin.clients.index',
    },
    {
        label: 'Properties',
        href: '/admin/properties',
        icon: '🏘️',
        name: 'admin.properties.index',
    },
    {
        label: 'Valuations',
        href: '/admin/valuations',
        icon: '📊',
        name: 'admin.valuations.index',
    },
    {
        label: 'Staff',
        href: '/admin/staff',
        icon: '👤',
        name: 'admin.staff.index',
    },
    {
        label: 'Settings',
        href: '/admin/settings',
        icon: '⚙️',
        name: 'admin.settings.edit',
    },
];

const currentPath = computed(() => page.url);

function isActive(href: string) {
    return currentPath.value.startsWith(href);
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'w-64' : 'w-16'"
            class="flex flex-shrink-0 flex-col bg-slate-800 text-white transition-all duration-200"
        >
            <!-- Brand -->
            <div class="flex h-16 items-center justify-between px-4">
                <div v-if="sidebarOpen" class="flex items-center gap-2 overflow-hidden">
                    <img
                        v-if="company?.logo_url"
                        :src="company.logo_url"
                        alt="Logo"
                        class="h-9 w-9 flex-shrink-0 rounded object-contain"
                    />
                    <span class="truncate text-sm font-bold tracking-wide leading-tight">
                        {{ company?.company_name || 'Apighar Jagga' }}
                    </span>
                </div>
                <button
                    class="rounded p-1 hover:bg-slate-700"
                    @click="sidebarOpen = !sidebarOpen"
                >
                    <span class="text-xl">{{ sidebarOpen ? '◀' : '▶' }}</span>
                </button>
            </div>

            <!-- Nav -->
            <nav class="flex-1 space-y-1 px-2 py-4">
                <Link
                    v-for="item in nav"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        isActive(item.href)
                            ? 'bg-slate-600 text-white'
                            : 'text-slate-300 hover:bg-slate-700',
                        'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium',
                    ]"
                >
                    <span class="text-lg">{{ item.icon }}</span>
                    <span v-if="sidebarOpen">{{ item.label }}</span>
                </Link>
            </nav>

            <!-- Footer -->
            <div v-if="sidebarOpen" class="border-t border-slate-700 p-4">
                <p class="text-sm text-slate-400">{{ user?.name }}</p>
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="mt-1 text-xs text-slate-400 hover:text-white"
                >
                    Sign out
                </Link>
            </div>
        </aside>

        <!-- Main -->
        <div class="flex flex-1 flex-col overflow-hidden">
            <!-- Top bar -->
            <header class="flex h-16 items-center justify-between bg-white px-6 shadow">
                <h1 class="text-lg font-semibold text-gray-700">
                    <slot name="title">Admin Panel</slot>
                </h1>
                <div class="flex items-center gap-4">
                    <Link href="/" target="_blank" class="text-sm text-blue-600 hover:underline">
                        View Website
                    </Link>
                    <span class="text-sm text-gray-500">{{ user?.name }}</span>
                </div>
            </header>

            <!-- Flash messages -->
            <div v-if="$page.props.flash?.success" class="mx-6 mt-4 rounded-md bg-green-50 p-3 text-sm text-green-700">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.error" class="mx-6 mt-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
                {{ $page.props.flash.error }}
            </div>

            <!-- Content -->
            <main class="flex-1 overflow-auto p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
