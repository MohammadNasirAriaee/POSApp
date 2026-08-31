<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import Card from '../Components/Card.vue';
import { DollarSign, ShoppingBag, Package, Users, AlertTriangle } from 'lucide-vue-next';

defineProps({
    stats: Object,
    recentOrders: Array,
});

const formatMoney = (amount) => {
    return '$' + parseFloat(amount).toFixed(2);
};
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
            <Card>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <DollarSign class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-surface-500 uppercase tracking-wider">Today's Sales</p>
                        <h4 class="text-2xl font-black text-surface-900 mt-1">{{ formatMoney(stats.today_sales) }}</h4>
                    </div>
                </div>
            </Card>

            <Card>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <ShoppingBag class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-surface-500 uppercase tracking-wider">Today's Orders</p>
                        <h4 class="text-2xl font-black text-surface-900 mt-1">{{ stats.today_orders }}</h4>
                    </div>
                </div>
            </Card>

            <Card>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <Package class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-surface-500 uppercase tracking-wider">Total Products</p>
                        <h4 class="text-2xl font-black text-surface-900 mt-1">{{ stats.total_products }}</h4>
                    </div>
                </div>
            </Card>

            <Card>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <Users class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-surface-500 uppercase tracking-wider">Total Customers</p>
                        <h4 class="text-2xl font-black text-surface-900 mt-1">{{ stats.total_customers }}</h4>
                    </div>
                </div>
            </Card>

            <Card>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                        <AlertTriangle class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-surface-500 uppercase tracking-wider">Low Stock</p>
                        <h4 class="text-2xl font-black text-surface-900 mt-1">{{ stats.low_stock_products }}</h4>
                    </div>
                </div>
            </Card>
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
                                <p class="font-bold text-surface-900">{{ order.customer ? order.customer.name : 'Walk-in Customer' }}</p>
                                <p class="text-xs text-surface-500 mt-0.5">{{ new Date(order.created_at).toLocaleString() }} &middot; Cashier: {{ order.employee ? order.employee.name : 'Admin' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-primary-600 text-lg">{{ formatMoney(order.total) }}</p>
                            <span :class="[
                                'inline-flex mt-1 items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide',
                                order.status === 'completed' ? 'bg-emerald-100 text-emerald-700' :
                                order.status === 'cancelled' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700'
                            ]">{{ order.status }}</span>
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
