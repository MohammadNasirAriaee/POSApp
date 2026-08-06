<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import Card from '../../Components/Card.vue';
import DataTable from '../../Components/DataTable.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { Plus, Edit2, Trash2 } from 'lucide-vue-next';

defineProps({
    categories: Object,
});

const form = useForm({});

const deleteCategory = (id) => {
    if (confirm('Are you sure you want to delete this category?')) {
        form.delete(route('categories.destroy', id));
    }
};
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-surface-900">Categories</h1>
                <p class="text-sm text-surface-500 mt-1">Manage product categories for your inventory.</p>
            </div>
            <Link :href="route('categories.create')" class="metronic-btn metronic-btn-primary">
                <Plus class="w-4 h-4" /> Add Category
            </Link>
        </div>

        <Card>
            <DataTable 
                :headers="['Name', 'Slug', 'Status', 'Products', 'Actions']"
                :items="categories.data"
                emptyMessage="No categories found. Create one to get started."
            >
                <template #rows="{ items }">
                    <tr v-for="category in items" :key="category.id" class="hover:bg-surface-50/50 transition-colors">
                        <td class="py-4 px-6 font-bold text-surface-900">{{ category.name }}</td>
                        <td class="py-4 px-6 text-surface-500 font-mono text-xs">{{ category.slug }}</td>
                        <td class="py-4 px-6">
                            <span :class="[
                                'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide',
                                category.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-surface-100 text-surface-600'
                            ]">
                                {{ category.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-surface-600 font-semibold">{{ category.products_count || 0 }}</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <Link :href="route('categories.edit', category.id)" class="text-primary-600 hover:text-primary-800 p-1">
                                    <Edit2 class="w-4 h-4" />
                                </Link>
                                <button @click="deleteCategory(category.id)" class="text-rose-500 hover:text-rose-700 p-1">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </DataTable>
            
            <div v-if="categories.links && categories.links.length > 3" class="mt-6 flex items-center justify-center gap-1">
                <template v-for="(link, k) in categories.links" :key="k">
                    <div v-if="link.url === null" class="px-3 py-1 text-sm text-surface-400 border border-surface-200 rounded-lg" v-html="link.label"></div>
                    <Link v-else :href="link.url" class="px-3 py-1 text-sm border rounded-lg transition-colors" :class="link.active ? 'bg-primary-600 text-white border-primary-600' : 'border-surface-200 text-surface-700 hover:bg-surface-50'" v-html="link.label"></Link>
                </template>
            </div>
        </Card>
    </AppLayout>
</template>
