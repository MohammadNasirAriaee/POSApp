<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import Card from '../../Components/Card.vue';
import DataTable from '../../Components/DataTable.vue';
import Pagination from "../../Components/Pagination.vue";
import OrderStatusBadge from '../../Components/OrderStatusBadge.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { Eye, Ban } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { formatMoney } from '../../Support/money';
import { customerName, cashierName } from '../../Support/orderLabels';

const props = defineProps({
    orders: Object,
    status: String,
    statuses: {
        type: Array,
        default: () => [],
    },
    customerFilter: Object,
});

const form = useForm({});
const statusFilter = ref(props.status || '');

const statusLabel = (value) =>
    value.replace(/_/g, ' ').replace(/\w/g, (c) => c.toUpperCase());

watch(statusFilter, (value) => {
    router.get(
        route('orders.index'),
        {
            ...(value ? { status: value } : {}),
            ...(props.customerFilter ? { customer_id: props.customerFilter.id } : {}),
        },
        { preserveState: true, replace: true },
    );
});

// The server cancels the order and returns its items to stock; nothing is deleted.
const cancelOrder = (id) => {
    if (confirm('Cancel this order? Its items will be returned to stock.')) {
        form.delete(route('orders.cancel', id));
    }
};

</script>

<template>
    <Head title="Orders" />
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

        <div
            v-if="customerFilter"
            class="mb-6 flex items-center justify-between gap-3 rounded-xl border border-primary-200 bg-primary-50 px-4 py-3 text-sm"
        >
            <span class="text-primary-800">
                Showing orders for
                <span class="font-bold">{{ customerFilter.name }}</span>
            </span>
            <Link
                :href="route('orders.index', statusFilter ? { status: statusFilter } : {})"
                class="font-semibold text-primary-700 hover:text-primary-900 hover:underline"
            >
                Clear
            </Link>
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
                            {{ customerName(order) }}
                        </td>
                        <td class="py-4 px-6 text-surface-600">
                            {{ cashierName(order) }}
                        </td>
                        <td class="py-4 px-6 font-bold text-primary-600">
                            {{ formatMoney(order.total) }}
                        </td>
                        <td class="py-4 px-6">
                            <OrderStatusBadge :status="order.status" />
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <Link :href="route('orders.show', order.id)" class="text-surface-600 hover:text-primary-600 p-1" title="View Receipt" aria-label="View receipt">
                                    <Eye class="w-4 h-4" />
                                </Link>
                                <button
                                    v-if="order.status !== 'cancelled'"
                                    @click="cancelOrder(order.id)"
                                    :disabled="form.processing"
                                    class="text-rose-500 hover:text-rose-700 p-1 disabled:opacity-50"
                                    title="Cancel Order"
                                >
                                    <span class="sr-only">Cancel order</span>
                                    <Ban class="w-4 h-4" />
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
