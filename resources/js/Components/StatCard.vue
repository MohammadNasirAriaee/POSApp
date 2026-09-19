<script setup>
import Card from './Card.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        default: '',
    },
    // A lucide component, rendered inside the tinted badge.
    icon: {
        type: [Object, Function],
        default: null,
    },
    // Tailwind classes for the icon badge.
    tone: {
        type: String,
        default: 'bg-surface-100 text-surface-600',
    },
    // When given, the whole card becomes a link to this destination.
    href: {
        type: String,
        default: '',
    },
});

const isLink = computed(() => Boolean(props.href));
</script>

<template>
    <component
        :is="isLink ? Link : 'div'"
        :href="isLink ? href : undefined"
        :class="isLink ? 'block transition-transform hover:-translate-y-0.5' : ''"
    >
        <Card>
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                    :class="tone"
                >
                    <component :is="icon" v-if="icon" class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-surface-500 uppercase tracking-wider">{{ label }}</p>
                    <h4 class="text-2xl font-black text-surface-900 mt-1">{{ value }}</h4>
                </div>
            </div>
        </Card>
    </component>
</template>
