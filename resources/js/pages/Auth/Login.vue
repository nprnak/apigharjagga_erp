<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Login" />

    <div class="flex min-h-screen items-center justify-center bg-slate-50 px-4">
        <div class="w-full max-w-sm">
            <!-- Logo / Brand -->
            <div class="mb-8 text-center">
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-slate-700 text-white text-xl font-bold">AJ</div>
                <h1 class="text-2xl font-bold text-gray-800">Apighar Jagga</h1>
                <p class="text-sm text-gray-500">Property & Engineering Consultancy</p>
            </div>

            <div class="rounded-xl bg-white p-8 shadow">
                <h2 class="mb-6 text-center text-lg font-semibold text-gray-700">Sign in to your account</h2>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            class="w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400"
                            :class="{ 'border-red-400': form.errors.email }"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                        <input
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            class="w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400"
                        />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input v-model="form.remember" type="checkbox" class="h-4 w-4 rounded border-gray-300" />
                            Remember me
                        </label>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-md bg-slate-700 py-2 text-sm font-semibold text-white hover:bg-slate-800 disabled:opacity-60"
                    >
                        Sign In
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-gray-400">
                &copy; {{ new Date().getFullYear() }} Apighar Jagga. All rights reserved.
            </p>
        </div>
    </div>
</template>
