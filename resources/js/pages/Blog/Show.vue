<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppFooter from '../../components/home/AppFooter.vue';
import AppHeader from '../../components/home/AppHeader.vue';

type Post = {
    post_id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    content: string;
    cover_image_url: string | null;
    category: string | null;
    published_at: string | null;
    author: { full_name: string } | null;
};

type RelatedPost = {
    post_id: number;
    title: string;
    slug: string;
    cover_image_url: string | null;
};

const props = defineProps<{
    post: Post;
    related: RelatedPost[];
}>();

function formatDate(value: string | null): string {
    if (!value) {
        return '';
    }

    return new Date(value).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

// Share links point at this exact page — computed client-side since the
// canonical URL isn't known until the page is actually loaded in a browser.
const shareUrl = computed(() =>
    typeof window !== 'undefined'
        ? encodeURIComponent(window.location.href)
        : '',
);
const shareTitle = computed(() => encodeURIComponent(props.post.title));

const shareLinks = computed(() => [
    {
        label: 'Facebook',
        href: `https://www.facebook.com/sharer/sharer.php?u=${shareUrl.value}`,
    },
    {
        label: 'X / Twitter',
        href: `https://twitter.com/intent/tweet?url=${shareUrl.value}&text=${shareTitle.value}`,
    },
    {
        label: 'LinkedIn',
        href: `https://www.linkedin.com/sharing/share-offsite/?url=${shareUrl.value}`,
    },
    {
        label: 'WhatsApp',
        href: `https://wa.me/?text=${shareTitle.value}%20${shareUrl.value}`,
    },
]);
</script>

<template>
    <Head :title="`${post.title} — Api Ghar Jagga`">
        <meta
            v-if="post.excerpt"
            head-key="description"
            name="description"
            :content="post.excerpt"
        />
    </Head>

    <div class="min-h-screen bg-white text-slate-900">
        <AppHeader solid />

        <article class="pt-24">
            <div
                v-if="post.cover_image_url"
                class="aspect-[21/9] w-full overflow-hidden bg-slate-100"
            >
                <img
                    :src="post.cover_image_url"
                    :alt="post.title"
                    class="h-full w-full object-cover"
                />
            </div>

            <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span
                        v-if="post.category"
                        class="rounded-full bg-brand-50 px-2 py-0.5 font-semibold text-brand-700"
                    >
                        {{ post.category }}
                    </span>
                    <span>{{ formatDate(post.published_at) }}</span>
                    <span v-if="post.author"
                        >· By {{ post.author.full_name }}</span
                    >
                </div>

                <h1
                    class="mt-3 text-3xl font-bold tracking-tight text-slate-900"
                >
                    {{ post.title }}
                </h1>

                <!-- eslint-disable-next-line vue/no-v-html -->
                <div
                    class="prose prose-slate mt-8 max-w-none"
                    v-html="post.content"
                />

                <div
                    class="mt-10 flex items-center gap-3 border-t border-slate-200 pt-6"
                >
                    <span class="text-sm font-semibold text-slate-600"
                        >Share:</span
                    >
                    <a
                        v-for="link in shareLinks"
                        :key="link.label"
                        :href="link.href"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200"
                    >
                        {{ link.label }}
                    </a>
                </div>

                <div
                    v-if="related.length"
                    class="mt-14 border-t border-slate-200 pt-10"
                >
                    <h3
                        class="text-sm font-bold tracking-wide text-slate-500 uppercase"
                    >
                        Related Posts
                    </h3>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <Link
                            v-for="item in related"
                            :key="item.post_id"
                            :href="`/blog/${item.slug}`"
                            class="group overflow-hidden rounded-xl border border-slate-200"
                        >
                            <div class="aspect-[16/9] bg-slate-100">
                                <img
                                    v-if="item.cover_image_url"
                                    :src="item.cover_image_url"
                                    :alt="item.title"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <p
                                class="p-3 text-sm font-semibold text-slate-800 group-hover:text-brand-700"
                            >
                                {{ item.title }}
                            </p>
                        </Link>
                    </div>
                </div>
            </div>
        </article>

        <AppFooter />
    </div>
</template>
