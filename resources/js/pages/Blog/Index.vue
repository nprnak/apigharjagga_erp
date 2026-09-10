<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppFooter from '../../components/home/AppFooter.vue';
import AppHeader from '../../components/home/AppHeader.vue';

type Post = {
    post_id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    cover_image_url: string | null;
    category: string | null;
    published_at: string | null;
};

defineProps<{ posts: Post[] }>();

function formatDate(value: string | null): string {
    if (!value) {
        return '';
    }

    return new Date(value).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <Head title="News & Blog — Api Ghar Jagga">
        <meta
            head-key="description"
            name="description"
            content="Real estate news, market insights, and updates from Api Ghar Jagga."
        />
    </Head>

    <div class="min-h-screen bg-white text-slate-900">
        <AppHeader solid />

        <section class="bg-[#1a365d] pt-28 pb-16 text-white">
            <div class="mx-auto max-w-5xl px-4 text-center sm:px-6">
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    News &amp; Blog
                </h1>
                <p
                    class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-white/80"
                >
                    Market insights, company updates, and real estate guidance
                    from our team.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div
                v-if="posts.length === 0"
                class="rounded-2xl border border-slate-200 p-10 text-center text-sm text-slate-500"
            >
                No posts published yet. Check back soon.
            </div>

            <div
                v-else
                class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3"
            >
                <Link
                    v-for="post in posts"
                    :key="post.post_id"
                    :href="`/blog/${post.slug}`"
                    class="group overflow-hidden rounded-2xl border border-slate-200 transition hover:shadow-md"
                >
                    <div class="aspect-[16/9] overflow-hidden bg-slate-100">
                        <img
                            v-if="post.cover_image_url"
                            :src="post.cover_image_url"
                            :alt="post.title"
                            class="h-full w-full object-cover transition group-hover:scale-105"
                        />
                    </div>
                    <div class="p-5">
                        <div
                            class="flex items-center gap-2 text-xs text-slate-500"
                        >
                            <span
                                v-if="post.category"
                                class="rounded-full bg-brand-50 px-2 py-0.5 font-semibold text-brand-700"
                            >
                                {{ post.category }}
                            </span>
                            <span>{{ formatDate(post.published_at) }}</span>
                        </div>
                        <h3
                            class="mt-2 text-base font-bold text-slate-900 group-hover:text-brand-700"
                        >
                            {{ post.title }}
                        </h3>
                        <p
                            v-if="post.excerpt"
                            class="mt-2 line-clamp-2 text-sm text-slate-600"
                        >
                            {{ post.excerpt }}
                        </p>
                    </div>
                </Link>
            </div>
        </section>

        <AppFooter />
    </div>
</template>
