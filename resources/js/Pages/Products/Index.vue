<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import Card from '../../Components/Card.vue';
import DataTable from '../../Components/DataTable.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { Plus, Edit2, Trash2, Search, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
    products: Object,
    search: String,
});

const form = useForm({});
const searchQuery = ref(props.search || '');

let searchTimeout = null;
watch(searchQuery, (value) => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('products.index'), { search: value }, { preserveState: true, replace: true });
    }, 300);
});

const clearSearch = () => {
    searchQuery.value = '';
    router.get(route('products.index'));
};

const deleteProduct = (id) => {
    if (confirm('Are you sure you want to delete this product?')) {
        form.delete(route('products.destroy', id));
    }
};

const formatMoney = (amount) => {
    return '$' + parseFloat(amount).toFixed(2);
};
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-surface-900">Products</h1>
                <p class="text-sm text-surface-500 mt-1">Manage your inventory, pricing, and stock levels.</p>
            </div>
            <Link :href="route('products.create')" class="metronic-btn metronic-btn-primary shrink-0">
                <Plus class="w-4 h-4" /> Add Product
            </Link>
        </div>

        <Card>
            <template #header>
                <div class="relative w-full max-w-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-surface-400">
                        <Search class="w-4 h-4" />
                    </div>
                    <input 
                        v-model="searchQuery" 
                        type="text" 
                        placeholder="Search products by name or SKU..." 
                        class="w-full rounded-lg border border-surface-200 bg-white pl-9 pr-8 py-2 text-sm text-surface-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                    />
                    <button v-if="searchQuery" @click="clearSearch" class="absolute inset-y-0 right-0 flex items-center pr-3 text-surface-400 hover:text-surface-600">
                        <X class="w-4 h-4" />
                    </button>
                </div>
            </template>

            <DataTable 
                :headers="['Product', 'SKU', 'Category', 'Price', 'Stock', 'Status', 'Actions']"
                :items="products.data"
                emptyMessage="No products found matching your criteria."
            >
                <template #rows="{ items }">
                    <tr v-for="product in items" :key="product.id" class="hover:bg-surface-50/50 transition-colors">
                        <td class="py-4 px-6 font-bold text-surface-900">{{ product.name }}</td>
                        <td class="py-4 px-6 text-surface-500 font-mono text-xs">{{ product.sku }}</td>
                        <td class="py-4 px-6 text-surface-600 font-medium">{{ product.category ? product.category.name : 'Uncategorized' }}</td>
                        <td class="py-4 px-6 font-bold text-surface-900">{{ formatMoney(product.price) }}</td>
                        <td class="py-4 px-6">
                            <span :class="[
                                'font-bold',
                                product.stock_quantity <= 5 ? 'text-rose-600' : 'text-surface-700'
                            ]">
                                {{ product.stock_quantity }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <span :class="[
                                'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide',
                                product.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 
                                (product.status === 'out_of_stock' ? 'bg-rose-100 text-rose-700' : 'bg-surface-100 text-surface-600')
                            ]">
                                {{ product.status.replace('_', ' ') }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <Link :href="route('products.edit', product.id)" class="text-primary-600 hover:text-primary-800 p-1">
                                    <Edit2 class="w-4 h-4" />
                                </Link>
                                <button @click="deleteProduct(product.id)" class="text-rose-500 hover:text-rose-700 p-1">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </DataTable>
            
            <div v-if="products.links && products.links.length > 3" class="mt-6 flex items-center justify-center gap-1">
                <template v-for="(link, k) in products.links" :key="k">
                    <div v-if="link.url === null" class="px-3 py-1 text-sm text-surface-400 border border-surface-200 rounded-lg" v-html="link.label"></div>
                    <Link v-else :href="link.url" class="px-3 py-1 text-sm border rounded-lg transition-colors" :class="link.active ? 'bg-primary-600 text-white border-primary-600' : 'border-surface-200 text-surface-700 hover:bg-surface-50'" v-html="link.label"></Link>
                </template>
            </div>
        </Card>
    </AppLayout>
</template>
