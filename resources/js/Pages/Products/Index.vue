<script setup>
import AppLayout from "../../Layouts/AppLayout.vue";
import Card from "../../Components/Card.vue";
import DataTable from "../../Components/DataTable.vue";
import Pagination from "../../Components/Pagination.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { Plus, Edit2, Trash2, Search, X } from "lucide-vue-next";
import { ref, watch } from "vue";
import { useDebouncedSearch } from "../../Composables/useDebouncedSearch";
import { formatMoney } from "../../Support/money";

const props = defineProps({
    products: Object,
    search: String,
    status: String,
    categoryId: Number,
    categories: {
        type: Array,
        default: () => [],
    },
    lowStockThreshold: {
        type: Number,
        required: true,
    },
    statusLabels: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({});
const statusFilter = ref(props.status || "");
const categoryFilter = ref(props.categoryId ?? "");

const { searchQuery, clearSearch } = useDebouncedSearch(
    "products.index",
    props.search,
    () => ({ status: statusFilter.value, category_id: categoryFilter.value }),
);

watch([statusFilter, categoryFilter], ([status, categoryId]) => {
    router.get(
        route("products.index"),
        {
            search: searchQuery.value,
            status,
            category_id: categoryId,
        },
        { preserveState: true, replace: true },
    );
});

const STATUS_BADGE_CLASSES = {
    active: "bg-emerald-100 text-emerald-700",
    out_of_stock: "bg-rose-100 text-rose-700",
};

const statusBadgeClass = (status) =>
    STATUS_BADGE_CLASSES[status] ?? "bg-surface-100 text-surface-600";

// Cost is collected on the create/edit form but was never shown anywhere -
// this is the only place a cashier or manager can see how thin a product's
// margin actually is. Returns null (rendered as "-") when no cost was set.
const marginPercent = (product) => {
    const price = Number(product.price) || 0;
    const cost = Number(product.cost);

    if (price <= 0 || product.cost === null || Number.isNaN(cost)) {
        return null;
    }

    return ((price - cost) / price) * 100;
};

const deleteProduct = (product) => {
    if (confirm(`Delete ${product.name}?`)) {
        form.delete(route("products.destroy", product.id));
    }
};

</script>

<template>
    <Head title="Products" />
    <AppLayout>
        <div
            class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-surface-900">
                    Products
                </h1>
                <p class="text-sm text-surface-500 mt-1">
                    Manage your inventory, pricing, and stock levels.
                </p>
            </div>
            <Link
                :href="route('products.create')"
                class="metronic-btn metronic-btn-primary shrink-0"
            >
                <Plus class="w-4 h-4" /> Add Product
            </Link>
        </div>

        <Card>
            <template #header>
                <div class="flex w-full flex-col gap-3 sm:flex-row">
                    <div class="relative w-full max-w-sm">
                        <label for="product-search" class="sr-only"
                            >Search products</label
                        >
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-surface-400"
                        >
                            <Search class="w-4 h-4" />
                        </div>
                        <input
                            id="product-search"
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search products by name or SKU..."
                            class="w-full rounded-lg border border-surface-200 bg-white pl-9 pr-8 py-2 text-sm text-surface-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                        />
                        <button
                            v-if="searchQuery"
                            @click="clearSearch"
                            aria-label="Clear product search"
                            title="Clear search"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-surface-400 hover:text-surface-600"
                        >
                            <X class="w-4 h-4" />
                        </button>
                    </div>
                    <label for="product-category" class="sr-only"
                        >Filter products by category</label
                    >
                    <select
                        id="product-category"
                        v-model="categoryFilter"
                        class="metronic-input w-full sm:w-44"
                    >
                        <option value="">All categories</option>
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.name }}
                        </option>
                    </select>
                    <label for="product-status" class="sr-only"
                        >Filter products by status</label
                    >
                    <select
                        id="product-status"
                        v-model="statusFilter"
                        class="metronic-input w-full sm:w-44"
                    >
                        <option value="">All statuses</option>
                        <option
                            v-for="(label, value) in statusLabels"
                            :key="value"
                            :value="value"
                        >
                            {{ label }}
                        </option>
                    </select>
                </div>
            </template>

            <DataTable
                :headers="[
                    'Product',
                    'SKU',
                    'Category',
                    'Price',
                    'Margin',
                    'Stock',
                    'Status',
                    'Actions',
                ]"
                :items="products.data"
                emptyMessage="No products found matching your criteria."
            >
                <template #rows="{ items }">
                    <tr
                        v-for="product in items"
                        :key="product.id"
                        class="hover:bg-surface-50/50 transition-colors"
                    >
                        <td class="py-4 px-6 font-bold text-surface-900">
                            {{ product.name }}
                        </td>
                        <td
                            class="py-4 px-6 text-surface-500 font-mono text-xs"
                        >
                            {{ product.sku }}
                        </td>
                        <td class="py-4 px-6 text-surface-600 font-medium">
                            {{
                                product.category
                                    ? product.category.name
                                    : "Uncategorized"
                            }}
                        </td>
                        <td class="py-4 px-6 font-bold text-surface-900">
                            {{ formatMoney(product.price) }}
                        </td>
                        <td class="py-4 px-6">
                            <span
                                v-if="marginPercent(product) !== null"
                                :class="[
                                    'font-semibold',
                                    marginPercent(product) < 10
                                        ? 'text-rose-600'
                                        : 'text-surface-600',
                                ]"
                            >
                                {{ marginPercent(product).toFixed(0) }}%
                            </span>
                            <span v-else class="text-surface-400">&mdash;</span>
                        </td>
                        <td class="py-4 px-6">
                            <span
                                :class="[
                                    'font-bold',
                                    product.stock_quantity <=
                                    lowStockThreshold
                                        ? 'text-rose-600'
                                        : 'text-surface-700',
                                ]"
                            >
                                {{ product.stock_quantity }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <span
                                :class="[
                                    'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide',
                                    statusBadgeClass(product.status),
                                ]"
                            >
                                {{ statusLabels[product.status] ?? product.status }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="route('products.edit', product.id)"
                                    :aria-label="`Edit ${product.name}`"
                                    class="text-primary-600 hover:text-primary-800 p-1"
                                >
                                    <Edit2 class="w-4 h-4" />
                                </Link>
                                <button
                                    @click="deleteProduct(product)"
                                    :aria-label="`Delete ${product.name}`"
                                    :disabled="form.processing"
                                    class="text-rose-500 hover:text-rose-700 p-1 disabled:opacity-50"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </DataTable>

            <Pagination :links="products.links" />
        </Card>
    </AppLayout>
</template>
