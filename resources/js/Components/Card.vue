<script setup>
defineProps({
    title: {
        type: String,
        default: ''
    },
    description: {
        type: String,
        default: ''
    }
});
</script>

<template>
    <div class="metronic-card">
        <div v-if="title || $slots.header" class="px-6 py-4 border-b border-surface-200 flex items-center justify-between bg-surface-50/50">
            <div>
                <h3 class="font-bold text-surface-900 text-lg">{{ title }}</h3>
                <p v-if="description" class="text-sm text-surface-500 mt-0.5">{{ description }}</p>
            </div>
            <div v-if="$slots.header">
                <slot name="header" />
            </div>
        </div>
        
        <div class="p-6">
            <slot />
        </div>
        
        <div v-if="$slots.footer" class="px-6 py-4 bg-surface-50 border-t border-surface-200 flex items-center justify-end gap-3">
            <slot name="footer" />
        </div>
    </div>
</template>
