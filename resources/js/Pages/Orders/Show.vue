<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import Card from '../../Components/Card.vue';
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, Printer } from 'lucide-vue-next';

defineProps({
    order: Object,
});

const formatMoney = (amount) => {
    return '$' + parseFloat(amount).toFixed(2);
};
</script>

<template>
    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link :href="route('orders.index')" class="p-2 rounded-lg hover:bg-surface-200 text-surface-500 hover:text-surface-900 transition-colors">
                    <ArrowLeft class="w-5 h-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-surface-900">Order #{{ String(order.id).padStart(5, '0') }}</h1>
                    <p class="text-sm text-surface-500 mt-1">{{ new Date(order.created_at).toLocaleString() }}</p>
                </div>
            </div>
            <button onclick="window.print()" class="metronic-btn metronic-btn-primary shrink-0 hidden sm:flex">
                <Printer class="w-4 h-4" /> Print Receipt
            </button>
        </div>

        <div class="max-w-3xl print:max-w-none">
            <Card class="print:border-none print:shadow-none">
                <!-- Receipt Header -->
                <div class="text-center pb-6 border-b border-surface-200 mb-6">
                    <h2 class="text-2xl font-black text-surface-900 mb-2">✦ POS System</h2>
                    <p class="text-sm text-surface-500">123 Business Road, City, State 12345</p>
                    <p class="text-sm text-surface-500">Phone: (555) 123-4567</p>
                </div>

                <!-- Order Info -->
                <div class="grid grid-cols-2 gap-6 mb-8 text-sm">
                    <div>
                        <p class="text-surface-500 font-semibold mb-1">Billed To:</p>
                        <template v-if="order.customer">
                            <p class="font-bold text-surface-900">{{ order.customer?.name || 'Walk-in customer' }}</p>
                            <p v-if="order.customer?.email" class="text-surface-600">{{ order.customer.email }}</p>
                            <p v-if="order.customer?.phone" class="text-surface-600">{{ order.customer.phone }}</p>
                        </template>
                        <p v-else class="font-bold text-surface-900">Walk-in Customer</p>
                    </div>
                    <div class="text-right">
                        <p class="text-surface-500 font-semibold mb-1">Order Details:</p>
                        <p><span class="text-surface-500">Receipt:</span> <span class="font-mono text-surface-900 font-bold">#{{ String(order.id).padStart(5, '0') }}</span></p>
                        <p><span class="text-surface-500">Cashier:</span> <span class="text-surface-900 font-bold">{{ order.employee ? order.employee.name : 'Admin' }}</span></p>
                        <p><span class="text-surface-500">Status:</span> 
                            <span :class="[
                                'uppercase text-[10px] font-bold ml-1',
                                order.status === 'completed' ? 'text-emerald-600' :
                                order.status === 'cancelled' ? 'text-rose-600' : 'text-amber-600'
                            ]">{{ order.status }}</span>
                        </p>
                    </div>
                </div>

                <!-- Order Items -->
                <table class="w-full text-sm mb-8">
                    <thead>
                        <tr class="border-b-2 border-surface-200">
                            <th class="text-left py-2 text-surface-500 font-bold uppercase text-xs">Item</th>
                            <th class="text-center py-2 text-surface-500 font-bold uppercase text-xs">Qty</th>
                            <th class="text-right py-2 text-surface-500 font-bold uppercase text-xs">Price</th>
                            <th class="text-right py-2 text-surface-500 font-bold uppercase text-xs">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        <tr v-for="item in order.items" :key="item.id">
                            <td class="py-3 font-semibold text-surface-900">{{ item.name }}</td>
                            <td class="py-3 text-center text-surface-600">{{ item.quantity }}</td>
                            <td class="py-3 text-right text-surface-600">{{ formatMoney(item.price) }}</td>
                            <td class="py-3 text-right font-bold text-surface-900">{{ formatMoney(item.subtotal) }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Totals -->
                <div class="flex justify-end">
                    <div class="w-full sm:w-1/2">
                        <div class="flex justify-between py-2 text-sm border-t border-surface-200">
                            <span class="text-surface-600">Subtotal</span>
                            <span class="font-semibold">{{ formatMoney(order.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-sm">
                            <span class="text-surface-600">Tax</span>
                            <span class="font-semibold">{{ formatMoney(order.tax) }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-sm border-b border-surface-200">
                            <span class="text-surface-600">Discount</span>
                            <span class="font-semibold text-rose-600">-{{ formatMoney(order.discount) }}</span>
                        </div>
                        <div class="flex justify-between py-3 text-lg font-black text-surface-900 border-b-2 border-surface-200">
                            <span>Total</span>
                            <span class="text-primary-600">{{ formatMoney(order.total) }}</span>
                        </div>
                        <div class="flex justify-between py-3 text-sm font-bold text-surface-600">
                            <span>Amount Tendered</span>
                            <span>{{ formatMoney(order.tendered) }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-sm font-bold text-surface-600">
                            <span>Change Due</span>
                            <span>{{ formatMoney(order.change) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-12 text-sm text-surface-500 print:mb-0 mb-4">
                    <p class="font-semibold text-surface-700">Thank you for your business!</p>
                    <p class="mt-1">Please keep this receipt for your records.</p>
                </div>
            </Card>

            <div class="mt-6 sm:hidden">
                <button onclick="window.print()" class="metronic-btn metronic-btn-primary w-full">
                    <Printer class="w-4 h-4" /> Print Receipt
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<style>
@media print {
    body { background-color: white; }
    aside, header, .metronic-btn { display: none !important; }
    main { padding: 0 !important; margin: 0 !important; height: auto !important; overflow: visible !important; }
    .metronic-card { border: none !important; box-shadow: none !important; padding: 0 !important; }
}
</style>
