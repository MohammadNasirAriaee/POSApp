<script setup>
import { computed } from "vue";

const props = defineProps({
    status: {
        type: String,
        default: "",
    },
    // "badge" is the filled pill used in listings; "text" is the bare coloured
    // label the printed receipt uses.
    variant: {
        type: String,
        default: "badge",
    },
});

const BADGE_CLASSES = {
    completed: "bg-emerald-100 text-emerald-700",
    cancelled: "bg-rose-100 text-rose-700",
};

const TEXT_CLASSES = {
    completed: "text-emerald-600",
    cancelled: "text-rose-600",
};

const toneClass = computed(() =>
    props.variant === "text"
        ? (TEXT_CLASSES[props.status] ?? "text-amber-600")
        : (BADGE_CLASSES[props.status] ?? "bg-amber-100 text-amber-700"),
);

const shapeClass = computed(() =>
    props.variant === "text"
        ? "uppercase text-[10px] font-bold"
        : "inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide",
);

const label = computed(() => props.status.replace(/_/g, " "));
</script>

<template>
    <span :class="[shapeClass, toneClass]">{{ label }}</span>
</template>
