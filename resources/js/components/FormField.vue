<template>
    <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
            {{ label }}
            <span v-if="labelNp" class="normal-case font-normal text-slate-400 ml-1">/ {{ labelNp }}</span>
            <span v-if="required" class="text-red-500 ml-0.5">*</span>
        </label>
        <slot />
        <p v-if="hint && !error" class="mt-1 text-xs text-slate-400">{{ hint }}</p>
        <p v-if="error" class="mt-1.5 text-red-500 text-xs flex items-center gap-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ error }}
        </p>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    label?: string;
    labelNp?: string;
    required?: boolean;
    error?: string;
    hint?: string;
}>();
</script>
