<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    // Laravel paginator links: [{ url, label, active }, ...]
    links: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <!-- Fewer than 4 links means a single page (previous, "1", next). -->
    <nav
        v-if="links.length > 3"
        aria-label="Pagination"
        class="mt-6 flex items-center justify-center gap-1"
    >
        <template v-for="(link, index) in links" :key="index">
            <span
                v-if="link.url === null"
                class="px-3 py-1 text-sm text-surface-400 border border-surface-200 rounded-lg"
                v-html="link.label"
            ></span>
            <Link
                v-else
                :href="link.url"
                :aria-current="link.active ? 'page' : undefined"
                class="px-3 py-1 text-sm border rounded-lg transition-colors"
                :class="
                    link.active
                        ? 'bg-primary-600 text-white border-primary-600'
                        : 'border-surface-200 text-surface-700 hover:bg-surface-50'
                "
                v-html="link.label"
            ></Link>
        </template>
    </nav>
</template>
