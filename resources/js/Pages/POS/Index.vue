<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import Card from '../../Components/Card.vue';
import { useForm, router, Link } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { ShoppingCart, Search, Plus, Minus, Trash2, CreditCard, Loader2 } from 'lucide-vue-next';

const props = defineProps({
    products: Array,
    categories: Array,
    customers: Array,
});

// Cart State
const cart = ref([]);
const searchQuery = ref('');
const activeCategory = ref('');
const selectedCustomer = ref('');
const tenderedAmount = ref('');
const showCheckoutModal = ref(false);
const processingCheckout = ref(false);

// Filter products based on search and category
const filteredProducts = computed(() => {
    return props.products.filter(product => {
        const matchesSearch = product.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                              product.sku.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesCategory = activeCategory.value === '' || product.category_id === activeCategory.value;
        return matchesSearch && matchesCategory;
    });
});

// Cart calculations
const cartSubtotal = computed(() => {
    return cart.value.reduce((total, item) => total + (item.price * item.quantity), 0);
});
const cartTax = computed(() => cartSubtotal.value * 0.1); // 10% tax for example
const cartTotal = computed(() => cartSubtotal.value + cartTax.value);
const cartItemCount = computed(() => cart.value.reduce((count, item) => count + item.quantity, 0));

// Cart Actions
const addToCart = (product) => {
    if (product.stock_quantity <= 0) return alert('Out of stock');
    
    const existingItem = cart.value.find(item => item.id === product.id);
    if (existingItem) {
        if (existingItem.quantity < product.stock_quantity) {
            existingItem.quantity++;
        } else {
            alert('Not enough stock');
        }
    } else {
        cart.value.push({ ...product, quantity: 1 });
    }
};

const updateQuantity = (item, change) => {
    const newQty = item.quantity + change;
    if (newQty <= 0) {
        removeFromCart(item.id);
    } else if (newQty <= item.stock_quantity) {
        item.quantity = newQty;
    } else {
        alert('Not enough stock');
    }
};

const removeFromCart = (id) => {
    cart.value = cart.value.filter(item => item.id !== id);
};

const clearCart = () => {
    if (confirm('Clear entire cart?')) {
        cart.value = [];
        selectedCustomer.value = '';
        tenderedAmount.value = '';
    }
};

// Checkout
const checkoutForm = useForm({
    cart: [],
    customer_id: '',
    payment_method: 'cash',
    tax_rate: 10,
    discount: 0,
});

const processCheckout = () => {
    if (cart.value.length === 0) return;
    
    const tendered = parseFloat(tenderedAmount.value) || 0;
    if (tendered < cartTotal.value) {
        alert('Tendered amount must be greater than or equal to total amount.');
        return;
    }

    processingCheckout.value = true;

    checkoutForm.cart = cart.value.map(i => ({ id: i.id, quantity: i.quantity, price: i.price }));
    checkoutForm.customer_id = selectedCustomer.value;
    checkoutForm.payment_method = 'cash';
    checkoutForm.tax_rate = 10;
    checkoutForm.discount = 0;

    checkoutForm.post(route('pos.checkout'), {
        preserveScroll: true,
        onSuccess: () => {
            cart.value = [];
            selectedCustomer.value = '';
            tenderedAmount.value = '';
            showCheckoutModal.value = false;
            processingCheckout.value = false;
        },
        onError: () => {
            processingCheckout.value = false;
        }
    });
};

const formatMoney = (amount) => {
    return '$' + parseFloat(amount).toFixed(2);
};
</script>

<template>
    <AppLayout>
        <div class="h-[calc(100vh-8rem)] flex flex-col lg:flex-row gap-6">
            
            <!-- Left Side: Products Grid -->
            <div class="flex-1 flex flex-col min-h-0 bg-surface-50">
                <!-- Filters Header -->
                <div class="mb-4 flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <Search class="w-4 h-4 text-surface-400" />
                        </div>
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            class="metronic-input pl-10" 
                            placeholder="Search products by name or SKU..."
                        >
                    </div>
                    <select v-model="activeCategory" class="metronic-input w-full sm:w-48">
                        <option value="">All Categories</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>

                <!-- Products Grid -->
                <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar pb-4">
                    <div v-if="filteredProducts.length === 0" class="h-full flex flex-col items-center justify-center text-surface-500 bg-white rounded-xl border border-dashed border-surface-300">
                        <Search class="w-8 h-8 mb-2 opacity-50" />
                        <p>No products found matching your search.</p>
                    </div>
                    
                    <div v-else class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                        <button 
                            v-for="product in filteredProducts" 
                            :key="product.id"
                            @click="addToCart(product)"
                            :disabled="product.stock_quantity <= 0"
                            class="group relative bg-white p-4 rounded-xl border border-surface-200 shadow-sm text-left transition-all hover:border-primary-500 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-60 disabled:hover:border-surface-200 disabled:hover:shadow-sm"
                        >
                            <div class="absolute top-2 right-2">
                                <span v-if="product.stock_quantity <= 0" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-rose-100 text-rose-700">Out</span>
                                <span v-else class="text-xs font-semibold text-surface-400">{{ product.stock_quantity }} left</span>
                            </div>
                            
                            <div class="h-12 w-12 rounded-lg bg-surface-100 text-surface-400 flex items-center justify-center mb-3 group-hover:bg-primary-50 group-hover:text-primary-600 transition-colors">
                                <span class="font-bold text-lg">{{ product.name.substring(0, 2).toUpperCase() }}</span>
                            </div>
                            
                            <h3 class="font-bold text-surface-900 leading-tight mb-1 truncate">{{ product.name }}</h3>
                            <p class="text-sm font-black text-primary-600">{{ formatMoney(product.price) }}</p>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Side: Cart -->
            <div class="w-full lg:w-96 shrink-0 flex flex-col metronic-card h-full max-h-full bg-white">
                <!-- Cart Header -->
                <div class="p-4 border-b border-surface-200 bg-surface-50/50 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2">
                        <ShoppingCart class="w-5 h-5 text-primary-600" />
                        <h2 class="font-bold text-surface-900">Current Order</h2>
                    </div>
                    <span class="bg-surface-800 text-white text-xs font-bold px-2 py-1 rounded-md">{{ cartItemCount }} items</span>
                </div>

                <!-- Customer Selection -->
                <div class="p-4 border-b border-surface-200 shrink-0">
                    <select v-model="selectedCustomer" class="metronic-input text-sm">
                        <option value="">Walk-in Customer</option>
                        <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                            {{ customer.name }}
                        </option>
                    </select>
                </div>

                <!-- Cart Items List -->
                <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                    <div v-if="cart.length === 0" class="h-full flex flex-col items-center justify-center text-surface-400">
                        <ShoppingCart class="w-12 h-12 mb-3 opacity-20" />
                        <p class="text-sm">Cart is empty</p>
                        <p class="text-xs mt-1">Select products to add them to the order</p>
                    </div>

                    <div v-else class="space-y-3">
                        <div v-for="item in cart" :key="item.id" class="flex gap-3 items-center group">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-surface-900 text-sm truncate">{{ item.name }}</h4>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-primary-600 font-bold text-sm">{{ formatMoney(item.price) }}</span>
                                    <span class="text-surface-500 text-xs font-semibold">{{ formatMoney(item.price * item.quantity) }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-1 bg-surface-100 rounded-lg p-1 shrink-0">
                                <button @click="updateQuantity(item, -1)" class="w-6 h-6 flex items-center justify-center rounded bg-white text-surface-600 hover:text-primary-600 hover:shadow-sm transition-all shadow-[0_1px_2px_rgba(0,0,0,0.05)]">
                                    <Minus class="w-3 h-3" />
                                </button>
                                <span class="w-6 text-center text-sm font-bold text-surface-900">{{ item.quantity }}</span>
                                <button @click="updateQuantity(item, 1)" class="w-6 h-6 flex items-center justify-center rounded bg-white text-surface-600 hover:text-primary-600 hover:shadow-sm transition-all shadow-[0_1px_2px_rgba(0,0,0,0.05)]">
                                    <Plus class="w-3 h-3" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart Footer (Totals & Checkout) -->
                <div class="p-4 border-t border-surface-200 bg-surface-50/50 shrink-0 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-surface-500 font-medium">Subtotal</span>
                        <span class="text-surface-900 font-bold">{{ formatMoney(cartSubtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-surface-500 font-medium">Tax (10%)</span>
                        <span class="text-surface-900 font-bold">{{ formatMoney(cartTax) }}</span>
                    </div>
                    <div class="pt-3 border-t border-surface-200 flex justify-between items-center">
                        <span class="text-surface-900 font-black text-lg">Total</span>
                        <span class="text-primary-600 font-black text-2xl tracking-tight">{{ formatMoney(cartTotal) }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <button 
                            @click="clearCart" 
                            :disabled="cart.length === 0"
                            class="metronic-btn bg-surface-200 text-surface-700 hover:bg-surface-300 disabled:opacity-50"
                        >
                            <Trash2 class="w-4 h-4" /> Clear
                        </button>
                        <button 
                            @click="showCheckoutModal = true"
                            :disabled="cart.length === 0"
                            class="metronic-btn metronic-btn-primary disabled:opacity-50"
                        >
                            <CreditCard class="w-4 h-4" /> Pay
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Modal Overlay -->
        <div v-if="showCheckoutModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-surface-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <div class="p-6 border-b border-surface-200">
                    <h3 class="text-xl font-bold text-surface-900">Complete Payment</h3>
                    <p class="text-sm text-surface-500 mt-1">Enter tendered amount to process checkout.</p>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="bg-primary-50 rounded-xl p-4 flex items-center justify-between">
                        <span class="font-bold text-primary-800">Total Due</span>
                        <span class="font-black text-2xl text-primary-700">{{ formatMoney(cartTotal) }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-surface-700 mb-2">Tendered Amount ($)</label>
                        <input 
                            v-model="tenderedAmount" 
                            type="number" 
                            step="0.01" 
                            class="metronic-input text-lg font-bold" 
                            placeholder="0.00"
                            autofocus
                        >
                        <div v-if="tenderedAmount && parseFloat(tenderedAmount) >= cartTotal" class="mt-2 text-emerald-600 font-semibold text-sm flex justify-between">
                            <span>Change Due:</span>
                            <span>{{ formatMoney(parseFloat(tenderedAmount) - cartTotal) }}</span>
                        </div>
                        <div v-else-if="tenderedAmount && parseFloat(tenderedAmount) < cartTotal" class="mt-2 text-rose-600 font-semibold text-sm">
                            Amount must cover the total due.
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-surface-200 bg-surface-50 flex gap-3">
                    <button @click="showCheckoutModal = false" class="metronic-btn metronic-btn-light flex-1">Cancel</button>
                    <button 
                        @click="processCheckout" 
                        :disabled="!tenderedAmount || parseFloat(tenderedAmount) < cartTotal || processingCheckout"
                        class="metronic-btn metronic-btn-primary flex-1 disabled:opacity-50"
                    >
                        <Loader2 v-if="processingCheckout" class="w-4 h-4 animate-spin" />
                        <span v-else>Confirm Payment</span>
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Custom scrollbar for POS areas to look cleaner */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 10px;
}
.custom-scrollbar:hover::-webkit-scrollbar-thumb {
    background-color: #94a3b8;
}
</style>
