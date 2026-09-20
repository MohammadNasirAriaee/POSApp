<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import Card from '../Components/Card.vue';
import StatCard from '../Components/StatCard.vue';
import OrderStatusBadge from '../Components/OrderStatusBadge.vue';
import { DollarSign, ShoppingBag, Package, Users, AlertTriangle } from 'lucide-vue-next';
import { formatMoney } from '../Support/money';
import { customerName, cashierName } from '../Support/orderLabels';

defineProps({
    stats: Object,
    recentOrders: Array,
});

</script>

<template>
    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-surface-900">Dashboard</h1>
                <p class="text-sm text-surface-500 mt-1">Overview of your store's performance today.</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
            <StatCard
                label="Today's Sales"
                :value="formatMoney(stats.today_sales)"
                :icon="DollarSign"
                tone="bg-emerald-50 text-emerald-600"
            />
            <StatCard
                label="Today's Orders"
                :value="stats.today_orders"
                :icon="ShoppingBag"
                tone="bg-indigo-50 text-indigo-600"
                :href="route('orders.index')"
            />
            <StatCard
                label="Total Products"
                :value="stats.total_products"
                :icon="Package"
                tone="bg-amber-50 text-amber-600"
                :href="route('products.index')"
            />
            <StatCard
                label="Total Customers"
                :value="stats.total_customers"
                :icon="Users"
                tone="bg-blue-50 text-blue-600"
                :href="route('customers.index')"
            />
            <StatCard
                label="Low Stock"
                :value="stats.low_stock_products"
                :icon="AlertTriangle"
                tone="bg-rose-50 text-rose-600"
                :href="route('inventory-alerts.index')"
            />
        </div>

        <!-- Recent Orders -->
        <Card title="Recent Transactions" description="The latest orders processed through the POS.">
            <template v-if="recentOrders.length > 0">
                <div class="divide-y divide-surface-100 -mx-6 -my-6">
                    <div v-for="order in recentOrders" :key="order.id" class="px-6 py-4 flex items-center justify-between hover:bg-surface-50/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-surface-100 text-surface-600 flex items-center justify-center font-bold text-xs shrink-0">
                                #{{ String(order.id).padStart(5, '0') }}
                            </div>
                            <div>
                                <p class="font-bold text-surface-900">{{ customerName(order) }}</p>
                                <p class="text-xs text-surface-500 mt-0.5">{{ new Date(order.created_at).toLocaleString() }} &middot; Cashier: {{ cashierName(order) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-primary-600 text-lg">{{ formatMoney(order.total) }}</p>
                            <OrderStatusBadge :status="order.status" class="mt-1" />
                        </div>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="py-12 text-center text-surface-500">
                    No recent transactions found.
                </div>
            </template>
        </Card>
    </AppLayout>
</template>
