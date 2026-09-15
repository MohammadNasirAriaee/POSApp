<script setup>
import { computed, useAttrs, useId } from "vue";

// Attributes are forwarded to the <input> explicitly, so stop Vue from also
// dropping them on the wrapper <div> (which duplicated id/placeholder).
defineOptions({ inheritAttrs: false });

defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },
    type: {
        type: String,
        default: "text",
    },
    label: {
        type: String,
        default: "",
    },
    error: {
        type: String,
        default: "",
    },
});

defineEmits(["update:modelValue"]);

const attrs = useAttrs();
const generatedId = useId();

// Respect an explicit id when the caller supplies one, otherwise generate a
// stable one so the label and the error message can both point at the input.
const inputId = computed(() => attrs.id ?? `input-${generatedId}`);
const errorId = computed(() => `${inputId.value}-error`);
</script>

<template>
    <div class="w-full">
        <label
            v-if="label"
            :for="inputId"
            class="block text-sm font-semibold text-surface-700 mb-1.5"
            >{{ label }}</label
        >
        <input
            :id="inputId"
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            class="metronic-input"
            :class="[
                error
                    ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30'
                    : '',
            ]"
            :aria-invalid="error ? 'true' : 'false'"
            :aria-describedby="error ? errorId : undefined"
            v-bind="$attrs"
        />
        <p
            v-if="error"
            :id="errorId"
            class="mt-1.5 text-xs font-medium text-rose-600"
        >
            {{ error }}
        </p>
    </div>
</template>
