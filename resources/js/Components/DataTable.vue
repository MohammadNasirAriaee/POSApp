<script setup>
defineProps({
    headers: {
        type: Array,
        required: true,
    },
    items: {
        type: Array,
        default: () => [],
    },
    // Callers should vary this by whether a search/filter is active, so it
    // doesn't suggest creating a record while the user is just searching.
    emptyMessage: {
        type: String,
        default: 'No records found.'
    }
});
</script>

<template>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-surface-200 bg-surface-50/80">
                    <th v-for="(header, index) in headers" :key="index" scope="col" class="py-3.5 px-6 text-[11px] font-bold uppercase tracking-wider text-surface-500 whitespace-nowrap">
                        {{ header }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-sm">
                <template v-if="items.length">
                    <slot name="rows" :items="items" />
                </template>
                <tr v-else>
                    <td :colspan="headers.length" class="py-12 px-6 text-center text-surface-500 bg-surface-50/30">
                        {{ emptyMessage }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
