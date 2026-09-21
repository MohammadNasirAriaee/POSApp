<script setup>
import AppLayout from "../../Layouts/AppLayout.vue";
import Card from "../../Components/Card.vue";
import DataTable from "../../Components/DataTable.vue";
import Pagination from "../../Components/Pagination.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { Plus, Edit2, Trash2, Search, X } from "lucide-vue-next";
import { useDebouncedSearch } from "../../Composables/useDebouncedSearch";

const props = defineProps({
    categories: Object,
    search: String,
});

const form = useForm({});
const { searchQuery, clearSearch } = useDebouncedSearch(
    "categories.index",
    props.search,
);

const deleteCategory = (id) => {
    if (confirm("Are you sure you want to delete this category?")) {
        form.delete(route("categories.destroy", id));
    }
};
</script>

<template>
    <AppLayout>
        <div
            class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-surface-900">
                    Categories
                </h1>
                <p class="text-sm text-surface-500 mt-1">
                    Manage product categories for your inventory.
                </p>
            </div>
            <Link
                :href="route('categories.create')"
                class="metronic-btn metronic-btn-primary shrink-0"
            >
                <Plus class="w-4 h-4" /> Add Category
            </Link>
        </div>

        <Card>
            <template #header>
                <div class="relative w-full max-w-sm">
                    <label for="category-search" class="sr-only"
                        >Search categories</label
                    >
                    <div
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-surface-400"
                    >
                        <Search class="w-4 h-4" />
                    </div>
                    <input
                        id="category-search"
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search categories by name..."
                        class="w-full rounded-lg border border-surface-200 bg-white pl-9 pr-8 py-2 text-sm text-surface-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                    />
                    <button
                        v-if="searchQuery"
                        @click="clearSearch"
                        aria-label="Clear category search"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-surface-400 hover:text-surface-600"
                    >
                        <X class="w-4 h-4" />
                    </button>
                </div>
            </template>

            <DataTable
                :headers="['Name', 'Slug', 'Status', 'Products', 'Actions']"
                :items="categories.data"
                emptyMessage="No categories found. Create one to get started."
            >
                <template #rows="{ items }">
                    <tr
                        v-for="category in items"
                        :key="category.id"
                        class="hover:bg-surface-50/50 transition-colors"
                    >
                        <td class="py-4 px-6 font-bold text-surface-900">
                            {{ category.name }}
                        </td>
                        <td
                            class="py-4 px-6 text-surface-500 font-mono text-xs"
                        >
                            {{ category.slug }}
                        </td>
                        <td class="py-4 px-6">
                            <span
                                :class="[
                                    'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide',
                                    category.is_active
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : 'bg-surface-100 text-surface-600',
                                ]"
                            >
                                {{ category.is_active ? "Active" : "Inactive" }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <Link
                                :href="route('products.index', { category_id: category.id })"
                                class="text-surface-600 font-semibold hover:text-primary-600 hover:underline"
                            >
                                {{ category.products_count || 0 }}
                            </Link>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="
                                        route('categories.edit', category.id)
                                    "
                                    aria-label="Edit category"
                                    class="text-primary-600 hover:text-primary-800 p-1"
                                >
                                    <Edit2 class="w-4 h-4" />
                                </Link>
                                <button
                                    @click="deleteCategory(category.id)"
                                    class="text-rose-500 hover:text-rose-700 p-1"
                                >
                                    <span class="sr-only">Delete category</span>
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </DataTable>

            <Pagination :links="categories.links" />
        </Card>
    </AppLayout>
</template>
