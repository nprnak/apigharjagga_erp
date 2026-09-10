<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppFooter from '../components/home/AppFooter.vue';
import AppHeader from '../components/home/AppHeader.vue';

type Document = {
    document_id: number;
    title: string;
    category: string | null;
    file_url: string | null;
    uploaded_at: string;
};

defineProps<{ documents: Document[] }>();
</script>

<template>
    <Head title="Document Downloads — Api Ghar Jagga">
        <meta
            head-key="description"
            name="description"
            content="Download brochures, forms, and policy documents from Api Ghar Jagga."
        />
    </Head>

    <div class="min-h-screen bg-white text-slate-900">
        <AppHeader solid />

        <section class="bg-[#1a365d] pt-28 pb-16 text-white">
            <div class="mx-auto max-w-5xl px-4 text-center sm:px-6">
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    Document Downloads
                </h1>
                <p
                    class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-white/80"
                >
                    Brochures, forms, and policy documents available for
                    download.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
            <div
                v-if="documents.length === 0"
                class="rounded-2xl border border-slate-200 p-10 text-center text-sm text-slate-500"
            >
                No documents available right now.
            </div>

            <ul
                v-else
                class="divide-y divide-slate-200 rounded-2xl border border-slate-200"
            >
                <li
                    v-for="doc in documents"
                    :key="doc.document_id"
                    class="flex items-center justify-between gap-4 p-5"
                >
                    <div>
                        <p class="text-sm font-bold text-slate-900">
                            {{ doc.title }}
                        </p>
                        <p v-if="doc.category" class="text-xs text-slate-500">
                            {{ doc.category }}
                        </p>
                    </div>
                    <a
                        v-if="doc.file_url"
                        :href="doc.file_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="shrink-0 rounded-lg bg-brand-600 px-4 py-2 text-xs font-bold text-white hover:bg-brand-700"
                    >
                        Download
                    </a>
                </li>
            </ul>
        </section>

        <AppFooter />
    </div>
</template>
