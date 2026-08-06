<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import Card from '../../Components/Card.vue';
import DataTable from '../../Components/DataTable.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { Eye, Trash2 } from 'lucide-vue-next';

defineProps({
    orders: Object,
});

const form = useForm({});

const deleteOrder = (id) => {
    if (confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
        form.delete(route('orders.destroy', id));
    }
};

const formatMoney = (amount) => {
    return '$' + parseFloat(amount).toFixed(2);
};
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-surface-900">Orders</h1>
                <p class="text-sm text-surface-500 mt-1">View and manage past sales transactions.</p>
            </div>
        </div>

        <Card>
            <DataTable 
                :headers="['Order ID', 'Date', 'Customer', 'Cashier', 'Total', 'Status', 'Actions']"
                :items="orders.data"
                emptyMessage="No orders have been placed yet."
            >
                <template #rows="{ items }">
                    <tr v-for="order in items" :key="order.id" class="hover:bg-surface-50/50 transition-colors">
                        <td class="py-4 px-6 font-bold text-surface-900 font-mono">
                            #{{ String(order.id).padStart(5, '0') }}
                        </td>
                        <td class="py-4 px-6 text-surface-600">
                            {{ new Date(order.created_at).toLocaleString() }}
                        </td>
                        <td class="py-4 px-6 font-medium text-surface-900">
                            {{ order.customer ? order.customer.name : 'Walk-in Customer' }}
                        </td>
                        <td class="py-4 px-6 text-surface-600">
                            {{ order.employee ? order.employee.name : 'Admin' }}
                        </td>
                        <td class="py-4 px-6 font-bold text-primary-600">
                            {{ formatMoney(order.total) }}
                        </td>
                        <td class="py-4 px-6">
                            <span :class="[
                                'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide',
                                order.status === 'completed' ? 'bg-emerald-100 text-emerald-700' :
                                order.status === 'cancelled' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700'
                            ]">
                                {{ order.status }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <Link :href="route('orders.show', order.id)" class="text-surface-600 hover:text-primary-600 p-1" title="View Receipt">
                                    <Eye class="w-4 h-4" />
                                </Link>
                                <button @click="deleteOrder(order.id)" class="text-rose-500 hover:text-rose-700 p-1" title="Delete Order">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </DataTable>
            
            <div v-if="orders.links && orders.links.length > 3" class="mt-6 flex items-center justify-center gap-1">
                <template v-for="(link, k) in orders.links" :key="k">
                    <div v-if="link.url === null" class="px-3 py-1 text-sm text-surface-400 border border-surface-200 rounded-lg" v-html="link.label"></div>
                    <Link v-else :href="link.url" class="px-3 py-1 text-sm border rounded-lg transition-colors" :class="link.active ? 'bg-primary-600 text-white border-primary-600' : 'border-surface-200 text-surface-700 hover:bg-surface-50'" v-html="link.label"></Link>
                </template>
            </div>
        </Card>
    </AppLayout>
</template>
