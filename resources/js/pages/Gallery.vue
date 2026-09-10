<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppFooter from '../components/home/AppFooter.vue';
import AppHeader from '../components/home/AppHeader.vue';

type GalleryImage = {
    image_id: number;
    image_url: string | null;
    caption: string | null;
};

type Album = {
    album_id: number;
    title: string;
    description: string | null;
    cover_image_url: string | null;
    images: GalleryImage[];
};

defineProps<{ albums: Album[] }>();

const lightboxImage = ref<GalleryImage | null>(null);
</script>

<template>
    <Head title="Gallery — Api Ghar Jagga">
        <meta
            head-key="description"
            name="description"
            content="Photos from Api Ghar Jagga's projects, properties, and events."
        />
    </Head>

    <div class="min-h-screen bg-white text-slate-900">
        <AppHeader solid />

        <section class="bg-[#1a365d] pt-28 pb-16 text-white">
            <div class="mx-auto max-w-5xl px-4 text-center sm:px-6">
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    Gallery
                </h1>
                <p
                    class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-white/80"
                >
                    A look at our projects, properties, and team in action.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div
                v-if="albums.length === 0"
                class="rounded-2xl border border-slate-200 p-10 text-center text-sm text-slate-500"
            >
                No albums published yet.
            </div>

            <div v-for="album in albums" :key="album.album_id" class="mb-14">
                <h2 class="text-xl font-bold text-slate-900">
                    {{ album.title }}
                </h2>
                <p v-if="album.description" class="mt-1 text-sm text-slate-500">
                    {{ album.description }}
                </p>

                <div
                    class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4"
                >
                    <button
                        v-for="image in album.images"
                        :key="image.image_id"
                        type="button"
                        class="aspect-square overflow-hidden rounded-xl bg-slate-100"
                        @click="lightboxImage = image"
                    >
                        <img
                            v-if="image.image_url"
                            :src="image.image_url"
                            :alt="image.caption ?? album.title"
                            class="h-full w-full object-cover transition hover:scale-105"
                        />
                    </button>
                </div>
            </div>
        </section>

        <AppFooter />

        <!-- Lightbox -->
        <div
            v-if="lightboxImage"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/80 p-4"
            @click="lightboxImage = null"
        >
            <img
                v-if="lightboxImage.image_url"
                :src="lightboxImage.image_url"
                :alt="lightboxImage.caption ?? ''"
                class="max-h-[85vh] max-w-full rounded-lg object-contain"
            />
            <button
                type="button"
                class="absolute top-5 right-5 text-2xl text-white"
                @click="lightboxImage = null"
            >
                ✕
            </button>
        </div>
    </div>
</template>
