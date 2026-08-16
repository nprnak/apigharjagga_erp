<template>
    <div>
        <label
            class="mb-1.5 block text-xs font-semibold tracking-wide text-slate-500 uppercase"
        >
            {{ label }}
            <span v-if="required" class="ml-0.5 text-red-500">*</span>
        </label>

        <div
            class="relative overflow-hidden rounded-2xl border-2 border-dashed transition-colors"
            :class="
                error
                    ? 'border-red-300 bg-red-50'
                    : previewUrl
                      ? 'border-emerald-300 bg-emerald-50/40'
                      : 'border-slate-200 bg-slate-50 hover:border-slate-300'
            "
        >
            <input
                ref="inputEl"
                type="file"
                accept="image/jpeg,image/png,image/webp,image/jpg"
                class="absolute inset-0 z-10 cursor-pointer opacity-0"
                @change="onFileChange"
            />

            <div v-if="previewUrl" class="relative flex items-center gap-4 p-4">
                <img
                    :src="previewUrl"
                    alt="Signature preview"
                    class="h-20 w-40 rounded-lg border border-slate-200 bg-white object-contain"
                />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-slate-800">
                        {{ fileName }}
                    </p>
                    <p class="text-xs text-slate-500">{{ fileSize }}</p>
                    <button
                        type="button"
                        class="relative z-20 mt-2 text-xs font-bold text-red-600 hover:underline"
                        @click.stop.prevent="clear"
                    >
                        Remove
                    </button>
                </div>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center gap-2 px-4 py-8 text-center"
            >
                <svg
                    class="h-8 w-8 text-slate-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                </svg>
                <p class="text-sm font-semibold text-slate-600">
                    Upload scanned signature
                </p>
                <p class="text-xs text-slate-400">
                    {{ hint || 'JPG, PNG or WEBP — max 2 MB' }}
                </p>
            </div>
        </div>

        <p
            v-if="error"
            class="mt-1.5 flex items-center gap-1 text-xs text-red-500"
        >
            {{ error }}
        </p>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    modelValue: File | null;
    label?: string;
    required?: boolean;
    error?: string;
    hint?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: File | null];
}>();

const inputEl = ref<HTMLInputElement | null>(null);
const previewUrl = ref<string | null>(null);

const fileName = computed(() => props.modelValue?.name ?? '');
const fileSize = computed(() => {
    if (!props.modelValue) return '';
    const kb = props.modelValue.size / 1024;
    return kb >= 1024
        ? `${(kb / 1024).toFixed(1)} MB`
        : `${Math.round(kb)} KB`;
});

watch(
    () => props.modelValue,
    (file) => {
        if (previewUrl.value) URL.revokeObjectURL(previewUrl.value);
        previewUrl.value = file ? URL.createObjectURL(file) : null;
        if (!file && inputEl.value) inputEl.value.value = '';
    },
    { immediate: true },
);

function onFileChange(event: Event) {
    const input = event.target as HTMLInputElement;
    emit('update:modelValue', input.files?.[0] ?? null);
}

function clear() {
    emit('update:modelValue', null);
}
</script>
