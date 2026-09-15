<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import Card from '../../Components/Card.vue';
import DataTable from '../../Components/DataTable.vue';
import Pagination from "../../Components/Pagination.vue";
import { Link, useForm, router } from '@inertiajs/vue3';
import { Eye, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
    orders: Object,
    status: String,
    statuses: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({});
const statusFilter = ref(props.status || '');

const statusLabel = (value) =>
    value.replace(/_/g, ' ').replace(/\w/g, (c) => c.toUpperCase());

watch(statusFilter, (value) => {
    router.get(
        route('orders.index'),
        value ? { status: value } : {},
        { preserveState: true, replace: true },
    );
});

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
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-surface-900">Orders</h1>
                <p class="text-sm text-surface-500 mt-1">View and manage past sales transactions.</p>
            </div>
            <select
                v-model="statusFilter"
                aria-label="Filter orders by status"
                class="metronic-input w-full sm:w-48 shrink-0"
            >
                <option value="">All Statuses</option>
                <option v-for="value in statuses" :key="value" :value="value">
                    {{ statusLabel(value) }}
                </option>
            </select>
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
            
            <Pagination :links="orders.links" />
        </Card>
    </AppLayout>
</template>
