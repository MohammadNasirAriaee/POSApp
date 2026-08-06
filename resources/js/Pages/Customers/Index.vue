<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import Card from '../../Components/Card.vue';
import DataTable from '../../Components/DataTable.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { Plus, Edit2, Trash2, Search, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
    customers: Object,
    search: String,
});

const form = useForm({});
const searchQuery = ref(props.search || '');

let searchTimeout = null;
watch(searchQuery, (value) => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('customers.index'), { search: value }, { preserveState: true, replace: true });
    }, 300);
});

const clearSearch = () => {
    searchQuery.value = '';
    router.get(route('customers.index'));
};

const deleteCustomer = (id) => {
    if (confirm('Are you sure you want to delete this customer?')) {
        form.delete(route('customers.destroy', id));
    }
};
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-surface-900">Customers</h1>
                <p class="text-sm text-surface-500 mt-1">Manage your customer database and contact info.</p>
            </div>
            <Link :href="route('customers.create')" class="metronic-btn metronic-btn-primary shrink-0">
                <Plus class="w-4 h-4" /> Add Customer
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
                        placeholder="Search by name, email, or phone..." 
                        class="w-full rounded-lg border border-surface-200 bg-white pl-9 pr-8 py-2 text-sm text-surface-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                    />
                    <button v-if="searchQuery" @click="clearSearch" class="absolute inset-y-0 right-0 flex items-center pr-3 text-surface-400 hover:text-surface-600">
                        <X class="w-4 h-4" />
                    </button>
                </div>
            </template>

            <DataTable 
                :headers="['Name', 'Contact Info', 'Joined Date', 'Actions']"
                :items="customers.data"
                emptyMessage="No customers found matching your criteria."
            >
                <template #rows="{ items }">
                    <tr v-for="customer in items" :key="customer.id" class="hover:bg-surface-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ (customer.first_name?.[0] || '') + (customer.last_name?.[0] || '') }}
                                </div>
                                <span class="font-bold text-surface-900">{{ customer.name }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-surface-600">
                            <div v-if="customer.email" class="text-sm">
                                <a :href="'mailto:' + customer.email" class="hover:text-primary-600">{{ customer.email }}</a>
                            </div>
                            <div v-if="customer.phone" class="text-sm mt-0.5">
                                <a :href="'tel:' + customer.phone" class="hover:text-primary-600">{{ customer.phone }}</a>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-surface-500">
                            {{ new Date(customer.created_at).toLocaleDateString() }}
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <Link :href="route('customers.edit', customer.id)" class="text-primary-600 hover:text-primary-800 p-1">
                                    <Edit2 class="w-4 h-4" />
                                </Link>
                                <button @click="deleteCustomer(customer.id)" class="text-rose-500 hover:text-rose-700 p-1">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </DataTable>
            
            <div v-if="customers.links && customers.links.length > 3" class="mt-6 flex items-center justify-center gap-1">
                <template v-for="(link, k) in customers.links" :key="k">
                    <div v-if="link.url === null" class="px-3 py-1 text-sm text-surface-400 border border-surface-200 rounded-lg" v-html="link.label"></div>
                    <Link v-else :href="link.url" class="px-3 py-1 text-sm border rounded-lg transition-colors" :class="link.active ? 'bg-primary-600 text-white border-primary-600' : 'border-surface-200 text-surface-700 hover:bg-surface-50'" v-html="link.label"></Link>
                </template>
            </div>
        </Card>
    </AppLayout>
</template>
