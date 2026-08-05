@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-8rem)] flex flex-col lg:flex-row gap-6 -mt-2">
    
    <!-- Left Panel: Product Selection -->
    <div class="flex-1 flex flex-col h-full overflow-hidden bg-white rounded-2xl border border-slate-200/80 shadow-xs">
        
        <!-- Search & Filter Header -->
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" id="productSearch" placeholder="Search products or scan barcode..." class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 py-2.5 text-sm font-medium text-slate-900 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" autocomplete="off" autofocus />
            </div>
            <select id="categoryFilter" class="w-full sm:w-48 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-medium text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <option value="all">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto p-4 bg-slate-50/50">
            <div id="productGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($products as $product)
                    <button type="button" 
                            class="product-card text-left bg-white p-4 rounded-2xl border border-slate-200 shadow-sm hover:border-indigo-500 hover:shadow-md hover:shadow-indigo-500/10 transition-all flex flex-col h-full {{ $product->stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                            data-id="{{ $product->id }}" 
                            data-name="{{ $product->name }}" 
                            data-price="{{ $product->price }}" 
                            data-sku="{{ $product->sku }}" 
                            data-category="{{ $product->category_id }}"
                            data-stock="{{ $product->stock_quantity }}"
                            {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                        
                        <div class="flex-1">
                            <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">{{ $product->category ? $product->category->name : 'General' }}</span>
                            <h3 class="font-bold text-slate-900 leading-tight">{{ $product->name }}</h3>
                            <p class="text-xs text-slate-500 mt-1 font-mono">{{ $product->sku }}</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-indigo-600 text-lg">${{ number_format($product->price, 2) }}</span>
                            <span class="text-xs font-semibold {{ $product->stock_quantity <= 5 ? 'text-rose-600' : 'text-slate-500' }}">Stock: {{ $product->stock_quantity }}</span>
                        </div>
                    </button>
                @endforeach
            </div>
            
            <div id="noProductsMsg" class="hidden h-full flex flex-col items-center justify-center text-slate-400 space-y-3">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                <p>No products found.</p>
            </div>
        </div>
    </div>

    <!-- Right Panel: Cart & Checkout -->
    <div class="w-full lg:w-[400px] xl:w-[450px] flex flex-col h-full bg-white rounded-2xl border border-slate-200/80 shadow-xs shrink-0 relative overflow-hidden">
        
        <!-- Cart Header -->
        <div class="p-4 border-b border-slate-100 bg-slate-900 text-white flex items-center justify-between">
            <h2 class="font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                Current Order
            </h2>
            <button type="button" id="clearCartBtn" class="text-xs font-semibold text-rose-300 hover:text-rose-200 transition-colors">Clear All</button>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-4 bg-slate-50/30" id="cartContainer">
            <!-- Empty State -->
            <div id="emptyCartState" class="h-full flex flex-col items-center justify-center text-slate-400 space-y-4">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <p class="font-medium">Cart is empty</p>
            </div>
            <!-- Populated via JS -->
            <ul id="cartList" class="space-y-3 hidden"></ul>
        </div>

        <!-- Checkout Form & Totals -->
        <div class="border-t border-slate-200 bg-white p-4 shrink-0 shadow-[0_-4px_10px_-4px_rgba(0,0,0,0.05)]">
            <form action="{{ route('pos.checkout') }}" method="POST" id="checkoutForm">
                @csrf
                <input type="hidden" name="cart" id="cartInput" value="[]">
                
                <div class="space-y-4 mb-4 pb-4 border-b border-slate-100">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Customer (Optional)</label>
                        <select name="customer_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="">Walk-in Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone ?? 'No Phone' }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tax Rate (%)</label>
                            <input type="number" name="tax_rate" id="taxRateInput" value="0" step="0.01" min="0" max="100" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-right font-mono" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Discount ($)</label>
                            <input type="number" name="discount" id="discountInput" value="0" step="0.01" min="0" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-right font-mono" />
                        </div>
                    </div>
                </div>

                <!-- Totals Calculation -->
                <div class="space-y-2 text-sm mb-5">
                    <div class="flex justify-between text-slate-500">
                        <span>Subtotal</span>
                        <span class="font-medium text-slate-900" id="subtotalDisplay">$0.00</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Tax</span>
                        <span class="font-medium text-slate-900" id="taxDisplay">$0.00</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Discount</span>
                        <span class="font-medium text-emerald-600" id="discountDisplay">-$0.00</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-100 pt-2 mt-2">
                        <span class="font-bold text-slate-900 text-base">Total</span>
                        <span class="font-bold text-indigo-600 text-2xl" id="totalDisplay">$0.00</span>
                    </div>
                </div>

                <!-- Payment & Submit -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="Cash" class="peer sr-only" checked />
                        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-center hover:bg-slate-50 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 font-semibold transition-all">
                            Cash
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="Card" class="peer sr-only" />
                        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-center hover:bg-slate-50 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 font-semibold transition-all">
                            Card
                        </div>
                    </label>
                </div>

                <button type="submit" id="checkoutBtn" disabled class="w-full rounded-xl bg-slate-900 px-6 py-4 text-sm font-bold text-white shadow-md hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Pay <span id="checkoutTotalDisplay">$0.00</span>
                </button>
            </form>
        </div>

    </div>
</div>

<!-- POS JavaScript Logic -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let cart = [];
        
        const productCards = document.querySelectorAll('.product-card');
        const searchInput = document.getElementById('productSearch');
        const categoryFilter = document.getElementById('categoryFilter');
        const noProductsMsg = document.getElementById('noProductsMsg');
        
        const emptyCartState = document.getElementById('emptyCartState');
        const cartList = document.getElementById('cartList');
        const cartInput = document.getElementById('cartInput');
        
        const taxRateInput = document.getElementById('taxRateInput');
        const discountInput = document.getElementById('discountInput');
        
        const subtotalDisplay = document.getElementById('subtotalDisplay');
        const taxDisplay = document.getElementById('taxDisplay');
        const discountDisplay = document.getElementById('discountDisplay');
        const totalDisplay = document.getElementById('totalDisplay');
        const checkoutTotalDisplay = document.getElementById('checkoutTotalDisplay');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const clearCartBtn = document.getElementById('clearCartBtn');

        // Format Currency Helper
        const formatMoney = (amount) => {
            return '$' + parseFloat(amount).toFixed(2);
        };

        // Update UI Calculation
        const updateCart = () => {
            let subtotal = 0;
            cartList.innerHTML = '';

            if (cart.length === 0) {
                emptyCartState.classList.remove('hidden');
                cartList.classList.add('hidden');
                checkoutBtn.disabled = true;
            } else {
                emptyCartState.classList.add('hidden');
                cartList.classList.remove('hidden');
                checkoutBtn.disabled = false;

                cart.forEach((item, index) => {
                    subtotal += item.price * item.quantity;
                    
                    const li = document.createElement('li');
                    li.className = 'bg-white border border-slate-200/60 rounded-xl p-3 shadow-[0_2px_4px_-2px_rgba(0,0,0,0.05)] flex flex-col gap-2';
                    li.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-bold text-sm text-slate-900">${item.name}</h4>
                                <p class="text-xs text-indigo-600 font-semibold">${formatMoney(item.price)}</p>
                            </div>
                            <button type="button" class="text-rose-400 hover:text-rose-600 p-1 remove-item" data-index="${index}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-50 pt-2">
                            <div class="flex items-center gap-3 bg-slate-50 rounded-lg p-1 border border-slate-200/60">
                                <button type="button" class="w-6 h-6 rounded-md bg-white text-slate-600 shadow-sm border border-slate-200 flex items-center justify-center hover:bg-slate-100 qty-minus" data-index="${index}">-</button>
                                <span class="text-sm font-bold w-6 text-center">${item.quantity}</span>
                                <button type="button" class="w-6 h-6 rounded-md bg-white text-slate-600 shadow-sm border border-slate-200 flex items-center justify-center hover:bg-slate-100 qty-plus" data-index="${index}">+</button>
                            </div>
                            <div class="font-bold text-sm text-slate-900">${formatMoney(item.price * item.quantity)}</div>
                        </div>
                    `;
                    cartList.appendChild(li);
                });
            }

            // Calculations
            const taxRate = parseFloat(taxRateInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            
            const tax = subtotal * (taxRate / 100);
            const total = Math.max(0, subtotal + tax - discount);

            // Displays
            subtotalDisplay.innerText = formatMoney(subtotal);
            taxDisplay.innerText = formatMoney(tax);
            discountDisplay.innerText = '-' + formatMoney(discount);
            totalDisplay.innerText = formatMoney(total);
            checkoutTotalDisplay.innerText = formatMoney(total);

            // Update hidden input for backend
            cartInput.value = JSON.stringify(cart);

            // Re-attach event listeners for dynamic buttons
            attachCartListeners();
        };

        const attachCartListeners = () => {
            document.querySelectorAll('.remove-item').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const idx = e.currentTarget.dataset.index;
                    cart.splice(idx, 1);
                    updateCart();
                });
            });

            document.querySelectorAll('.qty-minus').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const idx = e.currentTarget.dataset.index;
                    if (cart[idx].quantity > 1) {
                        cart[idx].quantity--;
                    } else {
                        cart.splice(idx, 1); // remove if 0
                    }
                    updateCart();
                });
            });

            document.querySelectorAll('.qty-plus').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const idx = e.currentTarget.dataset.index;
                    // Simple stock check client-side
                    if(cart[idx].quantity < cart[idx].maxStock) {
                         cart[idx].quantity++;
                         updateCart();
                    } else {
                        alert("Cannot add more. Max stock reached.");
                    }
                });
            });
        };

        // Add to cart click
        productCards.forEach(card => {
            card.addEventListener('click', () => {
                const id = card.dataset.id;
                const name = card.dataset.name;
                const price = parseFloat(card.dataset.price);
                const maxStock = parseInt(card.dataset.stock);

                const existingItem = cart.find(item => item.id === id);

                if (existingItem) {
                    if(existingItem.quantity < maxStock) {
                        existingItem.quantity++;
                    } else {
                        alert("Cannot add more. Max stock reached.");
                    }
                } else {
                    cart.push({ id, name, price, quantity: 1, maxStock });
                }

                updateCart();
            });
        });

        // Clear Cart
        clearCartBtn.addEventListener('click', () => {
            if(confirm("Are you sure you want to clear the order?")) {
                cart = [];
                updateCart();
            }
        });

        // Calculation Inputs Changes
        taxRateInput.addEventListener('input', updateCart);
        discountInput.addEventListener('input', updateCart);

        // Filtering & Searching
        const filterProducts = () => {
            const searchTerm = searchInput.value.toLowerCase();
            const categoryId = categoryFilter.value;
            let visibleCount = 0;

            productCards.forEach(card => {
                const name = card.dataset.name.toLowerCase();
                const sku = card.dataset.sku.toLowerCase();
                const cat = card.dataset.category;

                const matchesSearch = name.includes(searchTerm) || sku.includes(searchTerm);
                const matchesCategory = categoryId === 'all' || cat === categoryId;

                if (matchesSearch && matchesCategory) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                noProductsMsg.classList.remove('hidden');
            } else {
                noProductsMsg.classList.add('hidden');
            }
        };

        searchInput.addEventListener('input', filterProducts);
        categoryFilter.addEventListener('change', filterProducts);
        
        // Prevent form submission on enter in search
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') e.preventDefault();
        });

        // Initialize empty cart
        updateCart();
    });
</script>
@endsection
