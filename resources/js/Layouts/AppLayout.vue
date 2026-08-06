<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { 
    LayoutDashboard, 
    Tags, 
    Package, 
    Users, 
    ClipboardList, 
    Calculator,
    Menu,
    X,
    Bell,
    User
} from 'lucide-vue-next';
import { ref } from 'vue';

const page = usePage();
const appName = computed(() => page.props.config?.appName || 'POS System');

const sidebarOpen = ref(false);

const navigation = [
    { name: 'Dashboard', route: 'dashboard', icon: LayoutDashboard },
    { name: 'POS Terminal', route: 'pos.index', icon: Calculator },
    { name: 'Orders', route: 'orders.index', icon: ClipboardList },
    { name: 'Products', route: 'products.index', icon: Package },
    { name: 'Categories', route: 'categories.index', icon: Tags },
    { name: 'Customers', route: 'customers.index', icon: Users },
];
</script>

<template>
    <div class="min-h-screen bg-surface-50 flex flex-col md:flex-row font-sans text-surface-800">
        
        <!-- Mobile Header -->
        <div class="md:hidden bg-surface-900 text-white p-4 flex items-center justify-between z-20">
            <span class="font-bold text-lg tracking-tight">{{ appName }}</span>
            <button @click="sidebarOpen = !sidebarOpen" class="text-surface-400 hover:text-white transition-colors">
                <Menu v-if="!sidebarOpen" class="w-6 h-6" />
                <X v-else class="w-6 h-6" />
            </button>
        </div>

        <!-- Sidebar -->
        <aside :class="[
                'fixed inset-y-0 left-0 z-10 w-64 bg-surface-900 text-surface-300 transition-transform duration-300 ease-in-out md:translate-x-0 md:static flex flex-col',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]">
            <div class="p-6 hidden md:block">
                <span class="font-black text-2xl tracking-tight text-white flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-primary-600 flex items-center justify-center text-sm">✦</span>
                    {{ appName }}
                </span>
            </div>

            <nav class="flex-1 px-4 py-6 md:py-2 space-y-1.5 overflow-y-auto">
                <Link 
                    v-for="item in navigation" 
                    :key="item.name" 
                    :href="route(item.route)"
                    :class="[
                        'flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200',
                        route().current(item.route) 
                            ? 'bg-primary-600/10 text-primary-500' 
                            : 'hover:bg-surface-800 hover:text-white'
                    ]"
                >
                    <component :is="item.icon" class="w-5 h-5" :class="route().current(item.route) ? 'text-primary-500' : 'text-surface-500'" />
                    {{ item.name }}
                </Link>
            </nav>

            <div class="p-4 border-t border-surface-800">
                <div class="flex items-center gap-3 px-4 py-3 bg-surface-800/50 rounded-xl">
                    <div class="w-8 h-8 rounded-full bg-surface-700 flex items-center justify-center">
                        <User class="w-4 h-4 text-surface-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">Admin User</p>
                        <p class="text-xs text-surface-500 truncate">admin@posapp.test</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden h-screen">
            
            <!-- Topbar -->
            <header class="bg-white border-b border-surface-200 h-16 flex items-center justify-between px-6 shrink-0 z-10 hidden md:flex">
                <div class="flex items-center gap-4">
                    <!-- Global Search Placeholder -->
                    <div class="relative">
                        <input type="text" placeholder="Search..." class="bg-surface-50 border border-surface-200 rounded-lg pl-4 pr-10 py-2 text-sm w-64 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 transition-all" />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-surface-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button class="relative p-2 text-surface-400 hover:text-primary-600 transition-colors bg-surface-50 rounded-lg hover:bg-primary-50">
                        <Bell class="w-5 h-5" />
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 border-2 border-white"></span>
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-auto p-4 md:p-8">
                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success" class="mb-6 bg-emerald-50 text-emerald-800 p-4 rounded-xl border border-emerald-200/60 shadow-sm flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h4 class="font-bold text-sm">Success</h4>
                        <p class="text-sm mt-1 opacity-90">{{ $page.props.flash.success }}</p>
                    </div>
                </div>
                <div v-if="$page.props.flash?.error" class="mb-6 bg-rose-50 text-rose-800 p-4 rounded-xl border border-rose-200/60 shadow-sm flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h4 class="font-bold text-sm">Error</h4>
                        <p class="text-sm mt-1 opacity-90">{{ $page.props.flash.error }}</p>
                    </div>
                </div>

                <!-- Actual Slot -->
                <slot />
            </div>

        </main>

        <!-- Mobile Overlay -->
        <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm z-0 md:hidden"></div>
    </div>
</template>
