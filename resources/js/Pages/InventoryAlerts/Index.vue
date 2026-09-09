<script setup>
import AppLayout from "../../Layouts/AppLayout.vue";
import Card from "../../Components/Card.vue";
import { Link } from "@inertiajs/vue3";
import { AlertTriangle, ArrowRight, PackageSearch } from "lucide-vue-next";

defineProps({
    alerts: {
        type: Array,
        default: () => [],
    },
});

const formatStockStatus = (quantity) => {
    if (quantity === 0) return "Out of stock";
    if (quantity <= 2) return "Critical";
    return "Low stock";
};
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-surface-900">
                    Inventory Alerts
                </h1>
                <p class="text-sm text-surface-500 mt-1">
                    Products that need attention before they run out.
                </p>
            </div>
            <Link
                :href="route('products.index')"
                class="metronic-btn metronic-btn-primary"
            >
                <PackageSearch class="w-4 h-4" /> View Products
            </Link>
        </div>

        <Card
            title="Low stock items"
            :description="`${alerts.length} product(s) need restocking.`"
        >
            <div v-if="alerts.length" class="space-y-4">
                <div
                    v-for="product in alerts"
                    :key="product.id"
                    class="flex flex-col gap-4 rounded-2xl border border-amber-200 bg-amber-50/60 p-4 md:flex-row md:items-center md:justify-between"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-700"
                        >
                            <AlertTriangle class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-lg font-bold text-surface-900">
                                {{ product.name }}
                            </p>
                            <div
                                class="mt-1 flex flex-wrap items-center gap-2 text-sm text-surface-500"
                            >
                                <span>{{ product.sku }}</span>
                                <span class="hidden md:inline">•</span>
                                <span>{{
                                    product.category?.name || "Uncategorized"
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 md:items-end">
                        <div class="flex items-center gap-2">
                            <span
                                class="text-2xl font-black text-surface-900"
                                >{{ product.stock_quantity }}</span
                            >
                            <span
                                class="text-xs font-bold uppercase tracking-wide text-amber-700"
                                >left</span
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-800"
                            >
                                {{ formatStockStatus(product.stock_quantity) }}
                            </span>
                            <Link
                                :href="route('products.edit', product.id)"
                                class="inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-800"
                            >
                                Reorder
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="rounded-2xl border border-dashed border-surface-200 bg-surface-50 py-12 text-center"
            >
                <div
                    class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-700"
                >
                    <PackageSearch class="h-5 w-5" />
                </div>
                <p class="text-lg font-bold text-surface-900">All clear</p>
                <p class="mt-1 text-sm text-surface-500">
                    There are no low-stock products at the moment.
                </p>
            </div>
        </Card>
    </AppLayout>
</template>
