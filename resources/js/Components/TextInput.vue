<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        required: true,
    },
    type: {
        type: String,
        default: 'text',
    },
    label: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="w-full">
        <label v-if="label" class="block text-sm font-semibold text-surface-700 mb-1.5">{{ label }}</label>
        <input
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            class="metronic-input"
            :class="[error ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : '']"
            v-bind="$attrs"
        />
        <p v-if="error" class="mt-1.5 text-xs font-medium text-rose-600">{{ error }}</p>
    </div>
</template>
